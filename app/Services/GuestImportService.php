<?php

namespace App\Services;

use App\Models\Invitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use SimpleXMLElement;
use ZipArchive;

/**
 * Imports guests from a CSV or Excel (.xlsx) file.
 *
 * Columns are matched by header (Nama, WhatsApp/HP/Telepon, Grup, Meja), or
 * taken in that order when the file has no header. Empty names and names
 * already on the list are skipped. Files are untrusted: sizes are capped and
 * the .xlsx reader only reads the first sheet and shared strings.
 */
class GuestImportService
{
    public const MAX_ROWS = 1000;

    private const MAX_XML_BYTES = 8 * 1024 * 1024;

    private const HEADERS = [
        'name' => ['nama', 'name', 'nama tamu', 'tamu'],
        'phone' => ['whatsapp', 'wa', 'no wa', 'no. wa', 'nomor wa', 'nomor whatsapp', 'no whatsapp', 'hp', 'no hp', 'no. hp', 'telepon', 'telp', 'phone', 'nomor'],
        'group' => ['grup', 'group', 'kelompok', 'kategori'],
        'table' => ['meja', 'no meja', 'nomor meja', 'table'],
    ];

    public function __construct(private readonly GuestService $guests) {}

    /**
     * @return array{added: int, empty: int, duplicates: int, over_quota: int}
     */
    public function import(Invitation $invitation, string $path, string $extension): array
    {
        $rows = strtolower($extension) === 'xlsx' ? $this->readXlsx($path) : $this->readCsv($path);
        if ($rows === []) {
            throw ValidationException::withMessages(['importFile' => 'File kosong atau formatnya tidak dikenali.']);
        }

        [$map, $rows] = $this->columns($rows);

        $existing = $invitation->guests()->pluck('name')->map(fn ($n) => mb_strtolower(trim($n)))->flip();
        $remaining = (int) config('shirobell.invitation.max_guests', 1000) - $invitation->guests()->count();
        $result = ['added' => 0, 'empty' => 0, 'duplicates' => 0, 'over_quota' => 0];
        $toCreate = [];

        foreach (array_slice($rows, 0, self::MAX_ROWS) as $row) {
            $name = mb_substr(trim((string) ($row[$map['name']] ?? '')), 0, 100);
            if ($name === '') {
                $result['empty']++;

                continue;
            }
            $key = mb_strtolower($name);
            if ($existing->has($key)) {
                $result['duplicates']++;

                continue;
            }
            if (count($toCreate) >= $remaining) {
                $result['over_quota']++;

                continue;
            }
            $existing[$key] = true;
            $toCreate[] = [
                'name' => $name,
                'phone' => $map['phone'] !== null ? $this->phone((string) ($row[$map['phone']] ?? '')) : null,
                'group_name' => $map['group'] !== null ? (mb_substr(trim((string) ($row[$map['group']] ?? '')), 0, 50) ?: null) : null,
                'table_number' => $map['table'] !== null ? (mb_substr(trim((string) ($row[$map['table']] ?? '')), 0, 20) ?: null) : null,
            ];
        }

        DB::transaction(function () use ($invitation, $toCreate) {
            foreach ($toCreate as $data) {
                $this->guests->create($invitation, $data);
            }
        });
        $result['added'] = count($toCreate);

        return $result;
    }

    /**
     * Header row decides the columns; without one: name, phone, group, table.
     *
     * @param  list<list<string>>  $rows
     * @return array{0: array{name: int, phone: ?int, group: ?int, table: ?int}, 1: list<list<string>>}
     */
    private function columns(array $rows): array
    {
        $header = array_map(fn ($c) => mb_strtolower(trim((string) $c)), $rows[0]);
        $map = ['name' => null, 'phone' => null, 'group' => null, 'table' => null];
        foreach ($header as $i => $cell) {
            foreach (self::HEADERS as $field => $aliases) {
                if ($map[$field] === null && in_array($cell, $aliases, true)) {
                    $map[$field] = $i;
                }
            }
        }

        if ($map['name'] !== null) {
            return [$map, array_slice($rows, 1)];
        }

        return [['name' => 0, 'phone' => 1, 'group' => 2, 'table' => 3], $rows];
    }

    /** @return list<list<string>> */
    private function readCsv(string $path): array
    {
        $content = (string) file_get_contents($path);
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        if (! mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
        }

        // Excel in Indonesian locale saves with ";"; pick the most common separator on the first line.
        $first = strtok($content, "\r\n") ?: '';
        $delimiter = collect([';' => substr_count($first, ';'), ',' => substr_count($first, ','), "\t" => substr_count($first, "\t")])
            ->sortDesc()->keys()->first();
        if (substr_count($first, $delimiter) === 0) {
            $delimiter = ',';
        }

        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $content);
        rewind($stream);
        $rows = [];
        while (($row = fgetcsv($stream, 0, $delimiter, '"', '')) !== false && count($rows) <= self::MAX_ROWS) {
            if ($row !== [null]) {
                $rows[] = array_map(fn ($c) => (string) $c, $row);
            }
        }
        fclose($stream);

        return $rows;
    }

    /** @return list<list<string>> first worksheet only */
    private function readXlsx(string $path): array
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw ValidationException::withMessages(['importFile' => 'File Excel tidak bisa dibaca.']);
        }

        try {
            $strings = [];
            if (($xml = $this->zipXml($zip, 'xl/sharedStrings.xml')) !== null) {
                foreach ($xml->si as $si) {
                    $strings[] = isset($si->t) ? (string) $si->t : implode('', array_map(fn ($r) => (string) $r->t, iterator_to_array($si->r, false)));
                }
            }

            $sheet = $this->zipXml($zip, 'xl/worksheets/sheet1.xml');
            if ($sheet === null) {
                throw ValidationException::withMessages(['importFile' => 'Lembar pertama file Excel tidak ditemukan.']);
            }

            $rows = [];
            foreach ($sheet->sheetData->row as $row) {
                $cells = [];
                foreach ($row->c as $c) {
                    $index = $this->columnIndex((string) $c['r']);
                    $type = (string) $c['t'];
                    $value = match ($type) {
                        's' => $strings[(int) $c->v] ?? '',
                        'inlineStr' => (string) $c->is->t,
                        default => (string) $c->v,
                    };
                    $cells[$index] = $value;
                }
                if ($cells !== []) {
                    $line = array_fill(0, max(array_keys($cells)) + 1, '');
                    foreach ($cells as $i => $v) {
                        $line[$i] = $v;
                    }
                    $rows[] = $line;
                }
                if (count($rows) > self::MAX_ROWS) {
                    break;
                }
            }

            return $rows;
        } finally {
            $zip->close();
        }
    }

    private function zipXml(ZipArchive $zip, string $name): ?SimpleXMLElement
    {
        $stat = $zip->statName($name);
        if ($stat === false) {
            return null;
        }
        if ($stat['size'] > self::MAX_XML_BYTES) {
            throw ValidationException::withMessages(['importFile' => 'File Excel terlalu besar. Maksimal '.self::MAX_ROWS.' tamu per file.']);
        }

        $xml = simplexml_load_string((string) $zip->getFromName($name), SimpleXMLElement::class, LIBXML_NONET);

        return $xml === false ? null : $xml;
    }

    /** Excel drops the leading zero of numbers typed as numbers (812... -> 0812...). */
    private function phone(string $value): ?string
    {
        $digits = $this->guests->normalizePhone($value);

        return $digits !== null && str_starts_with($digits, '8') && strlen($digits) >= 9 ? '0'.$digits : $digits;
    }

    /** "C12" -> 2 */
    private function columnIndex(string $reference): int
    {
        $letters = preg_replace('/[^A-Z]/', '', strtoupper($reference));
        $index = 0;
        foreach (str_split($letters ?: 'A') as $char) {
            $index = $index * 26 + (ord($char) - 64);
        }

        return $index - 1;
    }
}

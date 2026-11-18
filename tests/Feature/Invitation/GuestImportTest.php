<?php

namespace Tests\Feature\Invitation;

use App\Livewire\Invitation\GuestManager;
use App\Models\Invitation;
use App\Models\User;
use App\Services\GuestService;
use Database\Seeders\ThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;
use ZipArchive;

class GuestImportTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Invitation $invitation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThemeSeeder::class);
        $this->owner = User::factory()->create();
        $this->invitation = Invitation::factory()->for($this->owner)->create();
    }

    private function upload(UploadedFile $file)
    {
        return Livewire::actingAs($this->owner)
            ->test(GuestManager::class, ['invitation' => $this->invitation])
            ->set('importFile', $file)
            ->call('importGuests');
    }

    private function csv(string $content, string $name = 'tamu.csv'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, $content);
    }

    /** Minimal real .xlsx: shared strings plus one sheet. */
    private function xlsx(array $rows): UploadedFile
    {
        $strings = [];
        $sheetRows = '';
        foreach ($rows as $r => $row) {
            $cells = '';
            foreach ($row as $c => $value) {
                $ref = chr(65 + $c).($r + 1);
                if (is_int($value)) {
                    $cells .= "<c r=\"{$ref}\"><v>{$value}</v></c>";
                } else {
                    $strings[] = htmlspecialchars($value, ENT_XML1);
                    $cells .= "<c r=\"{$ref}\" t=\"s\"><v>".(count($strings) - 1).'</v></c>';
                }
            }
            $sheetRows .= '<row r="'.($r + 1)."\">{$cells}</row>";
        }
        $path = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new ZipArchive;
        $zip->open($path, ZipArchive::OVERWRITE);
        $zip->addFromString('xl/sharedStrings.xml', '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'.implode('', array_map(fn ($s) => "<si><t>{$s}</t></si>", $strings)).'</sst>');
        $zip->addFromString('xl/worksheets/sheet1.xml', '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'.$sheetRows.'</sheetData></worksheet>');
        $zip->close();

        return UploadedFile::fake()->createWithContent('tamu.xlsx', file_get_contents($path));
    }

    public function test_imports_semicolon_csv_with_header_from_indonesian_excel(): void
    {
        $this->upload($this->csv("\xEF\xBB\xBFNama;No WA;Grup;Meja\r\nBapak Joko;081234567890;Keluarga;1\r\nIbu Rina;;Kantor;5\r\n;;;\r\n"))
            ->assertHasNoErrors()
            ->assertSet('importResult.added', 2)
            ->assertSet('importResult.empty', 1)
            ->assertSee('2 tamu ditambahkan.');

        $joko = $this->invitation->guests()->firstWhere('name', 'Bapak Joko');
        $this->assertSame(['081234567890', 'Keluarga', '1'], [$joko->phone, $joko->group_name, $joko->table_number]);
        $this->assertNotEmpty($joko->code);
    }

    public function test_imports_comma_csv_without_header_in_column_order(): void
    {
        $this->upload($this->csv("Pak Budi,0811111111,Teman\nBu Sari,,\n"))->assertSet('importResult.added', 2);

        $this->assertSame('Teman', $this->invitation->guests()->firstWhere('name', 'Pak Budi')->group_name);
    }

    public function test_imports_excel_file_and_restores_leading_zero_of_phone_numbers(): void
    {
        $this->upload($this->xlsx([
            ['Meja', 'Nama Tamu', 'WhatsApp'],
            [3, 'Keluarga Hartono', 81298765432],
            [7, 'Om Andi', 'tidak ada'],
        ]))->assertHasNoErrors()->assertSet('importResult.added', 2);

        $hartono = $this->invitation->guests()->firstWhere('name', 'Keluarga Hartono');
        $this->assertSame(['081298765432', '3'], [$hartono->phone, $hartono->table_number]);
        $this->assertNull($this->invitation->guests()->firstWhere('name', 'Om Andi')->phone);
    }

    public function test_skips_names_already_on_the_list_and_respects_the_quota(): void
    {
        app(GuestService::class)->create($this->invitation, ['name' => 'Bapak Joko']);
        config(['shirobell.invitation.max_guests' => 3]);

        $this->upload($this->csv("Nama\nbapak joko\nTamu A\nTamu A\nTamu B\nTamu C\n"))
            ->assertSet('importResult.added', 2)
            ->assertSet('importResult.duplicates', 2)
            ->assertSet('importResult.over_quota', 1);

        $this->assertSame(3, $this->invitation->guests()->count());
    }

    public function test_rejects_other_file_types_and_foreign_invitations(): void
    {
        $this->upload(UploadedFile::fake()->create('tamu.pdf', 10, 'application/pdf'))->assertHasErrors('importFile');

        Livewire::actingAs(User::factory()->create())
            ->test(GuestManager::class, ['invitation' => $this->invitation])
            ->assertForbidden();
    }

    public function test_owner_downloads_xlsx_sample_with_text_formatted_phone_column(): void
    {
        $response = $this->actingAs($this->owner)->get(route('app.invitations.guests.template', $this->invitation));

        $response->assertOk()->assertDownload('contoh-daftar-tamu.xlsx');
        $path = $response->baseResponse->getFile()->getPathname();
        $zip = new ZipArchive;
        $this->assertTrue($zip->open($path));
        $this->assertStringContainsString('numFmtId="49"', $zip->getFromName('xl/styles.xml'));
        $this->assertStringContainsString('<col min="2" max="2" width="20" style="1"', $zip->getFromName('xl/worksheets/sheet1.xml'));
        $zip->close();

        // The sample itself imports cleanly, leading zero intact.
        $content = (string) file_get_contents($path);
        unlink($path); // deleteFileAfterSend never runs in tests
        $this->upload(UploadedFile::fake()->createWithContent('contoh-daftar-tamu.xlsx', $content))->assertHasNoErrors();
        $this->assertSame('081234567890', $this->invitation->guests()->where('name', 'Bapak Joko Susilo')->value('phone'));
        $this->assertSame('A3', $this->invitation->guests()->where('name', 'Keluarga Besar Hartono')->value('table_number'));

        $this->actingAs(User::factory()->create())->get(route('app.invitations.guests.template', $this->invitation))->assertForbidden();
    }

    public function test_phone_in_scientific_notation_is_recovered_or_reported(): void
    {
        $component = $this->upload($this->csv("Nama;WhatsApp;Meja\nAndi;6,28123E+11;\nBudi;6.28123456789E+11;2\nCitra;;\n"));

        $component->assertHasNoErrors()->assertSet('importResult.bad_phones', 1)->assertSee('nomor WhatsApp tidak terbaca');
        $guests = $this->invitation->guests()->pluck('phone', 'name');
        $this->assertNull($guests['Andi']);
        $this->assertSame('628123456789', $guests['Budi']);
        $this->assertNull($guests['Citra']);
        $this->assertNull($this->invitation->guests()->where('name', 'Andi')->value('table_number'));
    }

    public function test_manual_bulk_add_can_set_a_table_for_the_batch(): void
    {
        Livewire::actingAs($this->owner)
            ->test(GuestManager::class, ['invitation' => $this->invitation])
            ->set('bulk', "Andi\nBudi - 081234567890")
            ->set('bulkGroup', 'Keluarga')
            ->set('bulkTable', '7')
            ->call('addBulk')
            ->assertHasNoErrors()
            ->assertSet('bulkTable', '');

        $this->assertSame(['7', '7'], $this->invitation->guests()->orderBy('name')->pluck('table_number')->all());

        Livewire::actingAs($this->owner)
            ->test(GuestManager::class, ['invitation' => $this->invitation])
            ->set('bulk', 'Citra')
            ->call('addBulk');
        $this->assertNull($this->invitation->guests()->where('name', 'Citra')->value('table_number'));
    }

    public function test_successful_import_tells_the_page_to_reset_the_file_picker(): void
    {
        $this->upload($this->csv("Nama\nAndi\n"))->assertDispatched('guests-imported');
    }
}

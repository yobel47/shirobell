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

    public function test_owner_downloads_sample_file(): void
    {
        $response = $this->actingAs($this->owner)->get(route('app.invitations.guests.template', $this->invitation));

        $response->assertOk();
        $this->assertStringContainsString('Nama;WhatsApp;Grup;Meja', $response->streamedContent());
        $this->actingAs(User::factory()->create())->get(route('app.invitations.guests.template', $this->invitation))->assertForbidden();
    }
}

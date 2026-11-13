<?php

namespace App\Filament\Pages;

use App\Services\Admin\AdminActionService;
use App\Services\SettingService;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $title = 'Pengaturan';

    protected static ?string $slug = 'pengaturan';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.manage-settings';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(SettingService $settings): void
    {
        $this->form->fill(array_intersect_key(array_merge(SettingService::DEFAULTS, $settings->all()), SettingService::DEFAULTS));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->components([
            Section::make('Paket')->description('Berlaku untuk checkout berikutnya. Order yang sudah dibuat memakai harga saat dibuat.')
                ->columns(2)->schema([
                    TextInput::make('package_price')->label('Harga paket')->prefix('Rp')->integer()->required()->minValue(1000)->maxValue(100000000),
                    TextInput::make('package_duration_days')->label('Masa aktif')->suffix('hari')->integer()->required()->minValue(1)->maxValue(730),
                    TextInput::make('gallery_max_photos')->label('Maksimal foto galeri')->integer()->required()->minValue(1)->maxValue(100),
                ]),
            Section::make('Fitur Hari-H')->description('Aplikasi penerima tamu, tiket QR, cetak kartu QR, dan layar sapa.')
                ->columns(2)->schema([
                    Select::make('event_day_mode')->label('Cara penjualan')->required()->native(false)
                        ->options(['included' => 'Termasuk paket (gratis)', 'addon' => 'Add-on berbayar'])->live(),
                    TextInput::make('event_day_price')->label('Harga add-on')->prefix('Rp')->integer()->minValue(1000)->maxValue(100000000)
                        ->required(fn ($get) => $get('event_day_mode') === 'addon')
                        ->visible(fn ($get) => $get('event_day_mode') === 'addon'),
                ]),
            Section::make('Situs & kontak')->columns(2)->schema([
                TextInput::make('site_name')->label('Nama situs')->required()->maxLength(50),
                TextInput::make('support_whatsapp')->label('WhatsApp support')->tel()->maxLength(20)->regex('/^\+?[0-9]{8,15}$/'),
                TextInput::make('support_email')->label('Email support')->email()->maxLength(150),
                TextInput::make('support_instagram')->label('Instagram')->prefix('@')->maxLength(30)
                    ->regex('/^@?[A-Za-z0-9._]{1,30}$/')->helperText('Username saja, tanpa tautan.'),
            ]),
            Section::make('Identitas usaha')->description('Tampil di halaman Kontak, Syarat & Ketentuan, Kebijakan Privasi, dan Kebijakan Refund. Wajib diisi sebelum mengajukan Midtrans production.')
                ->columns(2)->schema([
                    TextInput::make('business_name')->label('Nama usaha / pemilik')->maxLength(150),
                    Textarea::make('business_address')->label('Alamat usaha')->rows(3)->maxLength(500)->columnSpanFull(),
                ]),
        ]);
    }

    public function save(AdminActionService $admin): void
    {
        $data = $this->form->getState();
        $admin->updateSettings(array_map(fn ($v) => $v ?? '', $data));

        Notification::make()->title('Pengaturan tersimpan')->success()->send();
    }
}

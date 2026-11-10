<?php

namespace App\Filament\Pages;

use App\Services\Admin\AdminActionService;
use App\Services\SettingService;
use BackedEnum;
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
            Section::make('Situs & kontak')->columns(2)->schema([
                TextInput::make('site_name')->label('Nama situs')->required()->maxLength(50),
                TextInput::make('support_whatsapp')->label('WhatsApp support')->tel()->maxLength(20)->regex('/^\+?[0-9]{8,15}$/'),
                TextInput::make('support_email')->label('Email support')->email()->maxLength(150),
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

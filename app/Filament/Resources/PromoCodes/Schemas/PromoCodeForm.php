<?php

namespace App\Filament\Resources\PromoCodes\Schemas;

use App\Models\PromoCode;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PromoCodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Kode promo')->columns(2)->schema([
                TextInput::make('code')->label('Kode')
                    ->required()->maxLength(30)
                    ->regex('/^[A-Za-z0-9_-]+$/')
                    ->unique(ignoreRecord: true)
                    ->helperText('Huruf, angka, - dan _. Otomatis huruf besar.'),
                Toggle::make('is_active')->label('Aktif')->default(true)->inline(false),
                Select::make('type')->label('Jenis')->options(PromoCode::TYPES)->required()->live()->default('percent'),
                TextInput::make('value')->label('Nilai')->required()->integer()->minValue(1)
                    ->maxValue(fn (Get $get) => $get('type') === 'percent' ? 100 : 100000000)
                    ->prefix(fn (Get $get) => $get('type') === 'percent' ? null : 'Rp')
                    ->suffix(fn (Get $get) => $get('type') === 'percent' ? '%' : null),
            ]),
            Section::make('Batasan')->columns(3)->schema([
                TextInput::make('max_uses')->label('Kuota pemakaian')->integer()->minValue(1)->helperText('Kosongkan untuk tanpa batas.'),
                DateTimePicker::make('starts_at')->label('Mulai berlaku')->seconds(false),
                DateTimePicker::make('ends_at')->label('Berakhir')->seconds(false)->after('starts_at'),
            ]),
        ]);
    }
}

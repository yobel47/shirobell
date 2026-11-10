<?php

namespace App\Filament\Resources\PromoCodes\Tables;

use App\Models\PromoCode;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PromoCodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('code')->label('Kode')->searchable()->copyable()->weight('bold'),
                TextColumn::make('value')->label('Diskon')->state(fn (PromoCode $record) => $record->label()),
                TextColumn::make('used_count')->label('Terpakai')
                    ->state(fn (PromoCode $record) => $record->used_count.' / '.($record->max_uses ?? 'tak terbatas')),
                TextColumn::make('starts_at')->label('Mulai')->dateTime('d M Y H:i')->placeholder('-')->sortable(),
                TextColumn::make('ends_at')->label('Berakhir')->dateTime('d M Y H:i')->placeholder('-')->sortable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Aktif'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}

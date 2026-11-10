<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Models\PaymentLog;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    protected static ?string $title = 'Log pembayaran';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i:s'),
                TextColumn::make('event')->label('Event')->placeholder('-'),
                IconColumn::make('is_processed')->label('Diproses')->boolean(),
                TextColumn::make('error')->label('Error')->placeholder('-')->color('danger'),
            ])
            ->recordActions([
                ViewAction::make()->schema([
                    TextEntry::make('payload')->label('Payload')
                        ->state(fn (PaymentLog $record) => json_encode($record->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))
                        ->fontFamily('mono')->extraAttributes(['class' => 'whitespace-pre-wrap break-all']),
                ]),
            ]);
    }
}

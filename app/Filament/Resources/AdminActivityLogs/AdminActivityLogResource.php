<?php

namespace App\Filament\Resources\AdminActivityLogs;

use App\Filament\Resources\AdminActivityLogs\Pages\ListAdminActivityLogs;
use App\Models\AdminActivityLog;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/** Read-only audit trail. */
class AdminActivityLogResource extends Resource
{
    protected static ?string $model = AdminActivityLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?string $modelLabel = 'aktivitas';

    protected static ?string $pluralModelLabel = 'Audit log';

    protected static ?int $navigationSort = 9;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with('admin:id,name'))
            ->columns([
                TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i:s')->sortable(),
                TextColumn::make('admin.name')->label('Admin')->placeholder('Sistem'),
                TextColumn::make('action')->label('Aksi')->badge()->searchable(),
                TextColumn::make('subject')->label('Objek')
                    ->state(fn (AdminActivityLog $r) => $r->subject_type ? class_basename($r->subject_type).' #'.$r->subject_id : '-'),
            ])
            ->filters([
                SelectFilter::make('action')->options(fn () => AdminActivityLog::query()->distinct()->orderBy('action')->pluck('action', 'action')->all()),
            ])
            ->recordActions([
                ViewAction::make()->schema([
                    TextEntry::make('meta')->label('Detail')
                        ->state(fn (AdminActivityLog $record) => json_encode($record->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '-')
                        ->fontFamily('mono')->extraAttributes(['class' => 'whitespace-pre-wrap break-all']),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListAdminActivityLogs::route('/')];
    }
}

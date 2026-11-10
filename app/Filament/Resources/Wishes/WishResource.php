<?php

namespace App\Filament\Resources\Wishes;

use App\Enums\Attendance;
use App\Filament\Resources\Wishes\Pages\ListWishes;
use App\Models\Wish;
use App\Services\Admin\AdminActionService;
use App\Services\Admin\AuditLogger;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class WishResource extends Resource
{
    protected static ?string $model = Wish::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Undangan';

    protected static ?string $modelLabel = 'ucapan';

    protected static ?string $pluralModelLabel = 'Ucapan & RSVP';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with('invitation:id,slug'))
            ->columns([
                TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('invitation.slug')->label('Undangan')->searchable(),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('attendance')->label('RSVP')->badge()->placeholder('-')
                    ->formatStateUsing(fn (?Attendance $state) => $state?->label()),
                TextColumn::make('message')->label('Ucapan')->limit(80)->wrap()->searchable()->placeholder('-'),
                IconColumn::make('is_hidden')->label('Disembunyikan')->boolean()->trueColor('danger')->falseColor('gray'),
            ])
            ->filters([
                TernaryFilter::make('is_hidden')->label('Disembunyikan'),
                SelectFilter::make('attendance')->label('RSVP')->options(collect(Attendance::cases())->mapWithKeys(fn ($a) => [$a->value => $a->label()])),
            ])
            ->recordActions([
                Action::make('toggle')->label(fn (Wish $r) => $r->is_hidden ? 'Tampilkan' : 'Sembunyikan')
                    ->icon(fn (Wish $r) => $r->is_hidden ? Heroicon::OutlinedEye : Heroicon::OutlinedEyeSlash)
                    ->action(fn (Wish $record, AdminActionService $admin) => $admin->setWishHidden($record, ! $record->is_hidden)),
                DeleteAction::make()->after(fn (Wish $record) => app(AuditLogger::class)->log('wish.delete', null, [
                    'wish_id' => $record->id, 'invitation_id' => $record->invitation_id, 'name' => $record->name,
                ])),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListWishes::route('/')];
    }
}

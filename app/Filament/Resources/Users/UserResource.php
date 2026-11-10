<?php

namespace App\Filament\Resources\Users;

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Models\User;
use App\Services\Admin\AdminActionService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Penjualan';

    protected static ?string $modelLabel = 'pelanggan';

    protected static ?string $pluralModelLabel = 'Pelanggan';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withCount('invitations')
                ->withSum(['orders as paid_total' => fn ($q) => $q->where('status', 'paid')], 'total_amount'))
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('email')->searchable()->copyable(),
                TextColumn::make('phone')->label('No. WA')->placeholder('-')->toggleable(),
                TextColumn::make('role')->badge()->formatStateUsing(fn (UserRole $state) => $state->label())
                    ->color(fn (UserRole $state) => $state === UserRole::Admin ? 'primary' : 'gray'),
                TextColumn::make('invitations_count')->label('Undangan')->sortable(),
                TextColumn::make('paid_total')->label('Total belanja')->money('IDR', locale: 'id')->placeholder('Rp 0')->sortable(),
                IconColumn::make('email_verified_at')->label('Terverifikasi')->boolean()->state(fn (User $r) => $r->email_verified_at !== null),
                IconColumn::make('is_blocked')->label('Diblokir')->boolean()->trueColor('danger')->falseColor('gray'),
                TextColumn::make('created_at')->label('Daftar')->dateTime('d M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')->options(collect(UserRole::cases())->mapWithKeys(fn ($r) => [$r->value => $r->label()])),
                TernaryFilter::make('is_blocked')->label('Diblokir'),
            ])
            ->recordActions([ViewAction::make()]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Akun')->columns(3)->schema([
                TextEntry::make('name')->label('Nama'),
                TextEntry::make('email')->copyable(),
                TextEntry::make('phone')->label('No. WA')->placeholder('-'),
                TextEntry::make('role')->formatStateUsing(fn (UserRole $state) => $state->label()),
                TextEntry::make('email_verified_at')->label('Email terverifikasi')->dateTime('d M Y H:i')->placeholder('Belum'),
                TextEntry::make('created_at')->label('Daftar')->dateTime('d M Y H:i'),
            ]),
            Section::make('Undangan')->schema([
                RepeatableEntry::make('invitations')->label('')->columns(4)->schema([
                    TextEntry::make('slug')->label('Alamat'),
                    TextEntry::make('status')->formatStateUsing(fn ($state) => $state->label()),
                    TextEntry::make('active_until')->label('Aktif sampai')->dateTime('d M Y')->placeholder('-'),
                    TextEntry::make('created_at')->label('Dibuat')->dateTime('d M Y'),
                ])->placeholder('Belum ada undangan'),
            ]),
        ]);
    }

    public static function adminActions(): array
    {
        return [
            Action::make('block')->label('Blokir akun')->icon(Heroicon::OutlinedLockClosed)->color('danger')
                ->visible(fn (User $record) => ! $record->is_blocked && ! $record->is(auth()->user()))
                ->requiresConfirmation()
                ->modalDescription('Pengguna langsung keluar dan tidak bisa masuk lagi. Undangan aktif tetap tampil; tangguhkan undangan secara terpisah jika perlu.')
                ->action(function (User $record, AdminActionService $admin) {
                    $admin->setUserBlocked($record, true);
                    Notification::make()->title('Akun diblokir')->success()->send();
                }),
            Action::make('unblock')->label('Buka blokir')->icon(Heroicon::OutlinedLockOpen)->color('success')
                ->visible(fn (User $record) => $record->is_blocked)
                ->requiresConfirmation()
                ->action(function (User $record, AdminActionService $admin) {
                    $admin->setUserBlocked($record, false);
                    Notification::make()->title('Blokir dibuka')->success()->send();
                }),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'view' => ViewUser::route('/{record}'),
        ];
    }
}

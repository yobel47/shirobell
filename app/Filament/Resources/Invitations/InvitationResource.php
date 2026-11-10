<?php

namespace App\Filament\Resources\Invitations;

use App\Enums\InvitationStatus;
use App\Filament\Resources\Invitations\Pages\ListInvitations;
use App\Filament\Resources\Invitations\Pages\ViewInvitation;
use App\Models\Invitation;
use App\Services\Admin\AdminActionService;
use App\Services\SlugService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class InvitationResource extends Resource
{
    protected static ?string $model = Invitation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelopeOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Undangan';

    protected static ?string $modelLabel = 'undangan';

    protected static ?string $pluralModelLabel = 'Undangan';

    protected static ?int $navigationSort = 1;

    /**
     * The model policy guards the customer area (owner only). Panel access is
     * already limited to admins by User::canAccessPanel().
     */
    protected static bool $shouldSkipAuthorization = true;

    protected static ?string $recordTitleAttribute = 'slug';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['user:id,name,email', 'theme:id,name'])->withCount(['guests', 'wishes']))
            ->columns([
                TextColumn::make('slug')->label('Alamat')->searchable()->copyable()
                    ->url(fn (Invitation $r) => $r->isActive() ? $r->publicUrl() : null, shouldOpenInNewTab: true),
                TextColumn::make('couple')->label('Pasangan')->state(fn (Invitation $r) => $r->coupleName())
                    ->searchable(query: fn (Builder $query, string $search) => $query->where(fn ($q) => $q
                        ->where('groom_nickname', 'like', "%{$search}%")->orWhere('bride_nickname', 'like', "%{$search}%"))),
                TextColumn::make('user.email')->label('Pemilik')->searchable(),
                TextColumn::make('theme.name')->label('Tema')->toggleable(),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn (InvitationStatus $state) => $state->label())
                    ->color(fn (InvitationStatus $state) => self::statusColor($state)),
                TextColumn::make('active_until')->label('Aktif sampai')->dateTime('d M Y')->placeholder('-')->sortable(),
                TextColumn::make('guests_count')->label('Tamu')->sortable()->toggleable(),
                TextColumn::make('wishes_count')->label('RSVP/ucapan')->sortable()->toggleable(),
                TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y')->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(collect(InvitationStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),
                TrashedFilter::make()->label('Dihapus customer'),
            ])
            ->recordActions([ViewAction::make()]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Undangan')->columns(3)->schema([
                TextEntry::make('slug')->label('Alamat')->copyable(),
                TextEntry::make('status')->badge()->formatStateUsing(fn (InvitationStatus $state) => $state->label())->color(fn (InvitationStatus $state) => self::statusColor($state)),
                TextEntry::make('theme.name')->label('Tema'),
                TextEntry::make('couple')->label('Pasangan')->state(fn (Invitation $r) => $r->coupleName()),
                TextEntry::make('user.name')->label('Pemilik'),
                TextEntry::make('user.email')->label('Email pemilik')->copyable(),
                TextEntry::make('published_at')->label('Pertama terbit')->dateTime('d M Y H:i')->placeholder('-'),
                TextEntry::make('active_until')->label('Aktif sampai')->dateTime('d M Y H:i')->placeholder('-'),
                TextEntry::make('suspended_reason')->label('Alasan ditangguhkan')->placeholder('-'),
                TextEntry::make('deleted_at')->label('Dihapus customer')->dateTime('d M Y H:i')->placeholder('-'),
                TextEntry::make('created_at')->label('Dibuat')->dateTime('d M Y H:i'),
            ]),
        ]);
    }

    public static function adminActions(): array
    {
        return [
            Action::make('preview')->label('Buka undangan')->icon(Heroicon::OutlinedArrowTopRightOnSquare)->color('gray')
                ->url(fn (Invitation $record) => $record->publicUrl(), shouldOpenInNewTab: true)
                ->visible(fn (Invitation $record) => $record->isActive()),
            Action::make('editContent')->label('Edit isi')->icon(Heroicon::OutlinedPencil)
                ->visible(fn (Invitation $record) => ! $record->trashed())
                ->url(fn (Invitation $record) => route('app.invitations.edit', $record)),
            Action::make('extend')->label('Perpanjang / aktifkan manual')->icon(Heroicon::OutlinedCalendarDays)
                ->visible(fn (Invitation $record) => ! $record->trashed())
                ->schema([
                    TextInput::make('days')->label('Tambah hari')->integer()->required()->minValue(1)->maxValue(730)->default(30),
                    Textarea::make('reason')->label('Alasan')->required()->maxLength(255),
                ])
                ->action(function (Invitation $record, array $data, AdminActionService $admin) {
                    $admin->extendInvitation($record, (int) $data['days'], $data['reason']);
                    Notification::make()->title('Masa aktif diperbarui')->success()->send();
                }),
            Action::make('changeSlug')->label('Ubah alamat')->icon(Heroicon::OutlinedPencilSquare)->color('gray')
                ->visible(fn (Invitation $record) => ! $record->trashed())
                ->fillForm(fn (Invitation $record) => ['slug' => $record->slug])
                ->schema(fn (Invitation $record) => [
                    TextInput::make('slug')->label('Alamat baru')->required()->rules(app(SlugService::class)->rules($record))
                        ->suffix('.'.config('app.domain')),
                ])
                ->action(function (Invitation $record, array $data, AdminActionService $admin) {
                    $admin->changeSlug($record, strtolower(trim($data['slug'])));
                    Notification::make()->title('Alamat diubah')->success()->send();
                }),
            Action::make('suspend')->label('Tangguhkan')->icon(Heroicon::OutlinedNoSymbol)->color('danger')
                ->visible(fn (Invitation $record) => $record->status !== InvitationStatus::Suspended && ! $record->trashed())
                ->requiresConfirmation()
                ->modalDescription('Undangan tidak bisa dibuka tamu dan pemilik tidak bisa mengedit sampai diaktifkan kembali.')
                ->schema([Textarea::make('reason')->label('Alasan (ditampilkan ke pemilik)')->required()->maxLength(255)])
                ->action(function (Invitation $record, array $data, AdminActionService $admin) {
                    $admin->suspendInvitation($record, $data['reason']);
                    Notification::make()->title('Undangan ditangguhkan')->success()->send();
                }),
            Action::make('unsuspend')->label('Cabut penangguhan')->icon(Heroicon::OutlinedCheckCircle)->color('success')
                ->visible(fn (Invitation $record) => $record->status === InvitationStatus::Suspended)
                ->requiresConfirmation()
                ->action(function (Invitation $record, AdminActionService $admin) {
                    $admin->unsuspendInvitation($record);
                    Notification::make()->title('Penangguhan dicabut')->success()->send();
                }),
        ];
    }

    public static function statusColor(InvitationStatus $status): string
    {
        return match ($status) {
            InvitationStatus::Active => 'success',
            InvitationStatus::Draft => 'warning',
            InvitationStatus::Expired => 'gray',
            InvitationStatus::Suspended => 'danger',
        };
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvitations::route('/'),
            'view' => ViewInvitation::route('/{record}'),
        ];
    }
}

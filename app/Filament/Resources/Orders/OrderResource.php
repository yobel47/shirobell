<?php

namespace App\Filament\Resources\Orders;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Filament\Resources\Orders\RelationManagers\LogsRelationManager;
use App\Models\Order;
use App\Services\Admin\AdminActionService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Throwable;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Penjualan';

    protected static ?string $modelLabel = 'transaksi';

    protected static ?string $pluralModelLabel = 'Transaksi';

    protected static ?int $navigationSort = 1;

    /**
     * The model policy guards the customer area (owner only). Panel access is
     * already limited to admins by User::canAccessPanel().
     */
    protected static bool $shouldSkipAuthorization = true;

    protected static ?string $recordTitleAttribute = 'number';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['user:id,name,email', 'invitation:id,slug,groom_nickname,bride_nickname,deleted_at']))
            ->columns([
                TextColumn::make('number')->label('Nomor')->searchable()->copyable()->fontFamily('mono'),
                TextColumn::make('user.name')->label('Pelanggan')->description(fn (Order $r) => $r->user?->email)->searchable(),
                TextColumn::make('invitation.slug')->label('Undangan')->searchable(),
                TextColumn::make('type')->label('Jenis')->formatStateUsing(fn (string $state) => Order::TYPES[$state] ?? $state),
                TextColumn::make('total_amount')->label('Total')->money('IDR', locale: 'id')->sortable(),
                TextColumn::make('status')->label('Status')->badge()
                    ->formatStateUsing(fn (OrderStatus $state) => $state->label())
                    ->color(fn (OrderStatus $state) => self::statusColor($state)),
                TextColumn::make('payment_method')->label('Metode')->placeholder('-')->toggleable(),
                TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('paid_at')->label('Dibayar')->dateTime('d M Y H:i')->placeholder('-')->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(collect(OrderStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),
                Filter::make('created')->schema([
                    DatePicker::make('from')->label('Dari'),
                    DatePicker::make('until')->label('Sampai'),
                ])->query(fn (Builder $query, array $data) => $query
                    ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                    ->when($data['until'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '<=', $d))),
            ])
            ->recordActions([ViewAction::make()]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order')->columns(3)->schema([
                TextEntry::make('number')->label('Nomor')->copyable(),
                TextEntry::make('status')->badge()->formatStateUsing(fn (OrderStatus $state) => $state->label())->color(fn (OrderStatus $state) => self::statusColor($state)),
                TextEntry::make('type')->label('Jenis')->formatStateUsing(fn (string $state) => Order::TYPES[$state] ?? $state),
                TextEntry::make('user.name')->label('Pelanggan'),
                TextEntry::make('user.email')->label('Email'),
                TextEntry::make('invitation.slug')->label('Undangan'),
                TextEntry::make('base_amount')->label('Harga')->money('IDR', locale: 'id'),
                TextEntry::make('discount_amount')->label('Diskon')->money('IDR', locale: 'id'),
                TextEntry::make('total_amount')->label('Total')->money('IDR', locale: 'id'),
                TextEntry::make('promoCode.code')->label('Kode promo')->placeholder('-'),
                TextEntry::make('duration_days')->label('Masa aktif')->suffix(' hari'),
                TextEntry::make('uuid')->label('Order ID Midtrans')->copyable(),
                TextEntry::make('payment_method')->label('Metode')->placeholder('-'),
                TextEntry::make('payment_channel')->label('Channel')->placeholder('-'),
                TextEntry::make('gateway_transaction_id')->label('Transaction ID')->placeholder('-'),
                TextEntry::make('created_at')->label('Dibuat')->dateTime('d M Y H:i'),
                TextEntry::make('expires_at')->label('Kedaluwarsa')->dateTime('d M Y H:i')->placeholder('-'),
                TextEntry::make('paid_at')->label('Dibayar')->dateTime('d M Y H:i')->placeholder('-'),
            ]),
        ]);
    }

    /** Header actions used on the view page. */
    public static function adminActions(): array
    {
        return [
            Action::make('sync')->label('Cek status Midtrans')->icon(Heroicon::OutlinedArrowPath)
                ->visible(fn (Order $record) => $record->status !== OrderStatus::Paid && $record->payment_url)
                ->action(function (Order $record, AdminActionService $admin) {
                    try {
                        $order = $admin->syncOrder($record);
                        Notification::make()->title('Status: '.$order->status->label())->success()->send();
                    } catch (Throwable $e) {
                        Notification::make()->title('Gagal menghubungi Midtrans')->body($e->getMessage())->danger()->send();
                    }
                }),
            Action::make('markPaid')->label('Tandai lunas manual')->icon(Heroicon::OutlinedCheckBadge)->color('warning')
                ->visible(fn (Order $record) => $record->status !== OrderStatus::Paid)
                ->requiresConfirmation()
                ->modalDescription('Hanya jika pembayaran sudah dipastikan masuk (cek dashboard Midtrans). Undangan akan langsung aktif dan tindakan ini tercatat di audit log.')
                ->schema([Textarea::make('reason')->label('Alasan')->required()->maxLength(255)])
                ->action(function (Order $record, array $data, AdminActionService $admin) {
                    $admin->markOrderPaid($record, $data['reason']);
                    Notification::make()->title('Order ditandai lunas, undangan aktif')->success()->send();
                }),
        ];
    }

    public static function statusColor(OrderStatus $status): string
    {
        return match ($status) {
            OrderStatus::Paid => 'success',
            OrderStatus::Pending => 'warning',
            OrderStatus::Expired => 'gray',
            OrderStatus::Failed => 'danger',
        };
    }

    public static function getRelations(): array
    {
        return [LogsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'view' => ViewOrder::route('/{record}'),
        ];
    }
}

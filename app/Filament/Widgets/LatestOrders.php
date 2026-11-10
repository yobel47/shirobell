<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestOrders extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Transaksi terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->with(['user:id,name', 'invitation:id,slug,deleted_at'])->latest()->limit(10))
            ->paginated(false)
            ->recordUrl(fn (Order $record) => OrderResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('number')->label('Nomor')->fontFamily('mono'),
                TextColumn::make('user.name')->label('Pelanggan'),
                TextColumn::make('invitation.slug')->label('Undangan'),
                TextColumn::make('total_amount')->label('Total')->money('IDR', locale: 'id'),
                TextColumn::make('status')->badge()->formatStateUsing(fn (OrderStatus $state) => $state->label())
                    ->color(fn (OrderStatus $state) => OrderResource::statusColor($state)),
                TextColumn::make('created_at')->label('Dibuat')->since(),
            ]);
    }
}

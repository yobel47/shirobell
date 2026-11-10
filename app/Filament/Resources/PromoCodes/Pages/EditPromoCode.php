<?php

namespace App\Filament\Resources\PromoCodes\Pages;

use App\Filament\Resources\PromoCodes\PromoCodeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPromoCode extends EditRecord
{
    protected static string $resource = PromoCodeResource::class;

    protected function getHeaderActions(): array
    {
        // Codes already used on orders are kept for history; deactivate them instead.
        return [
            DeleteAction::make()->visible(fn () => $this->record->orders()->doesntExist()),
        ];
    }
}

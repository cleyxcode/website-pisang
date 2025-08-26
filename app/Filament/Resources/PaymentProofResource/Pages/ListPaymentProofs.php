<?php

namespace App\Filament\Resources\PaymentProofResource\Pages;

use App\Filament\Resources\PaymentProofResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentProofs extends ListRecords
{
    protected static string $resource = PaymentProofResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

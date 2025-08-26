<?php

namespace App\Filament\Resources\PaymentProofResource\Pages;

use App\Filament\Resources\PaymentProofResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentProof extends EditRecord
{
    protected static string $resource = PaymentProofResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

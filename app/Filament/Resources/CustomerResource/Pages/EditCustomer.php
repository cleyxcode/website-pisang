<?php
// app/Filament/Resources/CustomerResource/Pages/EditCustomer.php
namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            
            Actions\Action::make('toggle_status')
                ->label(fn () => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn () => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn () => $this->record->is_active ? 'danger' : 'success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['is_active' => !$this->record->is_active]);
                    $this->refreshFormData(['is_active']);
                })
                ->successNotificationTitle('Customer status updated'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Customer updated successfully';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Remove password from form data when editing
        unset($data['password']);
        return $data;
    }
}
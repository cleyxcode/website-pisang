<?php
// app/Filament/Resources/CustomerResource/Pages/ViewCustomer.php
namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            
            Actions\Action::make('toggle_status')
                ->label(fn () => $this->record->is_active ? 'Deactivate' : 'Activate')
                ->icon(fn () => $this->record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn () => $this->record->is_active ? 'danger' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn () => ($this->record->is_active ? 'Deactivate' : 'Activate') . ' Customer')
                ->modalDescription(fn () => 'Are you sure you want to ' . ($this->record->is_active ? 'deactivate' : 'activate') . ' this customer?')
                ->action(function () {
                    $this->record->update(['is_active' => !$this->record->is_active]);
                })
                ->successNotificationTitle('Customer status updated')
                ->after(fn () => $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]))),
        ];
    }
}
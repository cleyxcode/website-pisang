<?php

namespace App\Filament\Resources\StoreSettingsResource\Pages;

use App\Filament\Resources\StoreSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStoreSettings extends ManageRecords
{
    protected static string $resource = StoreSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => $this->getTableRecords()->count() === 0),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\StoreLocationMap::class,
        ];
    }
}
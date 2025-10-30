<?php

namespace App\Filament\Widgets;

use App\Models\StoreSettings;
use Webbingbrasil\FilamentMaps\Actions;
use Webbingbrasil\FilamentMaps\Marker;
use Webbingbrasil\FilamentMaps\Widgets\MapWidget;

class StoreLocationMap extends MapWidget
{
    protected int | string | array $columnSpan = 'full';
    protected bool $hasBorder = true;
    protected string $height = '450px';
    protected bool $rounded = true;

    public function getMarkers(): array
    {
        $store = StoreSettings::current();
        
        if (!$store->latitude || !$store->longitude) {
            return [];
        }

        return [
            Marker::make('store-location')
                ->lat($store->latitude)
                ->lng($store->longitude)
                ->popup('<strong>' . $store->store_name . '</strong><br>Lokasi Toko Kami')
                ->color(Marker::COLOR_RED),
        ];
    }

    public function getActions(): array
    {
        $store = StoreSettings::current();
        
        return [
            Actions\ZoomAction::make(),
            Actions\CenterMapAction::make()
                ->centerTo([$store->latitude ?? -3.65841000, $store->longitude ?? 128.28989000])
                ->zoom(15),
            Actions\FullscreenAction::make(),
        ];
    }

    // 🔧 Ubah dari protected → public
    public function getMapOptions(): array
    {
        $store = StoreSettings::current();
        
        return [
            'center' => [$store->latitude ?? -3.65841000, $store->longitude ?? 128.28989000],
            'zoom' => 15,
        ];
    }

    protected function getData(): array
    {
        return [];
    }
}

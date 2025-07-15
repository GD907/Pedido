<?php

namespace App\Filament\Resources\RopaResource\Pages;

use App\Filament\Resources\RopaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRopas extends ListRecords
{
    protected static string $resource = RopaResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Venta de Ropa'),
        ];
    }
}

<?php

namespace App\Filament\Resources\ProductoResource\Pages;

use App\Filament\Resources\ProductoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProducto extends ViewRecord
{
    protected static string $resource = ProductoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

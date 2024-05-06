<?php

namespace App\Filament\Resources\CategoriaProductoResource\Pages;

use App\Filament\Resources\CategoriaProductoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCategoriaProducto extends ViewRecord
{
    protected static string $resource = CategoriaProductoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

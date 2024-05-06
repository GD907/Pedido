<?php

namespace App\Filament\Resources\CategoriaProductoResource\Pages;

use App\Filament\Resources\CategoriaProductoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoriaProducto extends EditRecord
{
    protected static string $resource = CategoriaProductoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}

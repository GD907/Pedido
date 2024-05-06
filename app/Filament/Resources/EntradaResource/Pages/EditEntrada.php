<?php

namespace App\Filament\Resources\EntradaResource\Pages;

use App\Filament\Resources\EntradaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEntrada extends EditRecord
{
    protected static string $resource = EntradaResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

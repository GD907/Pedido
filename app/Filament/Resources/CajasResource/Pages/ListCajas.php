<?php

namespace App\Filament\Resources\CajasResource\Pages;

use App\Filament\Resources\CajasResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCajas extends ListRecords
{
    protected static string $resource = CajasResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

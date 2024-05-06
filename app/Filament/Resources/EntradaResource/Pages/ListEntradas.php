<?php

namespace App\Filament\Resources\EntradaResource\Pages;

use App\Filament\Resources\EntradaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEntradas extends ListRecords
{
    protected static string $resource = EntradaResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

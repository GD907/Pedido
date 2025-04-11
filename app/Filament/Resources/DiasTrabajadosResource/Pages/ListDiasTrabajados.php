<?php

namespace App\Filament\Resources\DiasTrabajadosResource\Pages;

use App\Filament\Resources\DiasTrabajadosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDiasTrabajados extends ListRecords
{
    protected static string $resource = DiasTrabajadosResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Día de Trabajo de Empleado'),
        ];
    }
}

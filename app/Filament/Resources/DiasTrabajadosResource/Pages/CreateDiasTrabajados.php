<?php

namespace App\Filament\Resources\DiasTrabajadosResource\Pages;

use App\Filament\Resources\DiasTrabajadosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDiasTrabajados extends CreateRecord
{
    protected static string $resource = DiasTrabajadosResource::class;

    public function getTitle(): string
    {
        return 'Registrar Día de Trabajo de Empleado';
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

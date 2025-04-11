<?php

namespace App\Filament\Resources\DiasTrabajadosResource\Pages;

use App\Filament\Resources\DiasTrabajadosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDiasTrabajados extends ViewRecord
{
    protected static string $resource = DiasTrabajadosResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

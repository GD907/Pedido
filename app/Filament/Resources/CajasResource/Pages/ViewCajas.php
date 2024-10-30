<?php

namespace App\Filament\Resources\CajasResource\Pages;

use App\Filament\Resources\CajasResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCajas extends ViewRecord
{
    protected static string $resource = CajasResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\CalendarioChequesResource\Pages;

use App\Filament\Resources\CalendarioChequesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCalendarioCheques extends ViewRecord
{
    protected static string $resource = CalendarioChequesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\CalendarioChequesResource\Pages;

use App\Filament\Resources\CalendarioChequesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCalendarioCheques extends ListRecords
{
    protected static string $resource = CalendarioChequesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Registrar Cheque'),
        ];
    }

}

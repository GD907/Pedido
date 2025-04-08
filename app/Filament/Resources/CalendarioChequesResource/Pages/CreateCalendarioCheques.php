<?php

namespace App\Filament\Resources\CalendarioChequesResource\Pages;

use App\Filament\Resources\CalendarioChequesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCalendarioCheques extends CreateRecord
{

    protected static string $resource = CalendarioChequesResource::class;
    public function getTitle(): string
    {
        return 'Registrar Cheque';
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

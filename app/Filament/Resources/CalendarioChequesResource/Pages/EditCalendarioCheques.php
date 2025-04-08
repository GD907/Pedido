<?php

namespace App\Filament\Resources\CalendarioChequesResource\Pages;

use App\Filament\Resources\CalendarioChequesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCalendarioCheques extends EditRecord
{
    protected static string $resource = CalendarioChequesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

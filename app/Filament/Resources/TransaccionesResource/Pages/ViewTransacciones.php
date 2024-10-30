<?php

namespace App\Filament\Resources\TransaccionesResource\Pages;

use App\Filament\Resources\TransaccionesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTransacciones extends ViewRecord
{
    protected static string $resource = TransaccionesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

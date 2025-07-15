<?php

namespace App\Filament\Resources\RepartosResource\Pages;

use App\Filament\Resources\RepartosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRepartos extends ViewRecord
{
    protected static string $resource = RepartosResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\RopaResource\Pages;

use App\Filament\Resources\RopaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRopa extends ViewRecord
{
    protected static string $resource = RopaResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

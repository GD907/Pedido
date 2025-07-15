<?php

namespace App\Filament\Resources\CierreDiaResource\Pages;

use App\Filament\Resources\CierreDiaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCierreDia extends ViewRecord
{
    protected static string $resource = CierreDiaResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

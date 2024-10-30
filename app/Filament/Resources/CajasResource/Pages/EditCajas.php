<?php

namespace App\Filament\Resources\CajasResource\Pages;

use App\Filament\Resources\CajasResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCajas extends EditRecord
{
    protected static string $resource = CajasResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}

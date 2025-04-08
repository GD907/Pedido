<?php

namespace App\Filament\Resources\RopaResource\Pages;

use App\Filament\Resources\RopaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRopa extends EditRecord
{
    protected static string $resource = RopaResource::class;

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

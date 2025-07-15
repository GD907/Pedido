<?php

namespace App\Filament\Resources\RepartosResource\Pages;

use App\Filament\Resources\RepartosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRepartos extends EditRecord
{
    protected static string $resource = RepartosResource::class;

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

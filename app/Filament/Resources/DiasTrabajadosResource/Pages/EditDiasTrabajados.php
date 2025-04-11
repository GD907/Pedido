<?php

namespace App\Filament\Resources\DiasTrabajadosResource\Pages;

use App\Filament\Resources\DiasTrabajadosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDiasTrabajados extends EditRecord
{
    protected static string $resource = DiasTrabajadosResource::class;

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

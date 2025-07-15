<?php

namespace App\Filament\Resources\CierreDiaResource\Pages;

use App\Filament\Resources\CierreDiaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCierreDia extends EditRecord
{
    protected static string $resource = CierreDiaResource::class;

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

<?php

namespace App\Filament\Resources\CierreDiaResource\Pages;

use App\Filament\Resources\CierreDiaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCierreDia extends CreateRecord
{
    protected static string $resource = CierreDiaResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

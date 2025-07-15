<?php

namespace App\Filament\Resources\RopaResource\Pages;

use App\Filament\Resources\RopaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRopa extends CreateRecord
{
    protected static string $resource = RopaResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

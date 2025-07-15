<?php

namespace App\Filament\Resources\RepartosResource\Pages;

use App\Filament\Resources\RepartosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRepartos extends CreateRecord
{
    protected static string $resource = RepartosResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

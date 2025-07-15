<?php

namespace App\Filament\Resources\RepartosResource\Pages;

use App\Filament\Resources\RepartosResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRepartos extends ListRecords
{
    protected static string $resource = RepartosResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\CajasResource\Pages;

use App\Filament\Resources\CajasResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCajas extends CreateRecord
{
    protected static string $resource = CajasResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['fecha'])) {
            $data['fecha'] = now()->format('Y-m-d H:i:s'); // ✅ formato correcto
        }

        if (empty($data['numero_caja'])) {
            $ultimaCCN = \App\Models\Cajas::withTrashed()
                ->where('numero_caja', 'like', 'CCN-%')
                ->orderByDesc('id')
                ->first()?->numero_caja;

            $siguiente = ($ultimaCCN && preg_match('/CCN-(\d+)/', $ultimaCCN, $matches)) ? (int)$matches[1] + 1 : 1;
            $data['numero_caja'] = 'CCN-' . $siguiente; // ✅ sin espacios extra
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}

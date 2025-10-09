<?php

namespace App\Filament\Resources\RopaResource\Pages;

use App\Filament\Resources\RopaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Cajas;
use Illuminate\Support\Facades\DB;
class CreateRopa extends CreateRecord
{
    protected static string $resource = RopaResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function afterCreate(): void
    {
        $ropa = $this->record;

        $monto = (int) ($ropa->precio ?? 0);

        if (!empty($ropa->caja_id) && $monto > 0) {
            Cajas::where('id', $ropa->caja_id)->update([
                'total_caja' => DB::raw('COALESCE(total_caja, 0) + ' . $monto),
            ]);
        }
    }
}

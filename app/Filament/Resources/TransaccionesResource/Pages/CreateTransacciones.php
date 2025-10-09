<?php

namespace App\Filament\Resources\TransaccionesResource\Pages;

use App\Filament\Resources\TransaccionesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use App\Models\Cajas;
class CreateTransacciones extends CreateRecord
{
    protected static string $resource = TransaccionesResource::class;
    protected function afterCreate(): void
    {
        $trx = $this->record;
        $monto = (int) ($trx->total_con_descuento ?? $trx->total_trx ?? 0);

        if (!$trx->caja_id || $monto <= 0) {
            return;
        }

        Cajas::where('id', $trx->caja_id)->update([
            'total_caja'   => DB::raw('COALESCE(total_caja,0) + ' . $monto),
            'cantidad_trx' => DB::raw('COALESCE(cantidad_trx,0) + 1'),
        ]);
    }
    public function getTitle(): string
    {
        return 'Crear Transacción';
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}

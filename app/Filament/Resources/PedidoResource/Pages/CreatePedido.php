<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Cajas;
use Illuminate\Support\Facades\DB;
class CreatePedido extends CreateRecord
{
    protected static string $resource = PedidoResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function afterCreate(): void
    {
        $pedido = $this->record;

        // Solo sumar si fue cobrado en caja y hay monto/caja válidos
        if (
            ($pedido->cobrado_por ?? null) === 'caja' &&
            !empty($pedido->caja_id) &&
            (int) ($pedido->total_venta ?? 0) > 0
        ) {
            Cajas::where('id', $pedido->caja_id)->update([
                'total_caja' => DB::raw('COALESCE(total_caja,0) + ' . (int) $pedido->total_venta),
            ]);
        }
    }
}

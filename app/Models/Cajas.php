<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cajas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cajas';

    protected $fillable = [
        'fecha',
        'numero_caja',
        'observacion',
        'users_id',
        'estado',
        'cantidad_trx',
        'total_caja',
        'contador_ediciones',
        'contador_cancelados',
        'cierre',
        'fue_procesado',
        'total_trx_efectivo',
        'total_trx_tarjeta',
        'total_trx_transferencia',
        'total_trx_general',
        'total_ropa_efectivo',
        'total_ropa_tarjeta',
        'total_ropa_transferencia',
        'total_ropa_general',
        'total_boleta_efectivo',
        'total_boleta_tarjeta',
        'total_boleta_transferencia',
        'total_boleta_general',
        'cantidad_ventas_ropa',
        'contador_ropas_canceladas',
        'contador_pedidos_cancelados',
        'cantidad_boletas',
        'total_efectivo_general',
        'total_tarjeta_general',
        'total_transferencia_general',
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function transacciones()
    {
        return $this->hasMany(Transacciones::class, 'caja_id');
    }
}

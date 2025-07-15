<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CierreDia extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'cierre_dias';

    protected $fillable = [
        'fecha_hora_cierre',
        'creado_por',
        'caja_id',
        'reparto_id',
        'dinero_inicial',


        'wepa_lote',
        'wepa_ingresos',
        'wepa_egresos',
        'wepa_cantidad',
        'wepa_comision',

        'aquipago_ingresos',
        'aquipago_egresos',
        'aquipago_cantidad',
        'aquipago_comision',
        'aquipago_lote',
        'total_efectivo',
        'total_transferencia',
        'total_tarjeta',
        'total_general',
        'caja_efectivo',
        'caja_transferencia',
        'caja_tarjeta',
        'caja_total',
        'reparto_efectivo',
        'reparto_transferencia',
        'reparto_tarjeta',
        'reparto_total',

    ];

    // Relaciones

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function caja()
    {
        return $this->belongsTo(Cajas::class, 'caja_id');
    }

    public function reparto()
    {
        return $this->belongsTo(Repartos::class, 'reparto_id');
    }
}

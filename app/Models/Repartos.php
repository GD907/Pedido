<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Repartos extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'fecha',
        'creado_por',
        'repartidor',
        'zona',
        'estado_reparto',
        'cantidad_pedidos',
        'monto_total',
        'procesado',
        'total_efectivo',
        'total_transferencia',
        'total_tarjeta',
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function usuarioRepartidor()
    {
        return $this->belongsTo(User::class, 'repartidor');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'reparto_id');
    }
}

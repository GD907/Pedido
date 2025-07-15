<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pedido extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'pedidos';
    protected $fillable = [
        'fecha',
        'numero_factura',
        'observacion',
        'users_id',
        'clientes_id',
        'estado_pedido',
        'total_venta',
        'metodo_pago',
        'cobrado_por',
        'caja_id',
        'reparto_id',
    ];
    public function users()
    {
        // Una venta fue realizado por un usuario
        return $this->belongsTo(User::class);
    }
    // Una venta es realizado por un cliente
    public function clientes()
    {
        return $this->belongsTo(Cliente::class);
    }
    public function productos()
    {
        return $this->hasMany(PedidoDetalle::class, 'pedido_id');
    }
    public function caja()
    {
        return $this->belongsTo(Cajas::class, 'caja_id');
    }
    public function reparto()
    {
        return $this->belongsTo(\App\Models\Repartos::class, 'reparto_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PedidoDetalle extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'pedido_detalles';
    protected $fillable = ['sort', 'venta_id', 'producto_id', 'cantidad', 'precio', 'pordescuento', 'subtotal'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}

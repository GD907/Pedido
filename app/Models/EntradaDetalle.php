<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
class EntradaDetalle extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    protected $table = 'entrada_detalles';
    protected $fillable = ['entrada_id', 'producto_id', 'cantidad', 'disponible', 'preciocompra','precioventa', 'preciotransaccion'];
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
class Transaccion_Detalles extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    protected $table = 'transaccion_detalles';
    protected $fillable = ['trx_id',    'producto_id',    'cantidad',    'disponible',    'precio',    'pordescuento',    'subtotal'];
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}

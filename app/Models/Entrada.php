<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
class Entrada extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    protected $table = 'entradas';
    protected $fillable = ['fecha',	'tipo_entrada',	'users_id',	'proveedor', 'observacion'];
    public function users()
    {
        // Una venta fue realizado por un usuario
        return $this->belongsTo(User::class);
    }
    public function productos()
    {
        return $this->hasMany(EntradaDetalle::class);
    }
}

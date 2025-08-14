<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
class Ropa extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'fecha',
        'descripcion',
        'precio',
        'unidades',
        'creado_por',
        'metodo_pago',
        'caja_id',
        'estado',
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function caja()
    {
        return $this->belongsTo(Cajas::class, 'caja_id');
    }
}

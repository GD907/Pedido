<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ropa extends Model
{
    use HasFactory, SoftDeletes;

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

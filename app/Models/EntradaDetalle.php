<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntradaDetalle extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'entrada_detalles';
    protected $fillable = ['entrada_id', 'producto_id', 'cantidad', 'disponible', 'preciocompra','precioventa'];
}

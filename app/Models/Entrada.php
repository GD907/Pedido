<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entrada extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'entradas';
    protected $fillable = ['fecha', 'observacion'];
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cajas extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'cajas';
    protected $fillable = [	'fecha' ,	'numero_caja',	'observacion',	'users_id',	'estado', 'cantidad_trx',	'total_caja', 'contador_ediciones', 'contador_cancelados',	'cierre', 'fue_procesado'];
    public function users()
    {
        // Una caja fue abierta por un usuario
        return $this->belongsTo(User::class);
    }


    public function transacciones()
    {
        return $this->hasMany(Transacciones::class, 'caja_id');
    }
}

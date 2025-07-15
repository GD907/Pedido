<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Transacciones extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'transacciones';
    protected $fillable = ['caja_id', 'fecha',	'numero_trx', 'users_id',	'clientes_id',	'observacion',	'total_trx', 'estado_transaccion' , 'metodo_pago',  'porcentaje_descuento',
    'total_con_descuento',        ];

    public function users()
    {
        // Una transaccion fue realizado por un usuario
        return $this->belongsTo(User::class);
    }
    public function cajas()
    {
        return $this->belongsTo(Cajas::class, 'caja_id');
    }
    public function clientes()
    {
        return $this->belongsTo(Cliente::class);
    }
    public function productos()
    {
        return $this->hasMany(Transaccion_Detalles::class);
    }
}

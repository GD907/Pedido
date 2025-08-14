<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
class CalendarioCheques extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'numero_cheque',
        'monto',
        'banco',
        'fecha_emitida',
        'fecha_vencimiento',
        'proveedor',
        'estado',
        'firmado_por',
        'concepto',
    ];
}

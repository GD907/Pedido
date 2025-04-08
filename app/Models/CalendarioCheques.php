<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarioCheques extends Model
{
    use HasFactory, SoftDeletes;
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

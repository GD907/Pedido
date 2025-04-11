<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiasTrabajados extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dias_trabajados';

    protected $fillable = [
        'user_id',
        'fecha',
        'turno',
        'observacion',
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

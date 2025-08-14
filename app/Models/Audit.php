<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Audit extends Model
{
    protected $table = 'audits';

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // Accessor para mostrar el nombre del usuario si está disponible
    public function getUserNameAttribute(): string
    {
        return optional($this->user)->name ?? 'Sistema o Desconocido';
    }

    // Accessor para mostrar el modelo simplificado (ej: App\Models\Pedido → Pedido)
    public function getModelNameAttribute(): string
    {
        return class_basename($this->auditable_type);
    }
}

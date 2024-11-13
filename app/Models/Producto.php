<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\NuevoProductoNotification;
use Illuminate\Support\Facades\Notification;
class Producto extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'productos';
    public function categoria_productos()
    {
        // Una categoria pertenece a un producto
        return $this->belongsTo(CategoriaProducto::class);
    }
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'proveedor',
        'preciocompra',
        'precio',
        'precio_transacciones',
        'stock',
        'unidades_caja',
        'umbralmin',
        'categoria_productos_id'
    ];
    public static function afterCreate($producto): void
    {
        $usuarios = \App\Models\User::all(); // Obtener todos los usuarios
        Notification::send($usuarios, new NuevoProductoNotification($producto));
    }
}

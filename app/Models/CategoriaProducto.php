<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CategoriaProducto extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'categoria_productos';
    protected $fillable = ['nombre', 'descripcion'];
    public function productos() {
         return $this->hasMany(Producto::class);
        }
}

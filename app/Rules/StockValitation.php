<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Producto;
class StockValitation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    protected $disponible;

    public function __construct($disponible)
    {
        $this->disponible = intval($disponible);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value < $this->disponible) {
            $fail("Cantidad no disponible. Stock actual: {$this->disponible} unidades.");
        }
    }
}

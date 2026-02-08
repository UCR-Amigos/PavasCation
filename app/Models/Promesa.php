<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class Promesa extends Model
{
    use Auditable;
    protected $table = 'promesas';

    protected $fillable = [
        'persona_id',
        'categoria',
        'monto',
        'frecuencia',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    private const CATEGORIA_MAP = [
        'campa' => 'campamento',
        'prestamo' => 'pro_templo',
        'pro-templo' => 'pro_templo',
        'ofrenda-especial' => 'ofrenda_especial',
    ];

    protected function categoria(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $lower = strtolower($value);
                return self::CATEGORIA_MAP[$lower] ?? $lower;
            },
        );
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }
}

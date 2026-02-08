<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SobreDetalle extends Model
{
    protected $table = 'sobre_detalles';

    protected $fillable = [
        'sobre_id',
        'categoria',
        'monto',
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

    public function sobre(): BelongsTo
    {
        return $this->belongsTo(Sobre::class);
    }
}

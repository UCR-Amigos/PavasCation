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

    /**
     * Variantes de nombre en BD para cada categoría canónica.
     * Se usa en queries SQL donde el accessor no aplica.
     */
    private const CATEGORIA_VARIANTS = [
        'campamento' => ['campamento', 'campa'],
        'pro_templo' => ['pro_templo', 'pro-templo', 'prestamo'],
        'ofrenda_especial' => ['ofrenda_especial', 'ofrenda-especial'],
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

    /**
     * Scope para buscar por categoría incluyendo variantes de nombre en BD.
     * Usar en vez de where('categoria', $cat) para queries SQL.
     */
    public function scopeWhereCategoria($query, $categoria)
    {
        $variants = self::CATEGORIA_VARIANTS[$categoria] ?? [$categoria];
        return $query->whereIn('categoria', $variants);
    }

    /**
     * Retorna las variantes de BD para una categoría canónica.
     */
    public static function categoriaVariants(string $categoria): array
    {
        return self::CATEGORIA_VARIANTS[$categoria] ?? [$categoria];
    }

    public function sobre(): BelongsTo
    {
        return $this->belongsTo(Sobre::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TotalesCulto extends Model
{
    protected $table = 'totales_culto';

    protected $fillable = [
        'culto_id',
        'total_diezmo',
        'total_ofrenda_especial',
        'total_misiones',
        'total_seminario',
        'total_campamento',
        'total_pro_templo',
        'total_suelto',
        'total_egresos',
        'total_general',
        'cantidad_sobres',
        'cantidad_transferencias',
        'notas',
    ];

    protected $casts = [
        'total_diezmo' => 'decimal:2',
        'total_ofrenda_especial' => 'decimal:2',
        'total_misiones' => 'decimal:2',
        'total_seminario' => 'decimal:2',
        'total_campamento' => 'decimal:2',
        'total_pro_templo' => 'decimal:2',
        'total_suelto' => 'decimal:2',
        'total_general' => 'decimal:2',
    ];

    public function culto(): BelongsTo
    {
        return $this->belongsTo(Culto::class);
    }
}

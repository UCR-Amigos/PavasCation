<?php

namespace App\Models;

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

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }
}

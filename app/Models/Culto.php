<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Culto extends Model
{
    protected $table = 'cultos';

    protected $fillable = [
        'fecha',
        'hora',
        'tipo_culto',
        'notas',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'datetime',
    ];

    public function sobres(): HasMany
    {
        return $this->hasMany(Sobre::class);
    }

    public function ofrendasSueltas(): HasMany
    {
        return $this->hasMany(OfrendaSuelta::class);
    }

    public function asistencia(): HasOne
    {
        return $this->hasOne(Asistencia::class);
    }

    public function totales(): HasOne
    {
        return $this->hasOne(TotalesCulto::class);
    }
}

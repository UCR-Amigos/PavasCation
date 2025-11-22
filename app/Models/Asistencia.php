<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    protected $table = 'asistencia';

    protected $fillable = [
        'culto_id',
        'chapel_hombres',
        'chapel_mujeres',
        'chapel_adultos_mayores',
        'chapel_adultos',
        'chapel_jovenes_masculinos',
        'chapel_jovenes_femeninas',
        'clase_0_1_hombres',
        'clase_0_1_mujeres',
        'clase_0_1_maestros_hombres',
        'clase_0_1_maestros_mujeres',
        'clase_2_6_hombres',
        'clase_2_6_mujeres',
        'clase_2_6_maestros_hombres',
        'clase_2_6_maestros_mujeres',
        'clase_7_8_hombres',
        'clase_7_8_mujeres',
        'clase_7_8_maestros_hombres',
        'clase_7_8_maestros_mujeres',
        'clase_9_11_hombres',
        'clase_9_11_mujeres',
        'clase_9_11_maestros_hombres',
        'clase_9_11_maestros_mujeres',
        'total_asistencia',
        'cerrado',
        'cerrado_at',
        'cerrado_por',
    ];

    protected $casts = [
        'cerrado' => 'boolean',
        'cerrado_at' => 'datetime',
    ];

    public function culto(): BelongsTo
    {
        return $this->belongsTo(Culto::class);
    }

    public function cerradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cerrado_por');
    }
}

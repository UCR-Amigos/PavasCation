<?php

namespace App\Http\Controllers;

use App\Models\Culto;
use App\Models\Asistencia;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    public function index()
    {
        $cultos = Culto::with('asistencia')->orderBy('fecha', 'desc')->paginate(20);
        return view('asistencia.index', compact('cultos'));
    }

    public function create()
    {
        $cultos = Culto::whereDoesntHave('asistencia')->orderBy('fecha', 'desc')->get();
        return view('asistencia.create', compact('cultos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'culto_id' => 'required|exists:cultos,id|unique:asistencia,culto_id',
            'chapel_hombres' => 'required|integer|min:0',
            'chapel_mujeres' => 'required|integer|min:0',
            'chapel_adultos_mayores' => 'required|integer|min:0',
            'chapel_adultos' => 'required|integer|min:0',
            'chapel_jovenes' => 'required|integer|min:0',
            'clase_0_1_hombres' => 'required|integer|min:0',
            'clase_0_1_mujeres' => 'required|integer|min:0',
            'clase_0_1_maestros_hombres' => 'required|integer|min:0',
            'clase_0_1_maestros_mujeres' => 'required|integer|min:0',
            'clase_2_6_hombres' => 'required|integer|min:0',
            'clase_2_6_mujeres' => 'required|integer|min:0',
            'clase_2_6_maestros_hombres' => 'required|integer|min:0',
            'clase_2_6_maestros_mujeres' => 'required|integer|min:0',
            'clase_7_8_hombres' => 'required|integer|min:0',
            'clase_7_8_mujeres' => 'required|integer|min:0',
            'clase_7_8_maestros_hombres' => 'required|integer|min:0',
            'clase_7_8_maestros_mujeres' => 'required|integer|min:0',
            'clase_9_11_hombres' => 'required|integer|min:0',
            'clase_9_11_mujeres' => 'required|integer|min:0',
            'clase_9_11_maestros_hombres' => 'required|integer|min:0',
            'clase_9_11_maestros_mujeres' => 'required|integer|min:0',
            'total_asistencia' => 'required|integer|min:0',
        ]);

        Asistencia::create($validated);

        return redirect()->route('asistencia.index')
            ->with('success', 'Asistencia registrada correctamente.');
    }

    public function edit(Asistencia $asistencium)
    {
        return view('asistencia.edit', ['asistencia' => $asistencium]);
    }

    public function update(Request $request, Asistencia $asistencium)
    {
        $validated = $request->validate([
            'chapel_hombres' => 'required|integer|min:0',
            'chapel_mujeres' => 'required|integer|min:0',
            'chapel_adultos_mayores' => 'required|integer|min:0',
            'chapel_adultos' => 'required|integer|min:0',
            'chapel_jovenes' => 'required|integer|min:0',
            'clase_0_1_hombres' => 'required|integer|min:0',
            'clase_0_1_mujeres' => 'required|integer|min:0',
            'clase_0_1_maestros_hombres' => 'required|integer|min:0',
            'clase_0_1_maestros_mujeres' => 'required|integer|min:0',
            'clase_2_6_hombres' => 'required|integer|min:0',
            'clase_2_6_mujeres' => 'required|integer|min:0',
            'clase_2_6_maestros_hombres' => 'required|integer|min:0',
            'clase_2_6_maestros_mujeres' => 'required|integer|min:0',
            'clase_7_8_hombres' => 'required|integer|min:0',
            'clase_7_8_mujeres' => 'required|integer|min:0',
            'clase_7_8_maestros_hombres' => 'required|integer|min:0',
            'clase_7_8_maestros_mujeres' => 'required|integer|min:0',
            'clase_9_11_hombres' => 'required|integer|min:0',
            'clase_9_11_mujeres' => 'required|integer|min:0',
            'clase_9_11_maestros_hombres' => 'required|integer|min:0',
            'clase_9_11_maestros_mujeres' => 'required|integer|min:0',
            'total_asistencia' => 'required|integer|min:0',
        ]);

        $asistencium->update($validated);

        return redirect()->route('asistencia.index')
            ->with('success', 'Asistencia actualizada correctamente.');
    }

    public function destroy(Asistencia $asistencium)
    {
        $asistencium->delete();

        return redirect()->route('asistencia.index')
            ->with('success', 'Asistencia eliminada correctamente.');
    }
}

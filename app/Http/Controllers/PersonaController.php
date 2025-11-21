<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Promesa;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PersonaController extends Controller
{
    public function index()
    {
        $personas = Persona::withCount(['sobres', 'promesas'])->paginate(20);
        return view('personas.index', compact('personas'));
    }

    public function create()
    {
        return view('personas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
            'activo' => 'boolean',
            'notas' => 'nullable|string',
        ]);

        Persona::create($validated);

        return redirect()->route('personas.index')
            ->with('success', 'Persona registrada correctamente.');
    }

    public function show(Persona $persona)
    {
        $persona->load(['sobres.detalles', 'promesas']);
        
        // Calcular cumplimiento de promesas
        $promesasConEstatus = $persona->promesas->map(function ($promesa) use ($persona) {
            $montoPagado = $persona->sobres()
                ->whereHas('detalles', function ($query) use ($promesa) {
                    $query->where('categoria', $promesa->categoria);
                })
                ->whereMonth('created_at', Carbon::now()->month)
                ->get()
                ->sum(function ($sobre) use ($promesa) {
                    return $sobre->detalles()
                        ->where('categoria', $promesa->categoria)
                        ->sum('monto');
                });

            return [
                'promesa' => $promesa,
                'pagado' => $montoPagado,
                'faltante' => max(0, $promesa->monto - $montoPagado),
                'cumplido' => $montoPagado >= $promesa->monto,
            ];
        });

        return view('personas.show', compact('persona', 'promesasConEstatus'));
    }

    public function edit(Persona $persona)
    {
        return view('personas.edit', compact('persona'));
    }

    public function update(Request $request, Persona $persona)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
            'activo' => 'boolean',
            'notas' => 'nullable|string',
        ]);

        $persona->update($validated);

        return redirect()->route('personas.index')
            ->with('success', 'Persona actualizada correctamente.');
    }

    public function destroy(Persona $persona)
    {
        $persona->delete();

        return redirect()->route('personas.index')
            ->with('success', 'Persona eliminada correctamente.');
    }
}

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
            'promesas' => 'nullable|array',
            'promesas.*.categoria' => 'required|string',
            'promesas.*.monto' => 'required|numeric|min:0',
            'promesas.*.frecuencia' => 'required|in:semanal,quincenal,mensual',
        ]);

        $persona = Persona::create($validated);

        // Guardar promesas si existen
        if ($request->has('promesas')) {
            foreach ($request->promesas as $promesaData) {
                if (!empty($promesaData['monto']) && $promesaData['monto'] > 0) {
                    $persona->promesas()->create($promesaData);
                }
            }
        }

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
        $persona->load('promesas');
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
            'promesas' => 'nullable|array',
            'promesas.*.categoria' => 'required|string',
            'promesas.*.monto' => 'required|numeric|min:0',
            'promesas.*.frecuencia' => 'required|in:semanal,quincenal,mensual',
        ]);

        $persona->update($validated);

        // Sincronizar promesas
        $persona->promesas()->delete(); // Eliminar promesas anteriores
        if ($request->has('promesas')) {
            foreach ($request->promesas as $promesaData) {
                if (!empty($promesaData['monto']) && $promesaData['monto'] > 0) {
                    $persona->promesas()->create($promesaData);
                }
            }
        }

        return redirect()->route('personas.index')
            ->with('success', 'Persona actualizada correctamente.');
    }

    public function destroy(Persona $persona)
    {
        $persona->delete();

        return redirect()->route('personas.index')
            ->with('success', 'Persona eliminada correctamente.');
    }

    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255'
        ]);

        $persona = Persona::create([
            'nombre' => $validated['nombre'],
            'activo' => true
        ]);

        return response()->json([
            'success' => true,
            'persona' => $persona
        ]);
    }

    /**
     * Reinicia los compromisos de una persona desde una fecha específica
     * Borra todo el historial anterior y comienza de cero desde el mes/año seleccionado
     */
    public function reiniciarCompromisos(Request $request, Persona $persona)
    {
        $validated = $request->validate([
            'fecha_inicio' => 'required|date',
        ]);

        $fechaInicio = \Carbon\Carbon::parse($validated['fecha_inicio']);
        
        // Eliminar todos los compromisos anteriores a la fecha de inicio
        $persona->compromisos()
            ->where(function($query) use ($fechaInicio) {
                $query->where('año', '<', $fechaInicio->year)
                    ->orWhere(function($q) use ($fechaInicio) {
                        $q->where('año', '=', $fechaInicio->year)
                          ->where('mes', '<', $fechaInicio->month);
                    });
            })
            ->delete();

        // Resetear el compromiso del mes de inicio (si existe) para que saldo_anterior = 0
        $persona->compromisos()
            ->where('año', $fechaInicio->year)
            ->where('mes', $fechaInicio->month)
            ->update(['saldo_anterior' => 0, 'saldo_actual' => 0]);

        return redirect()->route('personas.edit', $persona)
            ->with('success', 'Compromisos reiniciados correctamente desde ' . $fechaInicio->format('F Y'));
    }

    /**
     * Limpia completamente todo: promesas, compromisos e historial
     * Deja a la persona en estado "limpio" sin ninguna deuda ni compromiso
     */
    public function limpiarTodo(Persona $persona)
    {
        // Eliminar todas las promesas
        $persona->promesas()->delete();
        
        // Eliminar todos los compromisos
        $persona->compromisos()->delete();

        return redirect()->route('personas.edit', $persona)
            ->with('success', 'Todas las promesas, compromisos e historial han sido eliminados.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Promesa;
use App\Models\Compromiso;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class PersonaController extends Controller
{
    public function index(Request $request)
    {
        $query = Persona::withCount(['sobres', 'promesas']);
        
        // Búsqueda por nombre
        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }
        
        $personas = $query->paginate(20);
        $personasInactivas = Persona::where('activo', false)->count();
        return view('personas.index', compact('personas', 'personasInactivas'));
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
            'correo' => 'nullable|email|max:255|unique:users,email',
            'password' => 'required_with:correo|nullable|string|min:8',
            'activo' => 'boolean',
            'notas' => 'nullable|string',
            'promesas' => 'nullable|array',
            'promesas.*.categoria' => 'required|string',
            'promesas.*.monto' => 'required|numeric|min:0',
            'promesas.*.frecuencia' => 'required|in:semanal,quincenal,mensual',
        ], [
            'correo.unique' => 'Este correo electrónico ya está registrado en el sistema. Por favor, usa otro correo.',
            'correo.email' => 'El formato del correo electrónico no es válido.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.required_with' => 'Debes proporcionar una contraseña cuando ingresas un correo electrónico.',
        ]);

        // Si hay correo y contraseña, crear usuario miembro
        $user = null;
        if (!empty($validated['correo']) && !empty($validated['password'])) {
            $user = User::create([
                'name' => $validated['nombre'],
                'email' => $validated['correo'],
                'password' => Hash::make($validated['password']),
                'rol' => 'miembro',
            ]);
        }

        // Crear persona
        $personaData = [
            'nombre' => $validated['nombre'],
            'telefono' => $validated['telefono'] ?? null,
            'correo' => $validated['correo'] ?? null,
            'password' => !empty($validated['password']) ? Hash::make($validated['password']) : null,
            'user_id' => $user ? $user->id : null,
            'activo' => $validated['activo'] ?? true,
            'notas' => $validated['notas'] ?? null,
        ];

        $persona = Persona::create($personaData);

        // Guardar promesas si existen
        if ($request->has('promesas')) {
            foreach ($request->promesas as $promesaData) {
                if (!empty($promesaData['monto']) && $promesaData['monto'] > 0) {
                    $persona->promesas()->create($promesaData);
                }
            }
        }

        return redirect()->route('personas.index')
            ->with('success', 'Persona registrada correctamente.' . ($user ? ' Se creó acceso como miembro.' : ''));
    }

    public function show(Request $request, Persona $persona)
    {
        // Query de sobres con filtros
        $sobresQuery = $persona->sobres()->with(['detalles', 'culto']);
        
        // Aplicar filtros
        if ($request->filled('mes') && $request->mes !== 'todos') {
            $sobresQuery->whereHas('culto', function($q) use ($request) {
                $q->whereMonth('fecha', $request->mes);
            });
        }
        
        if ($request->filled('año') && $request->año !== 'todos') {
            $sobresQuery->whereHas('culto', function($q) use ($request) {
                $q->whereYear('fecha', $request->año);
            });
        }
        
        if ($request->filled('fecha_inicio')) {
            $sobresQuery->whereHas('culto', function($q) use ($request) {
                $q->where('fecha', '>=', $request->fecha_inicio);
            });
        }
        
        if ($request->filled('fecha_fin')) {
            $sobresQuery->whereHas('culto', function($q) use ($request) {
                $q->where('fecha', '<=', $request->fecha_fin);
            });
        }
        
        $persona->setRelation('sobres', $sobresQuery->get());
        $persona->load('promesas');
        
        // Obtener años disponibles de los sobres
        $añosDisponibles = $persona->sobres()->whereHas('culto')
            ->join('cultos', 'sobres.culto_id', '=', 'cultos.id')
            ->selectRaw('YEAR(cultos.fecha) as año')
            ->groupBy('año')
            ->orderBy('año', 'desc')
            ->pluck('año');
        
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

        return view('personas.show', compact('persona', 'promesasConEstatus', 'añosDisponibles'));
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
            'correo' => 'nullable|email|unique:users,email,' . ($persona->user_id ?? 'NULL'),
            'password' => $persona->user_id ? 'nullable|string|min:8' : 'required_with:correo|nullable|string|min:8',
            'activo' => 'boolean',
            'notas' => 'nullable|string',
            'promesas' => 'nullable|array',
            'promesas.*.categoria' => 'required|string',
            'promesas.*.monto' => 'required|numeric|min:0',
            'promesas.*.frecuencia' => 'required|in:semanal,quincenal,mensual',
        ], [
            'correo.unique' => 'Este correo electrónico ya está registrado en el sistema. Por favor, usa otro correo.',
            'correo.email' => 'El formato del correo electrónico no es válido.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.required_with' => 'Debes proporcionar una contraseña cuando ingresas un correo electrónico.',
        ]);

        // Manejar cambios en correo y usuario
        $updateUser = false;
        $createUser = false;
        
        // Si hay correo nuevo y no tiene usuario, crear usuario
        if (!empty($validated['correo']) && !$persona->user_id && !empty($validated['password'])) {
            $user = User::create([
                'name' => $validated['nombre'],
                'email' => $validated['correo'],
                'password' => Hash::make($validated['password']),
                'rol' => 'miembro',
            ]);
            $persona->user_id = $user->id;
            $createUser = true;
        }
        // Si tiene usuario y cambió el correo o contraseña, actualizar usuario
        elseif ($persona->user_id) {
            $user = $persona->user;
            if ($user) {
                $userUpdates = [];
                if (!empty($validated['correo']) && $user->email !== $validated['correo']) {
                    $userUpdates['email'] = $validated['correo'];
                }
                if (!empty($validated['password'])) {
                    $userUpdates['password'] = Hash::make($validated['password']);
                }
                if (!empty($validated['nombre']) && $user->name !== $validated['nombre']) {
                    $userUpdates['name'] = $validated['nombre'];
                }
                if (!empty($userUpdates)) {
                    $user->update($userUpdates);
                    $updateUser = true;
                }
            }
        }

        // Actualizar persona
        $personaData = [
            'nombre' => $validated['nombre'],
            'telefono' => $validated['telefono'] ?? null,
            'correo' => $validated['correo'] ?? null,
            'activo' => $validated['activo'] ?? true,
            'notas' => $validated['notas'] ?? null,
        ];

        // Solo actualizar password si se proporcionó uno nuevo
        if (!empty($validated['password'])) {
            $personaData['password'] = Hash::make($validated['password']);
        }

        $persona->update($personaData);

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
            ->with('success', 'Persona actualizada correctamente.' . 
                ($createUser ? ' Se creó acceso como miembro.' : '') . 
                ($updateUser ? ' Se actualizó el acceso de miembro.' : ''));
    }

    public function destroy(Persona $persona)
    {
        // Eliminar todas las promesas de la persona
        $persona->promesas()->delete();
        
        // Eliminar todos los compromisos de la persona
        $persona->compromisos()->delete();
        
        // Los sobres NO se eliminan porque el dinero ya fue dado y debe quedar registrado
        // Solo se rompe la relación
        $persona->sobres()->update(['persona_id' => null]);
        
        // Si tiene usuario asociado, eliminarlo también
        if ($persona->user_id) {
            $user = $persona->user;
            if ($user) {
                $user->delete();
            }
        }
        
        // Finalmente eliminar la persona
        $persona->delete();

        return redirect()->route('personas.index')
            ->with('success', 'Persona y sus promesas eliminadas correctamente. Los sobres quedan registrados.');
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
     * Limpia todas las personas inactivas y sus promesas/compromisos
     */
    public function limpiarInactivas()
    {
        // Obtener personas inactivas
        $personasInactivas = Persona::where('activo', false)->get();
        $count = $personasInactivas->count();
        
        foreach ($personasInactivas as $persona) {
            // Eliminar promesas
            $persona->promesas()->delete();
            
            // Eliminar compromisos
            $persona->compromisos()->delete();
            
            // Desvincular sobres (no eliminarlos)
            $persona->sobres()->update(['persona_id' => null]);
            
            // Eliminar usuario asociado si existe
            if ($persona->user_id) {
                $user = $persona->user;
                if ($user) {
                    $user->delete();
                }
            }
            
            // Eliminar persona
            $persona->delete();
        }
        
        return redirect()->route('personas.index')
            ->with('success', "Se eliminaron {$count} personas inactivas y sus promesas/compromisos.");
    }

    /**
     * Resetea todas las promesas y compromisos de todas las personas
     * Los sobres dados se mantienen como historial
     */
    public function resetearPromesas()
    {
        // Eliminar todos los compromisos
        Compromiso::truncate();
        
        // Eliminar todas las promesas
        Promesa::truncate();
        
        return redirect()->route('personas.index')
            ->with('success', 'Todas las promesas y compromisos han sido reseteados. Los sobres dados se mantienen como historial.');
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

    /**
     * Genera un reporte PDF de personas con sus sobres en un rango de fechas
     */
    public function reportePdf(Request $request)
    {
        $validated = $request->validate([
            'tipo_filtro' => 'required|in:meses,fechas',
            'mes_inicio' => 'required_if:tipo_filtro,meses|nullable|integer|min:1|max:12',
            'mes_fin' => 'required_if:tipo_filtro,meses|nullable|integer|min:1|max:12',
            'fecha_inicio' => 'required_if:tipo_filtro,fechas|nullable|date',
            'fecha_fin' => 'required_if:tipo_filtro,fechas|nullable|date',
            'accion' => 'required|in:ver,descargar',
        ]);

        // Determinar el rango de fechas según el tipo de filtro
        if ($validated['tipo_filtro'] === 'meses') {
            $añoActual = date('Y');
            $fechaInicio = Carbon::create($añoActual, $validated['mes_inicio'], 1)->startOfMonth();
            $fechaFin = Carbon::create($añoActual, $validated['mes_fin'], 1)->endOfMonth();
            $tituloPeriodo = $fechaInicio->locale('es')->translatedFormat('F') . ' - ' . 
                           $fechaFin->locale('es')->translatedFormat('F Y');
        } else {
            $fechaInicio = Carbon::parse($validated['fecha_inicio'])->startOfDay();
            $fechaFin = Carbon::parse($validated['fecha_fin'])->endOfDay();
            $tituloPeriodo = $fechaInicio->locale('es')->translatedFormat('d/m/Y') . ' - ' . 
                           $fechaFin->locale('es')->translatedFormat('d/m/Y');
        }

        // Validar que fecha inicio no sea mayor a fecha fin
        if ($fechaInicio->gt($fechaFin)) {
            return back()->withErrors(['error' => 'La fecha de inicio no puede ser mayor a la fecha de fin.']);
        }

        // Obtener personas activas con sus promesas
        $personas = Persona::where('activo', true)
            ->with(['promesas', 'sobres' => function($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('created_at', [$fechaInicio, $fechaFin])
                      ->with(['detalles', 'culto']);
            }])
            ->orderBy('nombre')
            ->get();

        // Calcular meses en el período
        $mesesEnPeriodo = $fechaInicio->diffInMonths($fechaFin) + 1;

        // Calcular totales por categoría y por persona
        $totalesPorCategoria = [];
        $totalGeneral = 0;
        $totalPrometidoGeneral = 0;

        foreach ($personas as $persona) {
            $persona->total_sobres = 0;
            $persona->promesas_periodo = [];
            $persona->cumplimiento_global = 0;

            // Calcular lo que debería dar según sus promesas en este período
            foreach ($persona->promesas as $promesa) {
                $montoPorMes = $promesa->monto;
                $categoria = $promesa->categoria;
                
                // Calcular cuánto debería dar en total en el período según frecuencia
                switch ($promesa->frecuencia) {
                    case 'semanal':
                        $montoEsperado = $montoPorMes * 4 * $mesesEnPeriodo; // 4 semanas por mes
                        break;
                    case 'quincenal':
                        $montoEsperado = $montoPorMes * 2 * $mesesEnPeriodo; // 2 quincenas por mes
                        break;
                    case 'mensual':
                        $montoEsperado = $montoPorMes * $mesesEnPeriodo;
                        break;
                    default:
                        $montoEsperado = $montoPorMes * $mesesEnPeriodo;
                }

                // Calcular cuánto realmente dio en esta categoría
                $montoDado = 0;
                foreach ($persona->sobres as $sobre) {
                    foreach ($sobre->detalles as $detalle) {
                        if ($detalle->categoria === $categoria) {
                            $montoDado += $detalle->monto;
                        }
                    }
                }

                $persona->promesas_periodo[$categoria] = [
                    'esperado' => $montoEsperado,
                    'dado' => $montoDado,
                    'diferencia' => $montoDado - $montoEsperado,
                    'cumple' => $montoDado >= $montoEsperado,
                    'porcentaje' => $montoEsperado > 0 ? ($montoDado / $montoEsperado * 100) : 0
                ];

                $totalPrometidoGeneral += $montoEsperado;
            }

            // Calcular totales de sobres
            foreach ($persona->sobres as $sobre) {
                foreach ($sobre->detalles as $detalle) {
                    if (!isset($totalesPorCategoria[$detalle->categoria])) {
                        $totalesPorCategoria[$detalle->categoria] = 0;
                    }
                    $totalesPorCategoria[$detalle->categoria] += $detalle->monto;
                    $persona->total_sobres += $detalle->monto;
                    $totalGeneral += $detalle->monto;
                }
            }

            // Calcular cumplimiento global (solo si tiene promesas)
            if (count($persona->promesas_periodo) > 0) {
                $totalEsperado = 0;
                $totalDado = 0;
                foreach ($persona->promesas_periodo as $datos) {
                    $totalEsperado += $datos['esperado'];
                    $totalDado += $datos['dado'];
                }
                $persona->cumplimiento_global = $totalEsperado > 0 ? ($totalDado / $totalEsperado * 100) : 0;
            }
        }

        // Filtrar solo personas con promesas o sobres
        $personas = $personas->filter(function($persona) {
            return count($persona->promesas) > 0 || $persona->sobres->count() > 0;
        });

        $pdf = \PDF::loadView('pdfs.reporte-personas', [
            'personas' => $personas,
            'tituloPeriodo' => $tituloPeriodo,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'totalesPorCategoria' => $totalesPorCategoria,
            'totalGeneral' => $totalGeneral,
            'totalPrometidoGeneral' => $totalPrometidoGeneral,
            'mesesEnPeriodo' => $mesesEnPeriodo,
        ]);

        $pdf->setPaper('letter', 'portrait');

        $nombreArchivo = 'reporte-personas-' . $fechaInicio->format('Ymd') . '-' . $fechaFin->format('Ymd') . '.pdf';

        if ($validated['accion'] === 'descargar') {
            return $pdf->download($nombreArchivo);
        } else {
            return $pdf->stream($nombreArchivo);
        }
    }
}

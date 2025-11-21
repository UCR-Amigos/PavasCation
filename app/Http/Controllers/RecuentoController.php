<?php

namespace App\Http\Controllers;

use App\Models\Culto;
use App\Models\Persona;
use App\Models\Sobre;
use App\Models\SobreDetalle;
use App\Services\CalculoTotalesCultoService;
use Illuminate\Http\Request;

class RecuentoController extends Controller
{
    protected $calculoService;

    public function __construct(CalculoTotalesCultoService $calculoService)
    {
        $this->calculoService = $calculoService;
    }

    public function index(Request $request)
    {
        $cultoId = $request->get('culto_id');
        
        $cultos = Culto::orderBy('fecha', 'desc')->get();
        $sobres = $cultoId 
            ? Sobre::where('culto_id', $cultoId)->with(['persona', 'detalles'])->get()
            : collect();

        $cultoSeleccionado = $cultoId ? Culto::find($cultoId) : null;

        return view('recuento.index', compact('sobres', 'cultos', 'cultoSeleccionado'));
    }

    public function create(Request $request)
    {
        $cultoId = $request->get('culto_id');
        $culto = $cultoId ? Culto::findOrFail($cultoId) : null;
        $personas = Persona::where('activo', true)->get();

        return view('recuento.create', compact('culto', 'personas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'culto_id' => 'required|exists:cultos,id',
            'persona_id' => 'nullable|exists:personas,id',
            'metodo_pago' => 'required|in:efectivo,transferencia',
            'notas' => 'nullable|string',
            'detalles' => 'required|array',
            'detalles.*.categoria' => 'required|string',
            'detalles.*.monto' => 'required|numeric|min:0',
        ]);

        // Calcular total declarado
        $totalDeclarado = collect($validated['detalles'])->sum('monto');

        $sobre = Sobre::create([
            'culto_id' => $validated['culto_id'],
            'persona_id' => $validated['persona_id'] ?? null,
            'metodo_pago' => $validated['metodo_pago'],
            'total_declarado' => $totalDeclarado,
            'notas' => $validated['notas'] ?? null,
        ]);

        // Crear detalles
        foreach ($validated['detalles'] as $detalle) {
            if ($detalle['monto'] > 0) {
                SobreDetalle::create([
                    'sobre_id' => $sobre->id,
                    'categoria' => $detalle['categoria'],
                    'monto' => $detalle['monto'],
                ]);
            }
        }

        // Recalcular totales del culto
        $this->calculoService->recalcular($sobre->culto);

        return redirect()->route('recuento.index', ['culto_id' => $validated['culto_id']])
            ->with('success', 'Sobre registrado correctamente.');
    }

    public function edit(Sobre $sobre)
    {
        $sobre->load(['detalles', 'culto']);
        $personas = Persona::where('activo', true)->get();

        return view('recuento.edit', compact('sobre', 'personas'));
    }

    public function update(Request $request, Sobre $sobre)
    {
        $validated = $request->validate([
            'persona_id' => 'nullable|exists:personas,id',
            'metodo_pago' => 'required|in:efectivo,transferencia',
            'notas' => 'nullable|string',
            'detalles' => 'required|array',
            'detalles.*.categoria' => 'required|string',
            'detalles.*.monto' => 'required|numeric|min:0',
        ]);

        $totalDeclarado = collect($validated['detalles'])->sum('monto');

        $sobre->update([
            'persona_id' => $validated['persona_id'] ?? null,
            'metodo_pago' => $validated['metodo_pago'],
            'total_declarado' => $totalDeclarado,
            'notas' => $validated['notas'] ?? null,
        ]);

        // Eliminar detalles existentes y crear nuevos
        $sobre->detalles()->delete();
        foreach ($validated['detalles'] as $detalle) {
            if ($detalle['monto'] > 0) {
                SobreDetalle::create([
                    'sobre_id' => $sobre->id,
                    'categoria' => $detalle['categoria'],
                    'monto' => $detalle['monto'],
                ]);
            }
        }

        // Recalcular totales
        $this->calculoService->recalcular($sobre->culto);

        return redirect()->route('recuento.index', ['culto_id' => $sobre->culto_id])
            ->with('success', 'Sobre actualizado correctamente.');
    }

    public function destroy(Sobre $sobre)
    {
        $cultoId = $sobre->culto_id;
        $sobre->delete();

        // Recalcular totales
        $culto = Culto::find($cultoId);
        if ($culto) {
            $this->calculoService->recalcular($culto);
        }

        return redirect()->route('recuento.index', ['culto_id' => $cultoId])
            ->with('success', 'Sobre eliminado correctamente.');
    }
}

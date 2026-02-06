<?php

namespace App\Http\Controllers;

use App\Models\ClaseAsistencia;
use App\Models\Culto;
use App\Models\Persona;
use App\Models\Sobre;
use App\Models\SobreDetalle;
use App\Models\OfrendaSuelta;
use App\Services\CalculoTotalesCultoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RecuentoClasesController extends Controller
{
    protected $calculoService;

    public function __construct(CalculoTotalesCultoService $calculoService)
    {
        $this->calculoService = $calculoService;
    }

    public function index(Request $request)
    {
        $cultoId = $request->get('culto_id');
        $claseId = $request->get('clase_id');

        // Cultos no cerrados para el selector
        $cultos = Culto::where('cerrado', false)->orderBy('fecha', 'desc')->get();

        // Clases activas
        $clases = ClaseAsistencia::where('activa', true)->orderBy('orden')->get();

        $cultoSeleccionado = $cultoId ? Culto::find($cultoId) : null;
        $claseSeleccionada = $claseId ? ClaseAsistencia::find($claseId) : null;

        // Sobres filtrados por culto y clase
        $sobres = collect();
        $ofrendasSueltas = collect();
        $resumen = null;

        if ($cultoId && $claseId) {
            $sobres = Sobre::where('culto_id', $cultoId)
                ->where('clase_id', $claseId)
                ->with(['persona', 'detalles', 'clase'])
                ->get();

            $ofrendasSueltas = OfrendaSuelta::where('culto_id', $cultoId)
                ->where('clase_id', $claseId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Calcular resumen de la clase
            $totalSuelto = $ofrendasSueltas->sum('monto');
            $resumen = [
                'total' => $sobres->sum('total_declarado') + $totalSuelto,
                'cantidad_sobres' => $sobres->count(),
                'efectivo' => $sobres->where('metodo_pago', 'efectivo')->count(),
                'transferencias' => $sobres->where('metodo_pago', 'transferencia')->count(),
            ];
        }

        return view('recuento-clases.index', compact(
            'cultos',
            'clases',
            'cultoSeleccionado',
            'claseSeleccionada',
            'sobres',
            'ofrendasSueltas',
            'resumen'
        ));
    }

    public function create(Request $request)
    {
        $cultoId = $request->get('culto_id');
        $claseId = $request->get('clase_id');

        $culto = $cultoId ? Culto::findOrFail($cultoId) : null;
        $clasePreseleccionada = $claseId ? ClaseAsistencia::find($claseId) : null;
        $personas = Persona::where('activo', true)->orderBy('nombre')->get();
        $clases = ClaseAsistencia::where('activa', true)->orderBy('orden')->get();

        return view('recuento-clases.create', compact('culto', 'personas', 'clases', 'clasePreseleccionada'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'culto_id' => 'required|exists:cultos,id',
            'clase_id' => 'required|exists:clases_asistencia,id',
            'persona_id' => 'nullable|exists:personas,id',
            'metodo_pago' => 'required|in:efectivo,transferencia',
            'comprobante_numero' => 'nullable|string|max:100',
            'notas' => 'nullable|string',
            'detalles' => 'required|array',
            'detalles.*.categoria' => 'required|string',
            'detalles.*.monto' => 'required|numeric|min:0',
        ]);

        if ($validated['metodo_pago'] === 'transferencia') {
            $request->validate([
                'comprobante_numero' => 'required|string|max:100',
            ]);
        }

        // Verificar que el culto no esté cerrado
        $culto = Culto::findOrFail($validated['culto_id']);
        if ($culto->cerrado) {
            return redirect()->route('recuento-clases.index', [
                'culto_id' => $culto->id,
                'clase_id' => $validated['clase_id']
            ])->with('error', 'No se pueden agregar sobres a un culto cerrado.');
        }

        // Calcular total declarado
        $totalDeclarado = collect($validated['detalles'])->sum('monto');

        $sobre = Sobre::create([
            'culto_id' => $validated['culto_id'],
            'clase_id' => $validated['clase_id'],
            'persona_id' => $validated['persona_id'] ?? null,
            'metodo_pago' => $validated['metodo_pago'],
            'comprobante_numero' => $validated['comprobante_numero'] ?? null,
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

        return redirect()->route('recuento-clases.index', [
            'culto_id' => $validated['culto_id'],
            'clase_id' => $validated['clase_id']
        ])->with('success', 'Sobre de clase registrado correctamente.');
    }

    public function edit(Sobre $sobre)
    {
        $user = auth()->user();
        $isAdmin = $user && method_exists($user, 'isAdmin') ? $user->isAdmin() : ($user && $user->rol === 'admin');

        if ($sobre->culto->cerrado && !$isAdmin) {
            return redirect()->route('recuento-clases.index', [
                'culto_id' => $sobre->culto_id,
                'clase_id' => $sobre->clase_id
            ])->with('error', 'No se puede editar un sobre de un culto cerrado.');
        }

        $sobre->load(['detalles', 'culto', 'clase']);
        $personas = Persona::where('activo', true)->orderBy('nombre')->get();
        $clases = ClaseAsistencia::where('activa', true)->orderBy('orden')->get();

        return view('recuento-clases.edit', compact('sobre', 'personas', 'clases'));
    }

    public function update(Request $request, Sobre $sobre)
    {
        $user = auth()->user();
        $isAdmin = $user && method_exists($user, 'isAdmin') ? $user->isAdmin() : ($user && $user->rol === 'admin');

        if ($sobre->culto->cerrado && !$isAdmin) {
            return redirect()->route('recuento-clases.index', [
                'culto_id' => $sobre->culto_id,
                'clase_id' => $sobre->clase_id
            ])->with('error', 'No se puede editar un sobre de un culto cerrado.');
        }

        $validated = $request->validate([
            'clase_id' => 'required|exists:clases_asistencia,id',
            'persona_id' => 'nullable|exists:personas,id',
            'metodo_pago' => 'required|in:efectivo,transferencia',
            'comprobante_numero' => 'nullable|string|max:100',
            'notas' => 'nullable|string',
            'detalles' => 'required|array',
            'detalles.*.categoria' => 'required|string',
            'detalles.*.monto' => 'required|numeric|min:0',
        ]);

        if ($validated['metodo_pago'] === 'transferencia') {
            $request->validate([
                'comprobante_numero' => 'required|string|max:100',
            ]);
        }

        $totalDeclarado = collect($validated['detalles'])->sum('monto');

        $sobre->update([
            'clase_id' => $validated['clase_id'],
            'persona_id' => $validated['persona_id'] ?? null,
            'metodo_pago' => $validated['metodo_pago'],
            'comprobante_numero' => $validated['comprobante_numero'] ?? null,
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

        return redirect()->route('recuento-clases.index', [
            'culto_id' => $sobre->culto_id,
            'clase_id' => $sobre->clase_id
        ])->with('success', 'Sobre de clase actualizado correctamente.');
    }

    public function destroy(Sobre $sobre)
    {
        $currentUser = auth()->user();
        if (!$currentUser || !in_array($currentUser->rol, ['admin', 'tesorero'])) {
            return redirect()->route('recuento-clases.index', [
                'culto_id' => $sobre->culto_id,
                'clase_id' => $sobre->clase_id
            ])->with('error', 'No tienes permiso para eliminar sobres.');
        }

        if ($sobre->culto->cerrado) {
            return redirect()->route('recuento-clases.index', [
                'culto_id' => $sobre->culto_id,
                'clase_id' => $sobre->clase_id
            ])->with('error', 'No se puede eliminar un sobre de un culto cerrado.');
        }

        $cultoId = $sobre->culto_id;
        $claseId = $sobre->clase_id;
        $sobreId = $sobre->id;
        $metodo = $sobre->metodo_pago;
        $comprobante = $sobre->comprobante_numero;
        $totalDeclarado = $sobre->total_declarado;
        $sobre->delete();

        // Recalcular totales
        $culto = Culto::find($cultoId);
        if ($culto) {
            $this->calculoService->recalcular($culto);
        }

        return redirect()->route('recuento-clases.index', [
            'culto_id' => $cultoId,
            'clase_id' => $claseId
        ])->with('success', 'Sobre de clase eliminado correctamente.');
    }

    public function storeSuelto(Request $request)
    {
        $validated = $request->validate([
            'culto_id' => 'required|exists:cultos,id',
            'clase_id' => 'required|exists:clases_asistencia,id',
            'monto' => 'required|numeric|min:0.01',
            'descripcion' => 'nullable|string|max:500',
        ]);

        // Verificar que el culto no esté cerrado
        $culto = Culto::findOrFail($validated['culto_id']);
        if ($culto->cerrado) {
            return redirect()->route('recuento-clases.index', [
                'culto_id' => $culto->id,
                'clase_id' => $validated['clase_id']
            ])->with('error', 'No se puede agregar dinero suelto a un culto cerrado.');
        }

        OfrendaSuelta::create([
            'culto_id' => $validated['culto_id'],
            'clase_id' => $validated['clase_id'],
            'monto' => $validated['monto'],
            'metodo_pago' => 'efectivo', // Dinero suelto siempre es efectivo
            'descripcion' => $validated['descripcion'] ?? null,
        ]);

        // Recalcular totales
        if ($culto) {
            $this->calculoService->recalcular($culto);
        }

        return redirect()->route('recuento-clases.index', [
            'culto_id' => $validated['culto_id'],
            'clase_id' => $validated['clase_id']
        ])->with('success', 'Dinero suelto registrado correctamente.');
    }

    public function editSuelto(OfrendaSuelta $suelto)
    {
        if ($suelto->culto->cerrado) {
            return redirect()->route('recuento-clases.index', [
                'culto_id' => $suelto->culto_id,
                'clase_id' => $suelto->clase_id
            ])->with('error', 'No se puede editar dinero suelto de un culto cerrado.');
        }

        return response()->json($suelto);
    }

    public function updateSuelto(Request $request, OfrendaSuelta $suelto)
    {
        if ($suelto->culto->cerrado) {
            return redirect()->route('recuento-clases.index', [
                'culto_id' => $suelto->culto_id,
                'clase_id' => $suelto->clase_id
            ])->with('error', 'No se puede editar dinero suelto de un culto cerrado.');
        }

        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $suelto->update([
            'monto' => $validated['monto'],
            'descripcion' => $validated['descripcion'] ?? null,
        ]);

        // Recalcular totales
        $culto = Culto::find($suelto->culto_id);
        if ($culto) {
            $this->calculoService->recalcular($culto);
        }

        return redirect()->route('recuento-clases.index', [
            'culto_id' => $suelto->culto_id,
            'clase_id' => $suelto->clase_id
        ])->with('success', 'Dinero suelto actualizado correctamente.');
    }

    public function destroySuelto(OfrendaSuelta $suelto)
    {
        // Solo admin y tesorero pueden eliminar dinero suelto
        if (!in_array(auth()->user()->rol, ['admin', 'tesorero'])) {
            return redirect()->route('recuento-clases.index', [
                'culto_id' => $suelto->culto_id,
                'clase_id' => $suelto->clase_id
            ])->with('error', 'No tienes permiso para eliminar dinero suelto.');
        }

        if ($suelto->culto->cerrado) {
            return redirect()->route('recuento-clases.index', [
                'culto_id' => $suelto->culto_id,
                'clase_id' => $suelto->clase_id
            ])->with('error', 'No se puede eliminar dinero suelto de un culto cerrado.');
        }

        $cultoId = $suelto->culto_id;
        $claseId = $suelto->clase_id;
        $suelto->delete();

        // Recalcular totales
        $culto = Culto::find($cultoId);
        if ($culto) {
            $this->calculoService->recalcular($culto);
        }

        return redirect()->route('recuento-clases.index', [
            'culto_id' => $cultoId,
            'clase_id' => $claseId
        ])->with('success', 'Dinero suelto eliminado correctamente.');
    }

    public function pdfClase(Culto $culto, ClaseAsistencia $clase)
    {
        $sobres = Sobre::where('culto_id', $culto->id)
            ->where('clase_id', $clase->id)
            ->with(['persona', 'detalles'])
            ->get();

        $ofrendasSueltas = OfrendaSuelta::where('culto_id', $culto->id)
            ->where('clase_id', $clase->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calcular totales por categoría
        $totalesPorCategoria = [
            'diezmo' => 0,
            'ofrenda_especial' => 0,
            'misiones' => 0,
            'seminario' => 0,
            'campamento' => 0,
            'pro_templo' => 0,
        ];

        foreach ($sobres as $sobre) {
            foreach ($sobre->detalles as $detalle) {
                if (isset($totalesPorCategoria[$detalle->categoria])) {
                    $totalesPorCategoria[$detalle->categoria] += $detalle->monto;
                }
            }
        }

        $totalSuelto = $ofrendasSueltas->sum('monto');

        $resumen = [
            'total' => $sobres->sum('total_declarado') + $totalSuelto,
            'cantidad_sobres' => $sobres->count(),
            'efectivo' => $sobres->where('metodo_pago', 'efectivo')->sum('total_declarado') + $totalSuelto,
            'transferencias' => $sobres->where('metodo_pago', 'transferencia')->sum('total_declarado'),
            'cantidad_efectivo' => $sobres->where('metodo_pago', 'efectivo')->count(),
            'cantidad_transferencias' => $sobres->where('metodo_pago', 'transferencia')->count(),
        ];

        $pdf = Pdf::loadView('pdfs.recuento-clase', compact('culto', 'clase', 'sobres', 'ofrendasSueltas', 'totalesPorCategoria', 'resumen'));
        $pdf->setPaper('A4', 'landscape');

        $filename = 'recuento-clase-' . $clase->slug . '-' . $culto->fecha->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }
}

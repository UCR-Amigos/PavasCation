<?php

namespace App\Http\Controllers;

use App\Models\ClaseAsistencia;
use App\Models\Culto;
use App\Models\Persona;
use App\Models\Sobre;
use App\Models\SobreDetalle;
use App\Models\AuditLog;
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
        $resumen = null;

        if ($cultoId && $claseId) {
            $sobres = Sobre::where('culto_id', $cultoId)
                ->where('clase_id', $claseId)
                ->with(['persona', 'detalles', 'clase'])
                ->get();

            // Calcular resumen de la clase
            $resumen = [
                'total' => $sobres->sum('total_declarado'),
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

        // Auditoría
        $user = $request->user();
        AuditLog::create([
            'user_id' => $user->id ?? null,
            'user_name' => $user->name ?? ($user->nombre ?? null),
            'user_email' => $user->email ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            'method' => 'POST',
            'route' => 'recuento-clases.store',
            'action' => 'Agregar Sobre de Clase',
            'details' => json_encode([
                'culto_id' => $sobre->culto_id,
                'clase_id' => $sobre->clase_id,
                'sobre_id' => $sobre->id,
                'metodo_pago' => $sobre->metodo_pago,
                'comprobante_numero' => $sobre->comprobante_numero,
                'total_declarado' => $sobre->total_declarado,
            ]),
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

        // Auditoría
        $user = $request->user();
        AuditLog::create([
            'user_id' => $user->id ?? null,
            'user_name' => $user->name ?? ($user->nombre ?? null),
            'user_email' => $user->email ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            'method' => 'PUT',
            'route' => 'recuento-clases.update',
            'action' => 'Editar Sobre de Clase',
            'details' => json_encode([
                'culto_id' => $sobre->culto_id,
                'clase_id' => $sobre->clase_id,
                'sobre_id' => $sobre->id,
                'metodo_pago' => $sobre->metodo_pago,
                'comprobante_numero' => $sobre->comprobante_numero,
                'total_declarado' => $sobre->total_declarado,
            ]),
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

        // Auditoría
        AuditLog::create([
            'user_id' => $currentUser->id ?? null,
            'user_name' => $currentUser->name ?? ($currentUser->nombre ?? null),
            'user_email' => $currentUser->email ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 255),
            'method' => 'DELETE',
            'route' => 'recuento-clases.destroy',
            'action' => 'Eliminar Sobre de Clase',
            'details' => json_encode([
                'culto_id' => $cultoId,
                'clase_id' => $claseId,
                'sobre_id' => $sobreId,
                'metodo_pago' => $metodo,
                'comprobante_numero' => $comprobante,
                'total_declarado' => $totalDeclarado,
            ]),
        ]);

        return redirect()->route('recuento-clases.index', [
            'culto_id' => $cultoId,
            'clase_id' => $claseId
        ])->with('success', 'Sobre de clase eliminado correctamente.');
    }

    public function pdfClase(Culto $culto, ClaseAsistencia $clase)
    {
        $sobres = Sobre::where('culto_id', $culto->id)
            ->where('clase_id', $clase->id)
            ->with(['persona', 'detalles'])
            ->get();

        // Calcular totales por categoría
        $totalesPorCategoria = [
            'diezmo' => 0,
            'ofrenda_especial' => 0,
            'misiones' => 0,
            'seminario' => 0,
            'campa' => 0,
            'prestamo' => 0,
            'construccion' => 0,
            'micro' => 0,
        ];

        foreach ($sobres as $sobre) {
            foreach ($sobre->detalles as $detalle) {
                if (isset($totalesPorCategoria[$detalle->categoria])) {
                    $totalesPorCategoria[$detalle->categoria] += $detalle->monto;
                }
            }
        }

        $resumen = [
            'total' => $sobres->sum('total_declarado'),
            'cantidad_sobres' => $sobres->count(),
            'efectivo' => $sobres->where('metodo_pago', 'efectivo')->sum('total_declarado'),
            'transferencias' => $sobres->where('metodo_pago', 'transferencia')->sum('total_declarado'),
            'cantidad_efectivo' => $sobres->where('metodo_pago', 'efectivo')->count(),
            'cantidad_transferencias' => $sobres->where('metodo_pago', 'transferencia')->count(),
        ];

        $pdf = Pdf::loadView('pdfs.recuento-clase', compact('culto', 'clase', 'sobres', 'totalesPorCategoria', 'resumen'));
        $pdf->setPaper('A4', 'landscape');

        $filename = 'recuento-clase-' . $clase->slug . '-' . $culto->fecha->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }
}

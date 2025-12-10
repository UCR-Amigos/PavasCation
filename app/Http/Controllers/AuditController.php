<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAdmin($request);

        $query = AuditLog::query()->latest();

        if ($email = $request->get('email')) {
            $query->where('user_email', 'like', "%$email%");
        }
        if ($method = $request->get('method')) {
            $query->where('method', strtoupper($method));
        }
        if ($route = $request->get('route')) {
            $query->where('route', 'like', "%$route%");
        }
        if ($ip = $request->get('ip')) {
            $query->where('ip_address', 'like', "%$ip%");
        }
        if ($from = $request->get('from')) {
            $query->where('created_at', '>=', $from.' 00:00:00');
        }
        if ($to = $request->get('to')) {
            $query->where('created_at', '<=', $to.' 23:59:59');
        }

        $logs = $query->paginate(20)->appends($request->query());

        return view('admin.auditoria.index', [
            'logs' => $logs,
        ]);
    }

    public function show(Request $request, AuditLog $log)
    {
        $this->authorizeAdmin($request);
        return view('admin.auditoria.show', [
            'log' => $log,
        ]);
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user() && $request->user()->isAdmin(), 403);
    }
}

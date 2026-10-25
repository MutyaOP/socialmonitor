<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Services\MonitoringService;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        $items = Alert::with('category')
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->level, fn($q, $v) => $q->where('level', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('alerts.index', compact('items'));
    }

    public function scan(MonitoringService $monitoring)
    {
        $n = $monitoring->scan();

        return back()->with('success', $n ? "Мониторинг аяқталды: {$n} ескерту жаңартылды." : 'Мониторинг аяқталды: күрт өсу байқалмады.');
    }

    public function close(Alert $alert)
    {
        $alert->update(['status' => 'closed']);

        return back()->with('success', 'Ескерту жабылды.');
    }
}

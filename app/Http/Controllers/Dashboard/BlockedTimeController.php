<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BlockedTime;
use App\Services\BarberService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlockedTimeController extends Controller
{
    protected $barberService;

    public function __construct(BarberService $barberService)
    {
        $this->barberService = $barberService;
    }

    /**
     * Display a listing of the blocked times.
     */
    public function index(): View
    {
        // Trazer bloqueios a partir de hoje
        $blockedTimes = BlockedTime::with('barber')
            ->where('date', '>=', date('Y-m-d'))
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
            
        $barbers = $this->barberService->getAllBarbers()->where('ativo', true);

        return view('blocked-times.index', compact('blockedTimes', 'barbers'));
    }

    /**
     * Store a newly created blocked time.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'barber_id' => 'nullable|exists:barbers,id',
            'reason' => 'nullable|string|max:255'
        ]);

        BlockedTime::create([
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'barber_id' => $request->barber_id,
            'reason' => $request->reason
        ]);

        return redirect()->route('blocked-times.index')->with('success', 'Período bloqueado com sucesso!');
    }

    /**
     * Remove the specified blocked time.
     */
    public function destroy($id)
    {
        $blockedTime = BlockedTime::findOrFail($id);
        $blockedTime->delete();

        return redirect()->route('blocked-times.index')->with('success', 'Bloqueio removido com sucesso!');
    }
}

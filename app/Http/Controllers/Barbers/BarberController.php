<?php

namespace App\Http\Controllers\Barbers;

use App\Http\Controllers\Controller;
use App\Services\BarberService;
use Illuminate\Http\Request;

class BarberController extends Controller
{
    protected $barberService;

    public function __construct(BarberService $barberService)
    {
        $this->barberService = $barberService;
    }

    public function index()
    {
        $barbers = $this->barberService->getAllBarbers();
        return view('barbers.index', compact('barbers'));
    }

    public function create()
    {
        return view('barbers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:barbers,email',
            'foto' => 'nullable|image|max:2048'
        ]);

        $this->barberService->createBarber($request->all(), $request->file('foto'));

        return redirect()->route('barbers.index')->with('success', 'Barbeiro cadastrado com sucesso!');
    }

    public function edit($id)
    {
        $barber = $this->barberService->getBarber($id);
        return view('barbers.edit', compact('barber'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:barbers,email,'.$id,
            'foto' => 'nullable|image|max:2048'
        ]);

        $this->barberService->updateBarber($id, $request->all(), $request->file('foto'));

        return redirect()->route('barbers.index')->with('success', 'Barbeiro atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $this->barberService->deleteBarber($id);
        return redirect()->route('barbers.index')->with('success', 'Barbeiro excluído com sucesso!');
    }
}

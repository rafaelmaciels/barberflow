<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Services\ServiceCatalogService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected $serviceCatalog;

    public function __construct(ServiceCatalogService $serviceCatalog)
    {
        $this->serviceCatalog = $serviceCatalog;
    }

    public function index()
    {
        $services = $this->serviceCatalog->getAllServices();
        return view('services.index', compact('services'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'duracao' => 'required|integer|min:1',
            'valor' => 'required|numeric|min:0'
        ]);

        $this->serviceCatalog->createService($request->all());

        return redirect()->route('services.index')->with('success', 'Serviço cadastrado com sucesso!');
    }

    public function edit($id)
    {
        $service = $this->serviceCatalog->getService($id);
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'duracao' => 'required|integer|min:1',
            'valor' => 'required|numeric|min:0'
        ]);

        $this->serviceCatalog->updateService($id, $request->all());

        return redirect()->route('services.index')->with('success', 'Serviço atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $this->serviceCatalog->deleteService($id);
        return redirect()->route('services.index')->with('success', 'Serviço excluído com sucesso!');
    }
}

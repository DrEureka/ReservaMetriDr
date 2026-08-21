<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMesaRequest;
use App\Http\Requests\UpdateMesaRequest;
use App\Models\Mesa;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MesaController extends Controller
{
    public function index(): View
    {
        $mesas = Mesa::orderBy('ubicacion')
            ->orderBy('numero')
            ->paginate(15)
            ->withQueryString();

        return view('admin.mesas.index', [
            'mesas' => $mesas,
            'ubicaciones' => Mesa::UBICACIONES,
        ]);
    }

    public function create(): View
    {
        return view('admin.mesas.create', [
            'ubicaciones' => Mesa::UBICACIONES,
        ]);
    }

    public function store(StoreMesaRequest $solicitud): RedirectResponse
    {
        Mesa::create($solicitud->validated());

        return redirect()
            ->route('admin.mesas.index')
            ->with('exito', 'Mesa creada correctamente.');
    }

    public function edit(Mesa $mesa): View
    {
        return view('admin.mesas.edit', [
            'mesa' => $mesa,
            'ubicaciones' => Mesa::UBICACIONES,
        ]);
    }

    public function update(UpdateMesaRequest $solicitud, Mesa $mesa): RedirectResponse
    {
        $mesa->update($solicitud->validated());

        return redirect()
            ->route('admin.mesas.index')
            ->with('exito', 'Mesa actualizada correctamente.');
    }

    public function destroy(Mesa $mesa): RedirectResponse
    {
        $mesa->delete();

        return redirect()
            ->route('admin.mesas.index')
            ->with('exito', 'Mesa eliminada correctamente.');
    }
}

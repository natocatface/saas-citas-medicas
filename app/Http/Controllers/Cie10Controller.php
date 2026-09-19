<?php

namespace App\Http\Controllers;

use App\Models\Cie10;
use Illuminate\Http\Request;

class Cie10Controller extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->trim();
        $items = Cie10::query()
            ->when($q->isNotEmpty(), fn ($w) => $w->where('codigo', 'like', "%{$q}%")->orWhere('descripcion', 'like', "%{$q}%"))
            ->orderBy('codigo')
            ->paginate(15)->withQueryString();
        return view('cie10.index', compact('items', 'q'));
    }

    public function create()
    {
        return view('cie10.create', ['item' => new Cie10(['activo' => true])]);
    }

    public function store(Request $request)
    {
        Cie10::create($this->validar($request));
        return redirect()->route('cie10.index')->with('success', 'Diagnóstico CIE-10 creado.');
    }

    public function edit(Cie10 $cie10)
    {
        return view('cie10.edit', ['item' => $cie10]);
    }

    public function update(Request $request, Cie10 $cie10)
    {
        $cie10->update($this->validar($request));
        return redirect()->route('cie10.index')->with('success', 'Diagnóstico actualizado.');
    }

    public function destroy(Cie10 $cie10)
    {
        $cie10->delete();
        return redirect()->route('cie10.index')->with('success', 'Diagnóstico eliminado.');
    }

    private function validar(Request $request): array
    {
        $v = $request->validate([
            'codigo' => ['required', 'string', 'max:20'],
            'descripcion' => ['required', 'string', 'max:255'],
            'categoria' => ['nullable', 'string', 'max:120'],
            'activo' => ['nullable', 'boolean'],
        ]);
        $v['activo'] = $request->boolean('activo');
        return $v;
    }
}

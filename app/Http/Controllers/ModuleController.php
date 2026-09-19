<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function show(Request $request, string $modulo)
    {
        $titulos = config('modulos.titulos', []);
        $titulo = $titulos[$modulo] ?? ucfirst($modulo);
        return view('modules.placeholder', compact('titulo', 'modulo'));
    }
}

<?php

namespace App\Http\Controllers\SaaS;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Clinica;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->trim();
        $clinicaId = $request->integer('clinica_id');

        $registros = Bitacora::query()
            ->when($q->isNotEmpty(), fn ($w) => $w->where('usuario_nombre', 'like', "%{$q}%")->orWhere('accion', 'like', "%{$q}%"))
            ->when($clinicaId, fn ($w) => $w->where('clinica_id', $clinicaId))
            ->latest('created_at')
            ->paginate(25)->withQueryString();

        return view('saas.bitacora', [
            'registros' => $registros, 'q' => $q, 'clinicaSel' => $clinicaId,
            'clinicas' => Clinica::orderBy('nombre')->get(),
        ]);
    }
}

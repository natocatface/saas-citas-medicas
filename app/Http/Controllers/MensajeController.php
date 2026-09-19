<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    public function index(Request $request)
    {
        $box = $request->string('box')->trim()->value() === 'enviados' ? 'enviados' : 'recibidos';
        $uid = $request->user()->id;

        $mensajes = Mensaje::query()
            ->with(['remitente', 'destinatario'])
            ->when($box === 'recibidos', fn ($w) => $w->where('destinatario_id', $uid))
            ->when($box === 'enviados', fn ($w) => $w->where('remitente_id', $uid))
            ->latest()
            ->paginate(15)->withQueryString();

        $noLeidos = Mensaje::where('destinatario_id', $uid)->where('leido', false)->count();

        return view('mensajes.index', compact('mensajes', 'box', 'noLeidos'));
    }

    public function create(Request $request)
    {
        return view('mensajes.create', [
            'usuarios' => User::where('clinica_id', $request->user()->clinica_id)
                ->where('id', '!=', $request->user()->id)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'destinatario_id' => ['required', 'exists:users,id'],
            'asunto' => ['required', 'string', 'max:160'],
            'cuerpo' => ['required', 'string', 'max:4000'],
        ]);
        $data['remitente_id'] = $request->user()->id;
        Mensaje::create($data);
        return redirect()->route('mensajes.index')->with('success', 'Mensaje enviado.');
    }

    public function show(Request $request, Mensaje $mensaje)
    {
        if ($mensaje->destinatario_id === $request->user()->id && ! $mensaje->leido) {
            $mensaje->update(['leido' => true, 'leido_at' => now()]);
        }
        $mensaje->load(['remitente', 'destinatario']);
        return view('mensajes.show', compact('mensaje'));
    }

    public function destroy(Mensaje $mensaje)
    {
        $mensaje->delete();
        return redirect()->route('mensajes.index')->with('success', 'Mensaje eliminado.');
    }
}

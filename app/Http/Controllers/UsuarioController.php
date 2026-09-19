<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public const ROLES = ['admin' => 'Administrador', 'medico' => 'Médico', 'recepcion' => 'Recepción'];

    public function index(Request $request)
    {
        $q = $request->string('q')->trim();
        $rol = $request->string('rol')->trim();

        $usuarios = User::query()
            ->where('clinica_id', $request->user()->clinica_id)
            ->where('rol', '!=', 'superadmin')
            ->when($q->isNotEmpty(), fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"))
            ->when($rol->isNotEmpty(), fn ($w) => $w->where('rol', (string) $rol))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('usuarios.index', [
            'usuarios' => $usuarios,
            'q' => $q,
            'rolSel' => (string) $rol,
            'roles' => self::ROLES,
        ]);
    }

    public function create()
    {
        return view('usuarios.create', ['usuario' => new User(['rol' => 'recepcion', 'activo' => true]), 'roles' => self::ROLES]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'rol' => ['required', Rule::in(array_keys(self::ROLES))],
            'telefono' => ['nullable', 'string', 'max:30'],
            'activo' => ['nullable', 'boolean'],
        ]);
        $data['activo'] = $request->boolean('activo');
        $data['clinica_id'] = $request->user()->clinica_id;
        User::create($data);
        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return view('usuarios.edit', ['usuario' => $usuario, 'roles' => self::ROLES]);
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'rol' => ['required', Rule::in(array_keys(self::ROLES))],
            'telefono' => ['nullable', 'string', 'max:30'],
            'activo' => ['nullable', 'boolean'],
        ]);
        $data['activo'] = $request->boolean('activo');
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $usuario->update($data);
        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, User $usuario)
    {
        if ($request->user()->id === $usuario->id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado.');
    }
}

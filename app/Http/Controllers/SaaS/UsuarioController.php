<?php

namespace App\Http\Controllers\SaaS;

use App\Http\Controllers\Controller;
use App\Models\Clinica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public const ROLES = ['superadmin' => 'Super Admin', 'admin' => 'Administrador', 'medico' => 'Médico', 'recepcion' => 'Recepción'];

    public function index(Request $request)
    {
        $q = $request->string('q')->trim();
        $rol = $request->string('rol')->trim();
        $clinicaId = $request->integer('clinica_id');

        $usuarios = User::query()
            ->with('clinica')
            ->when($q->isNotEmpty(), fn ($w) => $w->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"))
            ->when($rol->isNotEmpty(), fn ($w) => $w->where('rol', (string) $rol))
            ->when($clinicaId, fn ($w) => $w->where('clinica_id', $clinicaId))
            ->orderBy('name')
            ->paginate(15)->withQueryString();

        return view('saas.usuarios.index', [
            'usuarios' => $usuarios, 'q' => $q, 'rolSel' => (string) $rol, 'clinicaSel' => $clinicaId,
            'roles' => self::ROLES, 'clinicas' => Clinica::orderBy('nombre')->get(),
        ]);
    }

    public function create()
    {
        return view('saas.usuarios.create', [
            'usuario' => new User(['rol' => 'admin', 'activo' => true]),
            'roles' => self::ROLES, 'clinicas' => Clinica::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'rol' => ['required', Rule::in(array_keys(self::ROLES))],
            'clinica_id' => ['nullable', 'exists:clinicas,id'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'activo' => ['nullable', 'boolean'],
        ]);
        $data['activo'] = $request->boolean('activo');
        if ($data['rol'] === 'superadmin') {
            $data['clinica_id'] = null;
        }
        User::create($data);
        return redirect()->route('saas.usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return view('saas.usuarios.edit', [
            'usuario' => $usuario, 'roles' => self::ROLES, 'clinicas' => Clinica::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'rol' => ['required', Rule::in(array_keys(self::ROLES))],
            'clinica_id' => ['nullable', 'exists:clinicas,id'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'activo' => ['nullable', 'boolean'],
        ]);
        $data['activo'] = $request->boolean('activo');
        if ($data['rol'] === 'superadmin') {
            $data['clinica_id'] = null;
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $usuario->update($data);
        return redirect()->route('saas.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, User $usuario)
    {
        if ($request->user()->id === $usuario->id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        $usuario->delete();
        return redirect()->route('saas.usuarios.index')->with('success', 'Usuario eliminado.');
    }
}

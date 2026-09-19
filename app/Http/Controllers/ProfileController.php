<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('perfil.edit', ['usuario' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'telefono' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $user->update($data);
        return redirect()->route('perfil.edit')->with('success', 'Perfil actualizado correctamente.');
    }
}

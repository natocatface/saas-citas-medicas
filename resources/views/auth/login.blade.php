<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · CitasMédicas</title>
    @include('partials.assets')
</head>
<body class="h-full bg-slate-100">
<div class="min-h-full lg:grid lg:grid-cols-2">

    {{-- Panel izquierdo (marca) --}}
    <div class="hidden lg:flex flex-col justify-between brand-bg text-white p-12 relative overflow-hidden">
        <a href="{{ route('home') }}" class="flex items-center gap-3 relative">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-cyanx-500 to-brand-600 flex items-center justify-center shadow-lg shadow-cyan-500/20">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m9-9H3"/><circle cx="12" cy="12" r="9" stroke-width="1.6"/></svg>
            </div>
            <div>
                <p class="text-xl font-bold tracking-tight">CitasMédicas</p>
                <p class="text-[11px] tracking-[0.2em] text-cyan-200/80 uppercase">Gestión clínica premium</p>
            </div>
        </a>

        <div class="relative max-w-md">
            <h2 class="text-3xl font-bold leading-snug">Tu clínica, <span class="grad-text">bajo control</span></h2>
            <p class="mt-3 text-sm text-slate-300/90">
                <span class="font-semibold text-white">Aplicativo SaaS</span> para la gestión integral de citas médicas.
            </p>
            <div class="mt-8 space-y-5">
                @php
                    $features = [
                        ['Gestión de pacientes','Historial, datos y aseguradora de cada paciente.','M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z'],
                        ['Citas y calendario','Programa, confirma y da seguimiento a cada cita.','M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 6h15a.75.75 0 0 1 .75.75v12.75a.75.75 0 0 1-.75.75h-15a.75.75 0 0 1-.75-.75V6.75A.75.75 0 0 1 4.5 6Z'],
                        ['Recetas y facturación','Emite recetas y comprobantes en segundos.','M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z'],
                        ['Reportes en tiempo real','Métricas y estadísticas para decidir mejor.','M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z'],
                    ];
                @endphp
                @foreach($features as $f)
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-white/10 ring-1 ring-white/15 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-cyan-200" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $f[2] }}"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-white text-sm">{{ $f[0] }}</p>
                            <p class="text-xs text-slate-300/80">{{ $f[1] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="relative grid grid-cols-3 gap-3">
            @foreach([['+500','Clínicas'],['99.9%','Uptime'],['24/7','Soporte']] as $s)
                <div class="bg-white/5 ring-1 ring-white/10 rounded-xl px-3 py-3 text-center backdrop-blur">
                    <p class="text-xl font-black text-white">{{ $s[0] }}</p>
                    <p class="text-[11px] text-slate-400">{{ $s[1] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Panel derecho (formulario) --}}
    <div class="flex items-center justify-center p-6 sm:p-12 min-h-screen lg:min-h-full bg-white">
        <div class="w-full max-w-sm">
            <a href="{{ route('home') }}" class="lg:hidden flex items-center gap-2.5 mb-8 justify-center">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyanx-500 to-brand-600 text-white flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m9-9H3"/></svg>
                </div>
                <span class="text-lg font-bold text-slate-800">CitasMédicas</span>
            </a>

            <h1 class="text-2xl font-bold text-slate-800">Bienvenido de vuelta 👋</h1>
            <p class="text-sm text-slate-500 mt-1">Ingresa tus credenciales para acceder al sistema.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="mt-6 space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Correo electrónico</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email', 'admin@citasmedicas.test') }}" required autofocus
                               class="w-full pl-10 rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="tucorreo@ejemplo.com">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Contraseña</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                        </span>
                        <input type="password" name="password" id="password" required
                               class="w-full pl-10 rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="••••••••">
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        Recordarme
                    </label>
                    <a href="#" class="text-sm font-medium text-brand-600 hover:text-brand-700">¿Olvidaste tu contraseña?</a>
                </div>
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-cyanx-500 to-brand-600 hover:opacity-95 text-white font-semibold text-sm rounded-lg py-3 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
                    Iniciar sesión
                </button>
            </form>

            {{-- Cuentas de demostración --}}
            <div class="mt-7">
                <div class="relative text-center">
                    <span class="bg-white px-3 text-xs text-slate-400 relative z-10">Cuentas de demostración</span>
                    <span class="absolute inset-x-0 top-1/2 h-px bg-slate-200"></span>
                </div>
                <div class="mt-4 rounded-xl border border-slate-200 divide-y divide-slate-100 overflow-hidden">
                    @php
                        $demos = [
                            ['admin@citasmedicas.test','Admin','bg-violet-100 text-violet-700'],
                            ['recepcion@citasmedicas.test','Recepción','bg-amber-100 text-amber-700'],
                        ];
                    @endphp
                    @foreach($demos as $d)
                        <button type="button" onclick="fillLogin('{{ $d[0] }}')"
                                class="w-full flex items-center justify-between px-4 py-3 hover:bg-slate-50 transition text-left">
                            <span class="text-sm text-slate-600 font-mono">{{ $d[0] }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $d[2] }}">{{ $d[1] }}</span>
                        </button>
                    @endforeach
                </div>
                <p class="mt-2 text-center text-xs text-slate-400">Contraseña para todas: <span class="font-mono text-slate-600">password</span></p>
            </div>

            <p class="mt-6 text-center text-xs text-slate-400">© {{ date('Y') }} CitasMédicas · Todos los derechos reservados</p>
        </div>
    </div>
</div>

<script>
    function fillLogin(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'password';
        document.getElementById('password').focus();
    }
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CitasMédicas · Software de gestión de citas médicas</title>
    @include('partials.assets')
</head>
<body class="bg-white text-slate-700 antialiased">

{{-- ===================== NAVBAR ===================== --}}
<header x-data="{ open:false }" class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-100">
    <div class="max-w-6xl mx-auto px-5 h-16 flex items-center justify-between">
        <a href="#" class="flex items-center gap-2.5">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-cyanx-500 to-brand-600 text-white flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m9-9H3"/><circle cx="12" cy="12" r="9" stroke-width="1.6"/></svg>
            </span>
            <span class="font-bold text-slate-800 text-lg">CitasMédicas</span>
        </a>
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
            <a href="#funciones" class="hover:text-slate-900">Funciones</a>
            <a href="#modulos" class="hover:text-slate-900">Módulos</a>
            <a href="#precios" class="hover:text-slate-900">Precios</a>
        </nav>
        <div class="flex items-center gap-2 sm:gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900 px-3 py-2">Ir al panel</a>
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-white bg-gradient-to-r from-cyanx-500 to-brand-600 hover:opacity-90 rounded-lg px-4 py-2 shadow-sm">Mi clínica</a>
            @else
                <a href="{{ route('login') }}" class="hidden sm:inline text-sm font-semibold text-slate-600 hover:text-slate-900 px-3 py-2">Iniciar sesión</a>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-white bg-gradient-to-r from-cyanx-500 to-brand-600 hover:opacity-90 rounded-lg px-4 py-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Prueba gratis
                </a>
            @endauth
        </div>
    </div>
</header>

{{-- ===================== HERO ===================== --}}
<section class="hero-bg text-white relative overflow-hidden">
    <div class="max-w-6xl mx-auto px-5 pt-20 pb-24 text-center relative">
        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 ring-1 ring-white/20 text-xs sm:text-sm font-medium text-cyan-100 backdrop-blur">
            <span class="w-1.5 h-1.5 rounded-full bg-cyan-300"></span>
            Plataforma de gestión clínica #1 en LATAM
        </span>

        <h1 class="mt-7 text-4xl sm:text-6xl font-black tracking-tight leading-[1.05]">
            Gestiona tu clínica<br>
            <span class="grad-text">de forma inteligente</span>
        </h1>

        <p class="mt-6 max-w-2xl mx-auto text-base sm:text-lg text-slate-300">
            Todo lo que necesitas para administrar citas, pacientes, médicos, recetas y facturación.
            Sin complicaciones, desde cualquier dispositivo.
        </p>

        <div class="mt-9 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-cyanx-500 to-brand-600 hover:opacity-95 text-white font-semibold rounded-xl px-7 py-3.5 shadow-lg shadow-cyan-500/20">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Comenzar gratis — 30 días
            </a>
            <a href="#precios" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/15 ring-1 ring-white/20 text-white font-semibold rounded-xl px-7 py-3.5 backdrop-blur">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
                Ver precios
            </a>
        </div>

        <div class="mt-16 grid grid-cols-2 sm:grid-cols-4 gap-8 max-w-3xl mx-auto">
            @foreach([['+500','Clínicas activas'],['+50k','Pacientes gestionados'],['99.9%','Disponibilidad'],['24/7','Soporte']] as $s)
                <div>
                    <p class="text-3xl sm:text-4xl font-black text-white">{{ $s[0] }}</p>
                    <p class="text-xs sm:text-sm text-slate-400 mt-1">{{ $s[1] }}</p>
                </div>
            @endforeach
        </div>
    </div>
    <div class="h-10 bg-gradient-to-b from-transparent to-white"></div>
</section>

{{-- ===================== FUNCIONES ===================== --}}
<section id="funciones" class="py-20 sm:py-24">
    <div class="max-w-6xl mx-auto px-5">
        <div class="text-center max-w-2xl mx-auto">
            <p class="text-sm font-semibold text-cyanx-600 uppercase tracking-wider">Funciones</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold text-slate-900">Todo lo que tu clínica necesita</h2>
            <p class="mt-4 text-slate-500">Una plataforma completa, pensada para el día a día de consultorios y clínicas.</p>
        </div>

        <div class="mt-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @php
                $features = [
                    ['Agenda de citas','Programa, confirma y da seguimiento a las citas con cambio de estado en un clic.','M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 6h15a.75.75 0 0 1 .75.75v12.75a.75.75 0 0 1-.75.75h-15a.75.75 0 0 1-.75-.75V6.75A.75.75 0 0 1 4.5 6Z','from-cyan-50','text-cyan-600'],
                    ['Pacientes','Historial, datos demográficos y aseguradora de cada paciente, siempre a la mano.','M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z','from-emerald-50','text-emerald-600'],
                    ['Recetas médicas','Emite recetas con medicamentos, dosis e indicaciones, listas para imprimir.','M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z','from-violet-50','text-violet-600'],
                    ['Facturación','Genera comprobantes con ítems, descuentos e impuestos y controla los cobros.','M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z','from-amber-50','text-amber-600'],
                    ['Calendario','Visualiza la ocupación del mes y navega entre fechas con citas por color.','M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M3.75 6.75A.75.75 0 0 1 4.5 6h15a.75.75 0 0 1 .75.75v12.75a.75.75 0 0 1-.75.75h-15a.75.75 0 0 1-.75-.75V6.75Z','from-sky-50','text-sky-600'],
                    ['Reportes','Métricas de citas por mes, estado y especialidad, con exportación a CSV.','M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z','from-rose-50','text-rose-600'],
                ];
            @endphp
            @foreach($features as $f)
                <div class="group bg-white border border-slate-200 rounded-2xl p-6 hover:shadow-lg hover:-translate-y-0.5 transition">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $f[3] }} to-white {{ $f[4] }} flex items-center justify-center ring-1 ring-slate-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $f[2] }}"/></svg>
                    </div>
                    <h3 class="mt-4 font-bold text-slate-800 text-lg">{{ $f[0] }}</h3>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed">{{ $f[1] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== MÓDULOS (banda) ===================== --}}
<section id="modulos" class="py-16 bg-slate-50 border-y border-slate-100">
    <div class="max-w-6xl mx-auto px-5">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Un sistema, todos tus módulos</h2>
            <p class="mt-3 text-slate-500">Gestiona toda la operación clínica desde un único panel.</p>
        </div>
        <div class="flex flex-wrap justify-center gap-3">
            @foreach(['Dashboard','Citas','Lista de espera','Calendario','Pacientes','Médicos','Recetas','Especialidades','Aseguradores','Facturación','Reportes','Usuarios y roles'] as $m)
                <span class="inline-flex items-center gap-2 bg-white border border-slate-200 rounded-full px-4 py-2 text-sm font-medium text-slate-600 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-gradient-to-r from-cyanx-500 to-brand-600"></span>{{ $m }}
                </span>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== PRECIOS ===================== --}}
<section id="precios" class="py-20 sm:py-24">
    <div class="max-w-6xl mx-auto px-5">
        <div class="text-center max-w-2xl mx-auto">
            <p class="text-sm font-semibold text-cyanx-600 uppercase tracking-wider">Precios</p>
            <h2 class="mt-2 text-3xl sm:text-4xl font-extrabold text-slate-900">Planes para cada clínica</h2>
            <p class="mt-4 text-slate-500">Comienza gratis durante 30 días. Sin tarjeta de crédito.</p>
        </div>

        <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
            @php
                $planes = [
                    ['Básico','S/ 199','/mes', ['1 consultorio','Hasta 2 médicos','Citas y pacientes','Calendario','Soporte por correo'], false],
                    ['Profesional','S/ 449','/mes', ['Hasta 5 consultorios','Médicos ilimitados','Recetas y facturación','Reportes y export','Soporte prioritario'], true],
                    ['Clínica','S/ 899','/mes', ['Consultorios ilimitados','Usuarios y roles','Aseguradoras y convenios','Reportes avanzados','Soporte 24/7'], false],
                ];
            @endphp
            @foreach($planes as $p)
                <div class="relative bg-white rounded-2xl p-7 flex flex-col {{ $p[4] ? 'ring-2 ring-brand-500 shadow-xl' : 'border border-slate-200 shadow-sm' }}">
                    @if($p[4])
                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-cyanx-500 to-brand-600 text-white text-xs font-semibold px-3 py-1 rounded-full shadow">Más popular</span>
                    @endif
                    <h3 class="font-bold text-slate-800 text-lg">{{ $p[0] }}</h3>
                    <div class="mt-3 flex items-end gap-1">
                        <span class="text-4xl font-black text-slate-900">{{ $p[1] }}</span>
                        <span class="text-slate-400 mb-1.5 text-sm">{{ $p[2] }}</span>
                    </div>
                    <ul class="mt-6 space-y-3 flex-1">
                        @foreach($p[3] as $feat)
                            <li class="flex items-start gap-2.5 text-sm text-slate-600">
                                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                {{ $feat }}
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('login') }}" class="mt-7 text-center font-semibold rounded-xl px-5 py-3 {{ $p[4] ? 'bg-gradient-to-r from-cyanx-500 to-brand-600 text-white hover:opacity-95 shadow-sm' : 'border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                        Comenzar ahora
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== CTA ===================== --}}
<section class="px-5 pb-20">
    <div class="max-w-6xl mx-auto hero-bg rounded-3xl px-8 py-14 text-center text-white relative overflow-hidden">
        <h2 class="text-3xl sm:text-4xl font-black">¿Listo para modernizar tu clínica?</h2>
        <p class="mt-4 text-slate-300 max-w-xl mx-auto">Únete a cientos de clínicas que ya gestionan sus citas de forma inteligente.</p>
        <a href="{{ route('login') }}" class="mt-8 inline-flex items-center justify-center gap-2 bg-white text-slate-900 font-semibold rounded-xl px-7 py-3.5 hover:bg-slate-100 shadow-lg">
            Comenzar gratis — 30 días
        </a>
    </div>
</section>

{{-- ===================== FOOTER ===================== --}}
<footer class="border-t border-slate-100 py-10">
    <div class="max-w-6xl mx-auto px-5 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-cyanx-500 to-brand-600 text-white flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m9-9H3"/></svg>
            </span>
            <span class="font-bold text-slate-700">CitasMédicas</span>
        </div>
        <p class="text-sm text-slate-400">© {{ date('Y') }} CitasMédicas · Desarrollado con Laravel</p>
        <a href="{{ route('login') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">Iniciar sesión →</a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>

<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · CitasMédicas</title>

    @include('partials.assets')
</head>
<body class="h-full bg-slate-100 text-slate-700" x-data="{ open: false }" @keydown.escape.window="open = false">

    {{-- Backdrop (solo móvil/tablet cuando el drawer está abierto) --}}
    <div x-show="open" x-transition.opacity x-cloak @click="open = false"
         class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden"></div>

    {{-- ===================== SIDEBAR (drawer en móvil, fijo en desktop) ===================== --}}
    {{-- Sin x-cloak: en desktop (lg) se ve siempre; en móvil arranca oculto (-translate-x-full)
         y se abre con !translate-x-0 para ganar especificidad. --}}
    <aside :class="{ '!translate-x-0': open }"
           class="fixed inset-y-0 left-0 w-64 sm:w-72 lg:w-60 z-50 text-white
                  bg-gradient-to-b from-[#17b8cf] to-[#23c9dd] border-r border-white/10
                  flex flex-col transform transition-transform duration-300 ease-in-out
                  -translate-x-full lg:translate-x-0">
        @include('partials.sidebar')
    </aside>

    {{-- ===================== CONTENIDO ===================== --}}
    <div class="lg:pl-60 min-h-full flex flex-col">

        {{-- Topbar --}}
        <header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-30 flex items-center gap-3 px-4 sm:px-6">
            {{-- Hamburguesa (oculta en desktop) --}}
            <button type="button" @click="open = true"
                    class="lg:hidden w-9 h-9 -ml-1 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-600 shrink-0">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/></svg>
            </button>

            <div class="min-w-0">
                <h1 class="text-base sm:text-lg font-bold text-slate-800 leading-tight truncate">@yield('title', 'Dashboard')</h1>
                <p class="text-xs text-slate-400 -mt-0.5 hidden sm:block truncate">@yield('subtitle', 'Progress Update')</p>
            </div>

            <div class="ml-auto flex items-center gap-2 sm:gap-3">
                <div class="hidden md:flex items-center gap-2 bg-slate-100 rounded-lg px-3 py-2 w-44 lg:w-56">
                    <span class="text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-4 h-4'])</span>
                    <input type="text" placeholder="Buscar..." class="bg-transparent border-0 p-0 text-sm focus:ring-0 w-full placeholder:text-slate-400">
                </div>
                <button class="relative w-9 h-9 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                    @include('partials.icon', ['name' => 'bell', 'class' => 'w-5 h-5'])
                    <span class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
                </button>
                <a href="{{ route('perfil.edit') }}" title="Mi perfil"
                   class="flex items-center gap-2.5 sm:pl-3 sm:border-l border-slate-200 rounded-lg hover:bg-slate-50 px-1.5 py-1 transition">
                    <div class="text-right hidden sm:block leading-tight">
                        <p class="text-sm font-semibold text-slate-700">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-[11px] text-slate-400 -mt-0.5">{{ ucfirst(auth()->user()->rol ?? 'admin') }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 text-white text-sm font-semibold flex items-center justify-center shrink-0">
                        {{ auth()->user()?->iniciales() ?? 'AD' }}
                    </div>
                </a>
            </div>
        </header>

        {{-- Slot principal --}}
        <main class="p-4 sm:p-6 flex-1">
            @include('partials.flash')
            @yield('content')
        </main>
    </div>
</body>
</html>

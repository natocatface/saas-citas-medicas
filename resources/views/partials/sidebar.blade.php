{{-- Contenido interno del sidebar (fondo cian, texto blanco; compartido por escritorio y drawer móvil) --}}
<div class="h-16 flex items-center gap-2.5 px-5 border-b border-white/15 shrink-0">
    <div class="w-9 h-9 rounded-xl bg-white/20 text-white flex items-center justify-center backdrop-blur-sm">
        @include('partials.icon', ['name' => 'logo', 'class' => 'w-5 h-5'])
    </div>
    <div class="leading-tight">
        <p class="font-bold text-white text-[15px]">CitasMédicas</p>
        <p class="text-[10px] text-white/70 -mt-0.5">SaaS Clínico</p>
    </div>
    <button type="button" @click="open=false" class="ml-auto lg:hidden w-8 h-8 rounded-lg hover:bg-white/15 flex items-center justify-center text-white">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
    </button>
</div>

<nav class="flex-1 overflow-y-auto sidebar-scroll py-4 px-3 space-y-5">
    @foreach(\App\Support\Menu::paraRol(auth()->user()->rol ?? 'admin') as $grupo)
        <div class="space-y-1">
            @if($grupo['label'])
                <p class="px-3 mb-2 text-[10px] font-semibold tracking-wider text-white/60 uppercase">{{ $grupo['label'] }}</p>
            @endif
            @foreach($grupo['items'] as $item)
                <a href="{{ $item['url'] }}"
                   class="group rounded-full text-sm font-medium transition {{ $item['active'] ? 'bg-white/25 text-white shadow-sm' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
                   style="display:flex;align-items:center;gap:12px;padding:10px 12px;line-height:1">
                    <span class="{{ $item['active'] ? 'text-white' : 'text-white/85' }}"
                          style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;flex:none">
                        @include('partials.icon', ['name' => $item['icon'], 'class' => 'w-5 h-5'])
                    </span>
                    <span class="truncate" style="line-height:1.2">{{ $item['titulo'] }}</span>
                </a>
            @endforeach
        </div>
    @endforeach
</nav>

<div class="border-t border-white/15 p-3 shrink-0">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full rounded-full text-sm font-medium text-white/90 hover:bg-white/15 hover:text-white transition"
                style="display:flex;align-items:center;gap:12px;padding:10px 12px;line-height:1">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;flex:none">
                @include('partials.icon', ['name' => 'logout', 'class' => 'w-5 h-5'])
            </span>
            Salir
        </button>
    </form>
</div>

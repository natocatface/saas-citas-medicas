{{-- Mensajes flash de sesión --}}
@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)" x-transition
         class="mb-4 flex items-start gap-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
        <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
        <span class="flex-1">{{ session('success') }}</span>
        <button @click="show = false" class="text-emerald-400 hover:text-emerald-600">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 flex items-start gap-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">
        <svg class="w-5 h-5 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
        <span class="flex-1">{{ session('error') }}</span>
    </div>
@endif

@if(isset($errors) && $errors->any())
    <div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">
        <p class="font-semibold mb-1">Revisa los siguientes campos:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

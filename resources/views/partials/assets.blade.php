{{-- Estilos embebidos INLINE dentro del HTML: imposible que un CDN o la red los bloquee.
     El CSS se genera con /tmp/gencss y vive en partials/inline-css.blade.php. --}}

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

@include('partials.inline-css')

{{-- Chart.js: local si existe, si no CDN --}}
@if(file_exists(public_path('js/chart.umd.min.js')))
    <script src="{{ asset('js/chart.umd.min.js') }}"></script>
@else
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endif

{{-- Alpine.js: local si existe, si no CDN --}}
@if(file_exists(public_path('js/alpine.min.js')))
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>
@else
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endif

{{-- Iconos SVG (Heroicons outline). Uso: @include('partials.icon', ['name' => 'home', 'class' => 'w-5 h-5']) --}}
@php
    $class   = $class ?? 'w-5 h-5';
    // Convierte clases Tailwind w-N/h-N a px para el atributo style,
    // garantizando tamaÃ±o correcto cuando Tailwind CDN no procesa variables dinÃ¡micas.
    $sizeMap = ['3'=>'12px','4'=>'16px','5'=>'20px','6'=>'24px','7'=>'28px','8'=>'32px','9'=>'36px','10'=>'40px','11'=>'44px','12'=>'48px'];
    preg_match('/\bw-(\d+)\b/', $class, $wm);
    preg_match('/\bh-(\d+)\b/', $class, $hm);
    $wPx = isset($wm[1]) ? ($sizeMap[$wm[1]] ?? ($wm[1]*4).'px') : '20px';
    $hPx = isset($hm[1]) ? ($sizeMap[$hm[1]] ?? ($hm[1]*4).'px') : '20px';
    $svgStyle = "width:{$wPx};height:{$hPx};flex-shrink:0;";
@endphp
@switch($name)
    @case('home')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75 12 3l9 6.75M4.5 9.75V20a1 1 0 0 0 1 1H9v-5h6v5h3.5a1 1 0 0 0 1-1V9.75"/></svg>
        @break
    @case('calendar')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 6h15a.75.75 0 0 1 .75.75v12.75a.75.75 0 0 1-.75.75h-15a.75.75 0 0 1-.75-.75V6.75A.75.75 0 0 1 4.5 6Z"/></svg>
        @break
    @case('clock')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l3.75 2.25M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
        @break
    @case('calendar-days')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M3.75 6.75A.75.75 0 0 1 4.5 6h15a.75.75 0 0 1 .75.75v12.75a.75.75 0 0 1-.75.75h-15a.75.75 0 0 1-.75-.75V6.75ZM7.5 11.25h.008v.008H7.5v-.008Zm0 3h.008v.008H7.5v-.008Zm3-3h.008v.008H10.5v-.008Zm0 3h.008v.008H10.5v-.008Zm3-3h.008v.008H13.5v-.008Zm0 3h.008v.008H13.5v-.008Z"/></svg>
        @break
    @case('users')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
        @break
    @case('stethoscope')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v5a4 4 0 0 0 8 0V3M9 16v1.5a4.5 4.5 0 0 0 9 0V14"/><circle cx="18" cy="12" r="2" stroke-width="1.6"/><path stroke-linecap="round" d="M5 3H3.5M5 3h1.5M13 3h-1.5M13 3h1.5"/></svg>
        @break
    @case('document')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v1.5m3-3v3m3-4.5v4.5M6.75 21h10.5a2.25 2.25 0 0 0 2.25-2.25V9.75a2.25 2.25 0 0 0-.659-1.591l-5.25-5.25A2.25 2.25 0 0 0 12 2.25H6.75A2.25 2.25 0 0 0 4.5 4.5v14.25A2.25 2.25 0 0 0 6.75 21Z"/></svg>
        @break
    @case('clipboard')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 3.75h6M9 17.25h3M9 6.75h.008v.008H9V6.75Zm0 0h6V6a2.25 2.25 0 0 0-4.5 0v.75Zm0 0v.008H9V6.75ZM15.75 6.75H18a2.25 2.25 0 0 1 2.25 2.25v9A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V9A2.25 2.25 0 0 1 6 6.75h2.25"/></svg>
        @break
    @case('chat')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/></svg>
        @break
    @case('cash')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/></svg>
        @break
    @case('receipt')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3.75h12a.75.75 0 0 1 .75.75V21l-2.25-1.5L14.25 21 12 19.5 9.75 21 7.5 19.5 5.25 21V4.5A.75.75 0 0 1 6 3.75Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25h6M9 11.25h6M9 14.25h3"/></svg>
        @break
    @case('shield')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 5.25-3.75 8.25-8.5 9.45a1.2 1.2 0 0 1-.62 0C7.13 20.25 3 17.25 3 12V6.3a1.2 1.2 0 0 1 .8-1.13l8-3a1.2 1.2 0 0 1 .84 0l8 3a1.2 1.2 0 0 1 .8 1.13V12Z"/></svg>
        @break
    @case('chart')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
        @break
    @case('beaker')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 0-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0 1 12 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/></svg>
        @break
    @case('user-circle')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.964 0a9 9 0 1 0-11.964 0m11.964 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
        @break
    @case('wrench')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z"/></svg>
        @break
    @case('mail')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
        @break
    @case('cog')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.03 7.03 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.241.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.991l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
        @break
    @case('bell')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
        @break
    @case('search')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
        @break
    @case('logout')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
        @break
    @case('plus')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        @break
    @case('refresh')
        <svg class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        @break
    @case('building')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
        @break
    @case('ticket')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z"/></svg>
        @break
    @case('list')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 5.25a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0-10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM8.25 6.75h12M8.25 12h12m-12 5.25h12"/></svg>
        @break
    @case('logo')
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m9-9H3"/><circle cx="12" cy="12" r="9" stroke-width="1.6"/></svg>
        @break
    @default
        <svg style="{{ $svgStyle }}" class="{{ $class }}" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><circle cx="12" cy="12" r="9"/></svg>
@endswitch


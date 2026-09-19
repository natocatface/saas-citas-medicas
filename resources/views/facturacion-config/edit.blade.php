@extends('layouts.app')

@section('title', 'Facturación Electrónica')
@section('subtitle', 'Configuración de emisión de comprobantes (SUNAT)')

@php
    $driver       = old('driver', $config->driver ?? 'ninguno');
    $modo         = old('modo', $config->modo ?? 'beta');
    $habilitado   = old('habilitado', $config->habilitado);
    $certOk       = $config->certificadoDisponible();
    $enProduccion = $habilitado && $driver === 'greenter' && $modo === 'produccion';

    // Pastillas de estado (fondo translúcido, texto) que van dentro del banner.
    $pill = fn ($ok) => $ok
        ? ['background:rgba(16,185,129,.22)', 'color:#ecfdf5']
        : ['background:rgba(244,63,94,.20)',  'color:#fee2e2'];

    $chks = [
        'RUC'          => ! empty($config->ruc),
        'Razón social' => ! empty($config->razon_social),
        'Usuario SOL'  => ! empty($config->sol_usuario),
        'Certificado'  => $certOk,
    ];
    $completos = count(array_filter($chks));

    $pHab  = $pill($habilitado);
    $pCert = $pill($certOk);
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- ============================================================= --}}
    {{-- BANNER: identidad del módulo + estado + probar conexión        --}}
    {{-- Degradado índigo→cian coherente con el tema del sistema.       --}}
    {{-- ============================================================= --}}
    <div class="relative overflow-hidden rounded-2xl shadow-lg"
         style="background:linear-gradient(120deg,#4f46e5 0%,#6366f1 42%,#22b8d6 100%);">
        <div class="absolute" style="width:240px;height:240px;border-radius:9999px;background:rgba(255,255,255,.08);top:-90px;right:-40px;"></div>
        <div class="absolute" style="width:150px;height:150px;border-radius:9999px;background:rgba(255,255,255,.06);bottom:-70px;right:170px;"></div>

        <div class="relative p-6 sm:p-7">
            <div class="flex items-start gap-5">
                {{-- Icono --}}
                <div class="flex items-center justify-center shrink-0"
                     style="width:60px;height:60px;border-radius:16px;background:rgba(255,255,255,.16);box-shadow:inset 0 0 0 1px rgba(255,255,255,.25);">
                    @include('partials.icon', ['name' => 'receipt', 'class' => 'w-8 h-8'])
                </div>

                {{-- Título + descripción --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-xl font-bold" style="color:#fff;">Facturación Electrónica</h2>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full"
                              style="background:rgba(255,255,255,.2);color:#fff;">
                            <span style="display:inline-flex;width:18px;height:12px;border-radius:2px;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,.25);">
                                <span style="flex:1;background:#D91023;"></span><span style="flex:1;background:#fff;"></span><span style="flex:1;background:#D91023;"></span>
                            </span>
                            Perú
                        </span>
                    </div>
                    <p class="text-sm mt-1.5" style="color:rgba(255,255,255,.9);">
                        Emisión de comprobantes ante <strong>SUNAT</strong> · UBL 2.1 · Boletas, facturas y notas de crédito
                    </p>
                </div>

                {{-- Sello SUNAT --}}
                <div class="text-right shrink-0 hidden sm:block">
                    <span class="inline-block text-sm font-extrabold px-3 py-1.5 rounded-lg"
                          style="background:rgba(255,255,255,.95);color:#4338ca;letter-spacing:.5px;">SUNAT</span>
                    <p class="text-[11px] mt-2 leading-tight" style="color:rgba(255,255,255,.85);">Comprobantes de<br>Pago Electrónicos</p>
                </div>
            </div>

            {{-- Barra de estado integrada --}}
            <div class="mt-5 pt-4 flex flex-wrap items-center gap-2"
                 style="border-top:1px solid rgba(255,255,255,.18);">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
                      style="{{ $pHab[0] }};{{ $pHab[1] }};">
                    ● {{ $habilitado ? 'Habilitada' : 'Deshabilitada' }}
                </span>
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold"
                      style="background:rgba(255,255,255,.14);color:#eef2ff;">Driver: {{ $driver === 'ninguno' ? 'null' : $driver }}</span>
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold"
                      style="background:rgba(255,255,255,.14);color:#eef2ff;">Modo: {{ $modo }}</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
                      style="{{ $pCert[0] }};{{ $pCert[1] }};">
                    {{ $certOk ? '✓ Certificado OK' : '✕ Certificado no encontrado' }}
                </span>
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold"
                      style="background:rgba(255,255,255,.14);color:#eef2ff;">{{ $completos }}/4 datos</span>

                <form method="POST" action="{{ route('facturacion-config.probar') }}" class="ml-auto">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 text-sm font-semibold rounded-lg px-4 py-2 shadow-sm transition"
                            style="background:rgba(255,255,255,.95);color:#4338ca;">
                        @include('partials.icon', ['name' => 'refresh', 'class' => 'w-4 h-4'])
                        Probar conexión con SUNAT
                    </button>
                </form>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('facturacion-config.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- ===================== Estado y modo ===================== --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#cffafe;color:#0e7490;">
                    @include('partials.icon', ['name' => 'cog', 'class' => 'w-6 h-6'])
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Estado y modo</h3>
                    <p class="text-xs text-slate-400">Activación, forma de emisión y entorno de SUNAT.</p>
                </div>
            </div>

            <label class="flex items-start gap-3 cursor-pointer border border-slate-200 rounded-xl p-4 mb-3 hover:border-brand-300 transition">
                <input type="checkbox" name="habilitado" value="1" @checked($habilitado) class="mt-0.5 h-5 w-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <span>
                    <span class="block text-sm font-semibold text-slate-800">Habilitar facturación electrónica</span>
                    <span class="block text-xs text-slate-500">Si está desactivada, las ventas no generan comprobante ante SUNAT.</span>
                </span>
            </label>

            <label class="flex items-start gap-3 cursor-pointer border border-slate-200 rounded-xl p-4 mb-5 hover:border-brand-300 transition">
                <input type="checkbox" name="emitir_automatico" value="1" @checked(old('emitir_automatico', $config->emitir_automatico)) class="mt-0.5 h-5 w-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <span>
                    <span class="block text-sm font-semibold text-slate-800">Emitir automáticamente al registrar la factura</span>
                    <span class="block text-xs text-slate-500">Cada boleta o factura se envía apenas se registra.</span>
                </span>
            </label>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Driver de emisión</label>
                    <select name="driver" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="ninguno" @selected($driver === 'ninguno')>Ninguno (no emite, deja pendiente)</option>
                        <option value="greenter" @selected($driver === 'greenter')>Greenter (emite ante SUNAT)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Entorno SUNAT</label>
                    <select name="modo" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="beta" @selected($modo === 'beta')>Beta (homologación / pruebas)</option>
                        <option value="produccion" @selected($modo === 'produccion')>Producción</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- ===================== Datos del emisor ===================== --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#e0e7ff;color:#4f46e5;">
                    @include('partials.icon', ['name' => 'building', 'class' => 'w-6 h-6'])
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Datos del emisor</h3>
                    <p class="text-xs text-slate-400">Aparecen en el comprobante electrónico.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">RUC <span class="text-rose-500">*</span></label>
                    <input type="text" name="ruc" value="{{ old('ruc', $config->ruc) }}" maxlength="11" inputmode="numeric" placeholder="20123456789"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Razón social <span class="text-rose-500">*</span></label>
                    <input type="text" name="razon_social" value="{{ old('razon_social', $config->razon_social) }}" placeholder="CLÍNICA SALUD TOTAL S.A.C."
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre comercial</label>
                    <input type="text" name="nombre_comercial" value="{{ old('nombre_comercial', $config->nombre_comercial) }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Dirección fiscal</label>
                    <input type="text" name="direccion" value="{{ old('direccion', $config->direccion) }}" placeholder="Av. Principal 123"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Ubigeo <span class="text-slate-400 font-normal">(6 dígitos)</span></label>
                    <input type="text" name="ubigeo" value="{{ old('ubigeo', $config->ubigeo) }}" maxlength="6" placeholder="150101"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Departamento</label>
                    <input type="text" name="departamento" value="{{ old('departamento', $config->departamento) }}" placeholder="LIMA"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Provincia</label>
                    <input type="text" name="provincia" value="{{ old('provincia', $config->provincia) }}" placeholder="LIMA"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Distrito</label>
                    <input type="text" name="distrito" value="{{ old('distrito', $config->distrito) }}" placeholder="LIMA"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
            </div>
        </div>

        {{-- ===================== Series y tributos ===================== --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#d1fae5;color:#059669;">
                    @include('partials.icon', ['name' => 'cash', 'class' => 'w-6 h-6'])
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Series y tributos</h3>
                    <p class="text-xs text-slate-400">Series autorizadas por SUNAT e IGV.</p>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Serie factura</label>
                    <input type="text" name="serie_factura" value="{{ old('serie_factura', $config->serie_factura ?? 'F001') }}" maxlength="4"
                           class="w-full rounded-lg border-slate-300 text-sm uppercase focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Serie boleta</label>
                    <input type="text" name="serie_boleta" value="{{ old('serie_boleta', $config->serie_boleta ?? 'B001') }}" maxlength="4"
                           class="w-full rounded-lg border-slate-300 text-sm uppercase focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Serie N. Crédito</label>
                    <input type="text" name="serie_nota_credito" value="{{ old('serie_nota_credito', $config->serie_nota_credito ?? 'FC01') }}" maxlength="4"
                           class="w-full rounded-lg border-slate-300 text-sm uppercase focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">IGV (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="igv_tasa" value="{{ old('igv_tasa', $config->igv_tasa ?? '18.00') }}"
                           class="w-full rounded-lg border-slate-300 text-sm text-right focus:border-brand-500 focus:ring-brand-500">
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-400">Moneda: <strong class="text-slate-700">PEN — Soles</strong> (fija para Perú).</p>
        </div>

        {{-- ===================== Credenciales SUNAT ===================== --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#fef3c7;color:#b45309;">
                    @include('partials.icon', ['name' => 'shield', 'class' => 'w-6 h-6'])
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Credenciales SUNAT</h3>
                    <p class="text-xs text-slate-400">Clave SOL y certificado digital. Las claves se guardan cifradas.</p>
                </div>
            </div>

            @if($modo === 'beta')
                <div class="rounded-xl px-4 py-3 text-sm mb-5 flex items-start gap-2" style="background:#e0f2fe;border:1px solid #bae6fd;color:#075985;">
                    @include('partials.icon', ['name' => 'shield', 'class' => 'w-5 h-5'])
                    <span>En <strong>beta</strong> puedes usar RUC <strong>20000000001</strong> con usuario y clave <strong>MODDATOS</strong>.</span>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Usuario Clave SOL</label>
                    <input type="text" name="sol_usuario" value="{{ old('sol_usuario', $config->sol_usuario) }}" autocomplete="off" placeholder="MODDATOS"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Clave SOL</label>
                    <input type="password" name="sol_clave" autocomplete="new-password" placeholder="{{ $config->sol_clave ? '•••••••• (sin cambios)' : '' }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Ruta del certificado (.pem)</label>
                    <input type="text" name="certificado_ruta" value="{{ old('certificado_ruta', $config->certificado_ruta) }}"
                           placeholder="C:\SAAS\saas_citasmedicas\storage\facturacion\pe\certificate.pem"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                    @if($config->certificado_ruta && ! $certOk)
                        <p class="mt-2 text-xs text-rose-600 flex items-center gap-1.5">⚠ No se encontró el certificado en la ruta indicada.</p>
                    @elseif($certOk)
                        <p class="mt-2 text-xs text-emerald-600">✓ Certificado disponible.</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">…o subir certificado <span class="text-slate-400 font-normal">(.pfx / .p12 / .pem)</span></label>
                    <input type="file" name="certificado" accept=".pfx,.p12,.pem"
                           class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-brand-50 file:text-brand-700 file:font-semibold hover:file:bg-brand-100">
                    @if($config->certificado_path && ! $config->certificado_ruta)
                        <p class="mt-2 text-xs text-slate-500">Archivo cargado: {{ basename($config->certificado_path) }}</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Clave del certificado <span class="text-slate-400 font-normal">(.pfx/.p12)</span></label>
                    <input type="password" name="certificado_clave" autocomplete="new-password" placeholder="{{ $config->certificado_clave ? '•••••••• (sin cambios)' : '' }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
            </div>
        </div>

        {{-- ===================== Barra inferior ===================== --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm px-5 py-4 flex flex-wrap items-center gap-3">
            <a href="{{ route('facturacion.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">← Volver</a>
            <a href="{{ route('bandeja-sunat.index') }}" class="inline-flex items-center gap-1.5 border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold rounded-lg px-4 py-2">
                @include('partials.icon', ['name' => 'list', 'class' => 'w-4 h-4']) Bandeja SUNAT
            </a>
            <button type="submit" class="ml-auto inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-6 py-2.5 shadow-sm transition">
                @include('partials.icon', ['name' => 'refresh', 'class' => 'w-4 h-4']) Guardar configuración
            </button>
        </div>
    </form>
</div>
@endsection

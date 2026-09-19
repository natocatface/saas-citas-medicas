@php
    $tipoLabel = [
        'factura'      => 'FACTURA ELECTRÓNICA',
        'boleta'       => 'BOLETA DE VENTA ELECTRÓNICA',
        'nota_credito' => 'NOTA DE CRÉDITO ELECTRÓNICA',
        'nota_debito'  => 'NOTA DE DÉBITO ELECTRÓNICA',
    ][$cpe->tipo] ?? 'COMPROBANTE ELECTRÓNICO';
    $docLabel = ['ruc' => 'RUC', 'dni' => 'DNI', 'ce' => 'C.E.', 'sin' => 'S/D'][$cpe->receptor_doc] ?? strtoupper($cpe->receptor_doc);
    $fmt = fn ($n) => 'S/ ' . number_format((float) $n, 2);
    $emisorRuc   = $emisor['ruc'] ?? $cpe->emisor_ruc;
    $emisorRazon = $emisor['razon_social'] ?? 'EMPRESA';
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>{{ $tipoLabel }} {{ $cpe->numero_completo }}</title>
<style>
    * { box-sizing: border-box; }
    body { font-family: Arial, Helvetica, sans-serif; color: #1f2937; margin: 0; padding: 24px; font-size: 12px; background: #f1f5f9; }
    .hoja { background: #fff; max-width: 780px; margin: 0 auto; padding: 28px 32px; border: 1px solid #e2e8f0; }
    .top { display: flex; gap: 16px; align-items: stretch; }
    .emisor { flex: 1; }
    .logo { display: inline-flex; align-items: center; gap: 10px; }
    .logo .mark { width: 42px; height: 42px; border-radius: 10px; background: linear-gradient(135deg,#22d3ee,#0891b2); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:20px; }
    .emisor h1 { font-size: 16px; margin: 0; color:#0f172a; }
    .emisor p { margin: 2px 0; color:#475569; }
    .caja { width: 250px; border: 2px solid #0891b2; border-radius: 8px; padding: 12px; text-align: center; }
    .caja .ruc { font-weight: 700; font-size: 13px; color:#0f172a; }
    .caja .tipo { font-weight: 800; color:#0891b2; margin: 6px 0; letter-spacing: .3px; }
    .caja .num { font-size: 16px; font-weight: 800; color:#0f172a; }
    .sep { height: 1px; background: #e2e8f0; margin: 16px 0; }
    .cliente td { padding: 2px 0; vertical-align: top; }
    .cliente .k { color:#64748b; width: 130px; font-weight: 600; }
    table.det { width: 100%; border-collapse: collapse; margin-top: 14px; }
    table.det th { background: #0f172a; color:#fff; font-size: 10px; text-transform: uppercase; letter-spacing:.4px; padding: 7px 8px; text-align: left; }
    table.det th.r, table.det td.r { text-align: right; }
    table.det th.c, table.det td.c { text-align: center; }
    table.det td { padding: 7px 8px; border-bottom: 1px solid #eef2f7; color:#334155; }
    .tot { width: 300px; margin-left: auto; margin-top: 12px; }
    .tot tr td { padding: 4px 8px; }
    .tot .k { color:#64748b; text-align: right; }
    .tot .v { text-align: right; font-weight: 600; color:#0f172a; width: 110px; }
    .tot .total td { border-top: 2px solid #0f172a; font-size: 14px; font-weight: 800; padding-top: 8px; }
    .letras { margin-top: 14px; padding: 8px 10px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; font-weight: 600; color:#334155; }
    .pie { margin-top: 18px; display: flex; gap: 16px; align-items: flex-end; }
    .pie .qr { width: 96px; height: 96px; border: 1px dashed #cbd5e1; border-radius: 6px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:9px; text-align:center; padding:6px; }
    .pie .meta { flex: 1; color:#64748b; font-size: 10.5px; }
    .pie .meta .hash { word-break: break-all; font-family: 'Courier New', monospace; color:#475569; }
    .estado { display:inline-block; padding: 2px 10px; border-radius: 999px; font-size: 10px; font-weight: 700; }
    .estado.ok { background:#dcfce7; color:#15803d; }
    .estado.sim { background:#e0f2fe; color:#0369a1; }
    .barra { text-align:center; margin-bottom: 16px; }
    .barra button { background:#0891b2; color:#fff; border:0; padding: 9px 18px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 12px; }
    .barra a { color:#475569; text-decoration: none; margin-left: 14px; font-size: 12px; }
    @media print { body { background:#fff; padding:0; } .hoja { border:0; max-width:none; } .barra { display:none; } }
</style>
</head>
<body>
    <div class="barra">
        <button onclick="window.print()">Imprimir / Guardar PDF</button>
        <a href="{{ route('facturacion.show', $factura) }}">← Volver a la factura</a>
    </div>

    <div class="hoja">
        <div class="top">
            <div class="emisor">
                <div class="logo">
                    <span class="mark">＋</span>
                    <div>
                        <h1>{{ $emisorRazon }}</h1>
                        <p>{{ $emisor['nombre_comercial'] ?? 'Servicios médicos y de salud' }}</p>
                    </div>
                </div>
                <p style="margin-top:10px">{{ $emisor['direccion'] ?? 'Av. Principal 123 - Lima, Perú' }}</p>
            </div>
            <div class="caja">
                <div class="ruc">R.U.C. {{ $emisorRuc }}</div>
                <div class="tipo">{{ $tipoLabel }}</div>
                <div class="num">{{ $cpe->numero_completo }}</div>
            </div>
        </div>

        <div class="sep"></div>

        <table class="cliente">
            <tr>
                <td class="k">Señor(es):</td>
                <td>{{ $cpe->receptor_nombre }}</td>
                <td class="k">Fecha de emisión:</td>
                <td>{{ optional($cpe->fecha_emision)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="k">{{ $docLabel }}:</td>
                <td>{{ $cpe->receptor_numero }}</td>
                <td class="k">Moneda:</td>
                <td>{{ $cpe->moneda }} — Soles</td>
            </tr>
            <tr>
                <td class="k">Dirección:</td>
                <td>{{ $factura->paciente->direccion ?? '—' }}</td>
                <td class="k">Condición:</td>
                <td>{{ $factura->metodo_pago ? 'Contado ('.$factura->metodo_pago.')' : 'Contado' }}</td>
            </tr>
        </table>

        <table class="det">
            <thead>
                <tr>
                    <th class="c" style="width:52px">Cant.</th>
                    <th style="width:52px">U.M.</th>
                    <th>Descripción</th>
                    <th class="r" style="width:90px">P. Unit.</th>
                    <th class="r" style="width:100px">Importe</th>
                </tr>
            </thead>
            <tbody>
                @forelse($factura->items as $it)
                    <tr>
                        <td class="c">{{ rtrim(rtrim(number_format($it->cantidad, 2), '0'), '.') }}</td>
                        <td>NIU</td>
                        <td>{{ $it->descripcion }}</td>
                        <td class="r">{{ number_format($it->precio_unitario, 2) }}</td>
                        <td class="r">{{ number_format($it->importe, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:14px">Sin ítems.</td></tr>
                @endforelse
            </tbody>
        </table>

        <table class="tot">
            <tr><td class="k">Op. Gravada</td><td class="v">{{ $fmt($cpe->gravado) }}</td></tr>
            @if((float) $factura->descuento > 0)
                <tr><td class="k">Descuento</td><td class="v">- {{ $fmt($factura->descuento) }}</td></tr>
            @endif
            <tr><td class="k">I.G.V. (18%)</td><td class="v">{{ $fmt($cpe->igv) }}</td></tr>
            <tr class="total"><td class="k">Importe Total</td><td class="v">{{ $fmt($cpe->total) }}</td></tr>
        </table>

        <div class="letras">{{ $enLetras }}</div>

        <div class="pie">
            @if(!empty($qrImg))
                <div class="qr" style="border:1px solid #e2e8f0;padding:4px;"><img src="{{ $qrImg }}" alt="QR" style="width:88px;height:88px;display:block;"></div>
            @else
                <div class="qr" title="{{ $qrData ?? '' }}">Código QR<br>(instala endroid/qr-code<br>para mostrarlo)</div>
            @endif
            <div class="meta">
                <p>Representación impresa de la {{ $tipoLabel }}.
                   Estado:
                   @if(in_array($cpe->estado, ['aceptado','observado']))
                       <span class="estado ok">{{ ucfirst($cpe->estado) }}</span>
                   @else
                       <span class="estado sim">{{ ucfirst($cpe->estado) }}</span>
                   @endif
                   @unless(config('facturacion.proveedores.PE.habilitado'))
                       &nbsp;·&nbsp;<em>Modo simulado — no válido ante SUNAT hasta activar el certificado.</em>
                   @endunless
                </p>
                <p>Autorización / Resumen: {{ $cpe->codigo_respuesta ?: '—' }}</p>
                <p class="hash">Hash: {{ $cpe->hash_cpe ?: '—' }}</p>
            </div>
        </div>
    </div>
</body>
</html>

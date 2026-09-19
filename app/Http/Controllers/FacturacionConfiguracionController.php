<?php

namespace App\Http\Controllers;

use App\Facturacion\Infrastructure\Persistence\Models\FacturacionConfiguracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FacturacionConfiguracionController extends Controller
{
    /** Pantalla de configuración de la facturación electrónica de la clínica. */
    public function index(Request $request)
    {
        $config = $this->configDeClinica($request);

        return view('facturacion-config.edit', ['config' => $config]);
    }

    public function update(Request $request)
    {
        $config = $this->configDeClinica($request);

        $data = $request->validate([
            'habilitado' => ['nullable', 'boolean'],
            'emitir_automatico' => ['nullable', 'boolean'],
            'modo' => ['required', Rule::in(['beta', 'produccion'])],
            'driver' => ['required', Rule::in(['ninguno', 'greenter'])],
            'ruc' => ['nullable', 'digits:11'],
            'razon_social' => ['nullable', 'string', 'max:255'],
            'nombre_comercial' => ['nullable', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ubigeo' => ['nullable', 'digits:6'],
            'departamento' => ['nullable', 'string', 'max:60'],
            'provincia' => ['nullable', 'string', 'max:60'],
            'distrito' => ['nullable', 'string', 'max:60'],
            'igv_tasa' => ['required', 'numeric', 'between:0,100'],
            'serie_factura' => ['required', 'regex:/^[A-Z]\d{3}$/'],
            'serie_boleta' => ['required', 'regex:/^[A-Z]\d{3}$/'],
            'serie_nota_credito' => ['required', 'regex:/^[A-Z]\d{3}$/'],
            'sol_usuario' => ['nullable', 'string', 'max:50'],
            'sol_clave' => ['nullable', 'string', 'max:100'],
            'certificado' => ['nullable', 'file', 'max:5120'],
            'certificado_ruta' => ['nullable', 'string', 'max:255'],
            'certificado_clave' => ['nullable', 'string', 'max:100'],
        ], [], [
            'serie_factura' => 'serie de factura',
            'serie_boleta' => 'serie de boleta',
            'serie_nota_credito' => 'serie de nota de crédito',
        ]);

        // Validación y almacenamiento del certificado (pfx/p12/pem).
        if ($request->hasFile('certificado')) {
            $archivo = $request->file('certificado');
            $ext = strtolower($archivo->getClientOriginalExtension());
            if (! in_array($ext, ['pfx', 'p12', 'pem'], true)) {
                return back()->withInput()->with('error', 'El certificado debe ser .pfx, .p12 o .pem');
            }

            // Para .pfx/.p12 se valida la clave del certificado al vuelo: así el
            // usuario sabe de inmediato si la contraseña es correcta.
            if (in_array($ext, ['pfx', 'p12'], true)) {
                $clave = $data['certificado_clave'] ?? ($config->certificado_clave ?? '');
                $certs = [];
                if (! openssl_pkcs12_read((string) file_get_contents($archivo->getRealPath()), $certs, (string) $clave)) {
                    return back()->withInput()->with('error', 'No se pudo abrir el certificado: la clave del certificado es incorrecta o el archivo está dañado.');
                }
            }

            $ruc = $data['ruc'] ?? $config->ruc ?? 'sin-ruc';
            $config->certificado_path = $archivo->storeAs(
                'facturacion/PE/certificados',
                "{$ruc}-".($config->clinica_id ?? 'x').".{$ext}",
                'local'
            );
        }

        $config->fill([
            'habilitado' => $request->boolean('habilitado'),
            'emitir_automatico' => $request->boolean('emitir_automatico'),
            'modo' => $data['modo'],
            'driver' => $data['driver'],
            'ruc' => $data['ruc'] ?? null,
            'razon_social' => $data['razon_social'] ?? null,
            'nombre_comercial' => $data['nombre_comercial'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'ubigeo' => $data['ubigeo'] ?? null,
            'departamento' => $data['departamento'] ?? null,
            'provincia' => $data['provincia'] ?? null,
            'distrito' => $data['distrito'] ?? null,
            'igv_tasa' => $data['igv_tasa'],
            'serie_factura' => $data['serie_factura'],
            'serie_boleta' => $data['serie_boleta'],
            'serie_nota_credito' => $data['serie_nota_credito'],
            'sol_usuario' => $data['sol_usuario'] ?? null,
            'certificado_ruta' => $data['certificado_ruta'] ?? null,
        ]);

        // Sólo se sobreescriben las claves si el usuario ingresó una nueva.
        if (! empty($data['sol_clave'])) {
            $config->sol_clave = $data['sol_clave'];
        }
        if (! empty($data['certificado_clave'])) {
            $config->certificado_clave = $data['certificado_clave'];
        }

        $config->save();

        return redirect()->route('facturacion-config.index')
            ->with('success', 'Configuración de facturación electrónica guardada.');
    }

    /**
     * Prueba de conexión con SUNAT: valida que la configuración esté completa,
     * que el certificado se pueda leer y que el endpoint de SUNAT sea accesible.
     */
    public function probarConexion(Request $request, \App\Facturacion\Infrastructure\Providers\Peru\ResolverConfiguracionSunat $resolver)
    {
        $config = $this->configDeClinica($request);

        if (! $config->habilitado || $config->driver !== 'greenter') {
            return back()->with('error', 'Está en modo simulado (driver "ninguno" o FE deshabilitada): no hay conexión real que probar. Activa el driver Greenter para emitir ante SUNAT.');
        }

        try {
            $cred = $resolver->paraClinica($config->clinica_id);
        } catch (\Throwable $e) {
            return back()->with('error', 'Configuración/certificado: '.$e->getMessage());
        }

        if ($cred === null) {
            return back()->with('error', 'No se pudieron resolver las credenciales de SUNAT.');
        }

        // Verifica que el WSDL del servicio de SUNAT responda.
        try {
            $resp = \Illuminate\Support\Facades\Http::timeout(12)->get($cred->endpoint.'?wsdl');
            if (! $resp->successful()) {
                return back()->with('error', 'El servicio de SUNAT respondió con código '.$resp->status().'. Reintenta más tarde.');
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo contactar el servicio de SUNAT ('.$cred->endpoint.'). Verifica la conexión a internet. Detalle: '.$e->getMessage());
        }

        $entorno = $cred->esProduccion() ? 'Producción' : 'Homologación (Beta)';

        return back()->with('success', "Conexión correcta: certificado válido y servicio de SUNAT accesible en {$entorno}. RUC {$cred->ruc}.");
    }

    private function configDeClinica(Request $request): FacturacionConfiguracion
    {
        $clinicaId = $request->user()->clinica_id;

        return FacturacionConfiguracion::firstOrNew(['clinica_id' => $clinicaId]);
    }
}

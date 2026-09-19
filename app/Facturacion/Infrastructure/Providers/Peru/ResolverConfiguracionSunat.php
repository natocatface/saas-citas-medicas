<?php

namespace App\Facturacion\Infrastructure\Providers\Peru;

use App\Facturacion\Domain\Exception\FacturacionException;
use App\Facturacion\Infrastructure\Persistence\Models\FacturacionConfiguracion;
use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * Traduce la FacturacionConfiguracion de una clínica en CredencialesSunat listas
 * para emitir. Aquí ocurre todo lo sensible: descifrado de claves, lectura del
 * certificado y su conversión .pfx/.p12 → PEM (formato que requiere Greenter),
 * y la elección del endpoint según el entorno (homologación / producción).
 */
final class ResolverConfiguracionSunat
{
    // URLs oficiales del web service de comprobantes de SUNAT.
    private const ENDPOINT_BETA = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';
    private const ENDPOINT_PRODUCCION = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService';

    public function __construct(private readonly Filesystem $disk)
    {
    }

    /**
     * Devuelve las credenciales de la clínica, o null si no está habilitada la
     * emisión real (en cuyo caso el proveedor opera en modo simulado).
     *
     * @throws FacturacionException si la configuración está incompleta o el
     *                              certificado/clave son inválidos.
     */
    public function paraClinica(?int $clinicaId): ?CredencialesSunat
    {
        /** @var FacturacionConfiguracion|null $cfg */
        $cfg = FacturacionConfiguracion::withoutGlobalScopes()
            ->where('clinica_id', $clinicaId)
            ->first();

        // Simulado si: no existe, no habilitada, o el driver no emite en real.
        if (! $cfg || ! $cfg->habilitado || $cfg->driver !== 'greenter') {
            return null;
        }

        $faltan = $this->camposFaltantes($cfg);
        if ($faltan !== []) {
            throw new FacturacionException(
                'Configuración de facturación incompleta: falta '.implode(', ', $faltan).
                '. Complétala en Administración → Fact. Electrónica.'
            );
        }

        return new CredencialesSunat(
            ruc: (string) $cfg->ruc,
            razonSocial: (string) $cfg->razon_social,
            nombreComercial: $cfg->nombre_comercial,
            direccion: $cfg->direccion,
            ubigeo: $cfg->ubigeo,
            departamento: $cfg->departamento,
            provincia: $cfg->provincia,
            distrito: $cfg->distrito,
            endpoint: $cfg->modo === 'produccion' ? self::ENDPOINT_PRODUCCION : self::ENDPOINT_BETA,
            solUsuario: (string) $cfg->sol_usuario,
            solClave: (string) $cfg->sol_clave,
            certificadoPem: $this->certificadoPem($cfg),
            modo: (string) $cfg->modo,
        );
    }

    /** @return string[] nombres de los campos obligatorios que faltan */
    private function camposFaltantes(FacturacionConfiguracion $cfg): array
    {
        $req = [
            'RUC' => $cfg->ruc,
            'razón social' => $cfg->razon_social,
            'usuario SOL' => $cfg->sol_usuario,
            'clave SOL' => $cfg->sol_clave,
            'certificado digital' => $cfg->certificadoEnUso(),
        ];

        return array_keys(array_filter($req, fn ($v) => empty($v)));
    }

    /**
     * Devuelve el certificado en formato PEM (llave privada + certificado).
     * Prioriza la ruta absoluta a un .pem; si no, usa el archivo subido en storage.
     */
    private function certificadoPem(FacturacionConfiguracion $cfg): string
    {
        // 1) Ruta absoluta a un .pem indicada por el usuario.
        if ($cfg->certificado_ruta) {
            if (! is_file($cfg->certificado_ruta)) {
                throw new FacturacionException('No se encontró el certificado en la ruta indicada: '.$cfg->certificado_ruta);
            }

            return (string) file_get_contents($cfg->certificado_ruta);
        }

        // 2) Archivo subido (.pfx/.p12/.pem) almacenado en el disco de la app.
        $ruta = (string) $cfg->certificado_path;
        if (! $ruta || ! $this->disk->exists($ruta)) {
            throw new FacturacionException('No se encuentra el archivo del certificado digital.');
        }

        $contenido = (string) $this->disk->get($ruta);
        $ext = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));

        if ($ext === 'pem') {
            return $contenido;
        }

        // .pfx / .p12 → extraer llave y certificado con la clave del certificado.
        $certs = [];
        if (! openssl_pkcs12_read($contenido, $certs, (string) $cfg->certificado_clave)) {
            throw new FacturacionException(
                'No se pudo abrir el certificado (.pfx/.p12): la clave del certificado es incorrecta o el archivo está dañado.'
            );
        }

        return ($certs['pkey'] ?? '').($certs['cert'] ?? '');
    }
}

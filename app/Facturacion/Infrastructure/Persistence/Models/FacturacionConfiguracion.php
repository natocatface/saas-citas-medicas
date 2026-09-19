<?php

namespace App\Facturacion\Infrastructure\Persistence\Models;

use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\Model;

/**
 * Configuración de facturación electrónica por clínica (multi-tenant).
 * Las credenciales sensibles (clave SOL y clave del certificado) se guardan
 * cifradas en base de datos mediante el cast 'encrypted'.
 */
class FacturacionConfiguracion extends Model
{
    use BelongsToClinica;

    protected $table = 'facturacion_configuraciones';

    protected $fillable = [
        'clinica_id', 'habilitado', 'emitir_automatico', 'modo', 'driver',
        'ruc', 'razon_social', 'nombre_comercial', 'direccion', 'ubigeo',
        'departamento', 'provincia', 'distrito',
        'moneda', 'igv_tasa', 'serie_factura', 'serie_boleta', 'serie_nota_credito',
        'sol_usuario', 'sol_clave', 'certificado_path', 'certificado_ruta', 'certificado_clave',
    ];

    protected $casts = [
        'habilitado' => 'boolean',
        'emitir_automatico' => 'boolean',
        'igv_tasa' => 'decimal:2',
        'sol_clave' => 'encrypted',
        'certificado_clave' => 'encrypted',
    ];

    protected $hidden = ['sol_clave', 'certificado_clave'];

    /** ¿Emite realmente ante SUNAT? (habilitada + driver real). */
    public function emiteReal(): bool
    {
        return $this->habilitado && $this->driver === 'greenter';
    }

    /** Ruta del certificado que se usará (ruta manual .pem o archivo subido). */
    public function certificadoEnUso(): ?string
    {
        return $this->certificado_ruta ?: $this->certificado_path;
    }

    /** ¿Existe el certificado configurado? */
    public function certificadoDisponible(): bool
    {
        if ($this->certificado_ruta) {
            return is_file($this->certificado_ruta);
        }
        if ($this->certificado_path) {
            return \Illuminate\Support\Facades\Storage::disk(config('facturacion.disco', 'local'))->exists($this->certificado_path);
        }

        return false;
    }

    /** ¿Está lista para emitir en real? (habilitada + credenciales + certificado). */
    public function listaParaProduccion(): bool
    {
        return $this->emiteReal()
            && $this->ruc
            && $this->sol_usuario
            && $this->certificadoDisponible();
    }
}

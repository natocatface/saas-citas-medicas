<?php

namespace App\Facturacion\Infrastructure\Storage;

use App\Facturacion\Domain\Contracts\AlmacenDocumentos;
use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * Almacén de XML/CDR/PDF sobre el sistema de archivos de Laravel.
 * El disco es configurable (local, s3, azure...) sin tocar el dominio.
 */
final class AlmacenLocalDocumentos implements AlmacenDocumentos
{
    public function __construct(private readonly Filesystem $disk)
    {
    }

    public function guardar(string $ruta, string $contenido): string
    {
        $this->disk->put($ruta, $contenido);

        return $ruta;
    }

    public function leer(string $ruta): ?string
    {
        return $this->disk->exists($ruta) ? $this->disk->get($ruta) : null;
    }

    public function existe(string $ruta): bool
    {
        return $this->disk->exists($ruta);
    }
}

<?php

namespace App\Facturacion\Domain\Contracts;

/**
 * PUERTO de almacenamiento de artefactos (XML firmado, CDR/acuse, PDF).
 * Implementaciones posibles: disco local, S3, Azure Blob... sin tocar el dominio.
 */
interface AlmacenDocumentos
{
    /** Guarda contenido y devuelve la ruta/URI donde quedó almacenado. */
    public function guardar(string $ruta, string $contenido): string;

    public function leer(string $ruta): ?string;

    public function existe(string $ruta): bool;
}

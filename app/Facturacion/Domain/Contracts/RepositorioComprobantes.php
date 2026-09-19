<?php

namespace App\Facturacion\Domain\Contracts;

use App\Facturacion\Domain\Model\Comprobante;
use App\Facturacion\Domain\Result\ResultadoEmision;

/**
 * PUERTO de persistencia. El dominio define QUÉ necesita guardar;
 * la infraestructura (Eloquent) decide CÓMO. Permite testear sin base de datos.
 */
interface RepositorioComprobantes
{
    /** Persiste/actualiza el comprobante y devuelve su id interno. */
    public function guardar(Comprobante $comprobante, ResultadoEmision $resultado): int;

    /** Registra un evento en la bitácora de auditoría del comprobante. */
    public function registrarEvento(string $numeroCompleto, string $evento, array $payload = []): void;

    /** Siguiente correlativo atómico para una serie (evita huecos/duplicados). */
    public function siguienteCorrelativo(int $clinicaId, string $serie): string;
}

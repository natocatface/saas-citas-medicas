# Módulo de Facturación Electrónica (contexto acotado)

Motor de facturación electrónica multi-país embebido en el ERP de Citas Médicas,
diseñado con **Clean Architecture + DDD + patrón Strategy/Adapter** para que:

- El ERP **no dependa** de la lógica tributaria de ningún país.
- Agregar un país **no obligue a modificar** código existente (Open/Closed).
- El módulo pueda **extraerse a un microservicio** mañana sin reescribir el ERP
  (el contrato REST en `Interface/routes.php` ya es la frontera).

## Capas (regla de dependencia: todo apunta hacia el Dominio)

```
Interface  ──▶  Application  ──▶  Domain  ◀──  Infrastructure
(REST/HTTP)     (casos de uso)   (contratos,     (adaptadores país,
                                  modelo puro)    Eloquent, storage, jobs)
```

- **Domain/** — modelo puro (`Comprobante`, `Emisor`, `Receptor`…), enums,
  y los **puertos**: `ProveedorFacturacion` (interfaz común con `emitirFactura`,
  `anularFactura`, `emitirNotaCredito`, `consultarEstado`), `RepositorioComprobantes`,
  `AlmacenDocumentos`. No importa nada de Laravel.
- **Application/** — casos de uso que orquestan puertos + `RegistroProveedores`
  (resuelve el adaptador por país) + `MapeadorFacturaErp` (anticorruption layer).
- **Infrastructure/** — adaptadores concretos: `Peru/SunatProvider` (real vía
  Greenter / simulado en dev), stubs `Colombia/Chile/Argentina/Mexico`,
  persistencia Eloquent, almacenamiento y job de reintentos.
- **Interface/** — API REST estable + composición HTTP.
- **Providers/FacturacionServiceProvider** — composition root: cablea puertos↔adaptadores.

## Cómo agregar un país nuevo (ej. Ecuador - SRI)

1. Crear `Infrastructure/Providers/Ecuador/SriProvider.php` que implemente
   `ProveedorFacturacion`.
2. Añadir el case al enum `Domain/Enum/Pais.php`.
3. Registrarlo con una línea en `FacturacionServiceProvider::registrarProveedores()`.

No se toca el ERP, ni el dominio, ni los otros países.

## Modo de operación (Fase 3 — real vía Greenter)

La emisión resuelve las credenciales **por empresa** desde su `FacturacionConfiguracion`
(pantalla Administración → Fact. Electrónica). No hay credenciales globales.

- Si la clínica **no** tiene la casilla "Habilitar emisión real" activada, o le faltan
  datos, el `SunatProvider` opera en **modo simulado** (genera XML/hash, no transmite).
- Si está habilitada y completa, `ResolverConfiguracionSunat` arma las credenciales
  (descifra claves, convierte el certificado `.pfx/.p12` → PEM, elige endpoint beta/producción)
  y `ConstructorUblGreenter` genera el UBL, firma XAdES y envía a SUNAT, devolviendo el CDR.

Puesta en marcha:

1. `composer update` (instala `greenter/lite` y `endroid/qr-code`, ya en `composer.json`).
2. `php artisan migrate` (tablas de comprobantes y configuración).
3. En la pantalla de configuración: cargar RUC, razón social, usuario/clave SOL,
   certificado `.pfx` + su clave, elegir entorno (Beta primero) y activar la casilla.
4. Emitir una factura → se transmite a SUNAT y se guarda el CDR + hash + QR.

`ResolverConfiguracionSunat` y `ConstructorUblGreenter` son los únicos que tocan Greenter,
así que el resto del sistema no depende de esa librería.

## Uso rápido

```php
// Asíncrono (recomendado): encola y responde al instante
EmitirComprobanteJob::dispatch($mapeador->desdeFactura($factura, Pais::PE));

// o vía REST
POST /api/facturacion/comprobantes { "factura_id": 123 }
```

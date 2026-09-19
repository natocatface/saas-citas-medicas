<?php

namespace App\Facturacion\Providers;

use App\Facturacion\Application\RegistroProveedores;
use App\Facturacion\Domain\Contracts\AlmacenDocumentos;
use App\Facturacion\Domain\Contracts\RepositorioComprobantes;
use App\Facturacion\Infrastructure\Persistence\EloquentRepositorioComprobantes;
use App\Facturacion\Infrastructure\Providers\Argentina\ArcaProvider;
use App\Facturacion\Infrastructure\Providers\Chile\SiiProvider;
use App\Facturacion\Infrastructure\Providers\Colombia\DianProvider;
use App\Facturacion\Infrastructure\Providers\Mexico\SatProvider;
use App\Facturacion\Infrastructure\Providers\Peru\ConstructorUblGreenter;
use App\Facturacion\Infrastructure\Providers\Peru\ResolverConfiguracionSunat;
use App\Facturacion\Infrastructure\Providers\Peru\SunatProvider;
use App\Facturacion\Infrastructure\Storage\AlmacenLocalDocumentos;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

/**
 * COMPOSITION ROOT del módulo de facturación electrónica.
 *
 * Único lugar donde se "cablean" puertos con adaptadores y se registran los
 * proveedores por país. Añadir un país = una línea en registrarProveedores().
 */
class FacturacionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/facturacion.php', 'facturacion');

        // Puertos -> adaptadores
        $this->app->bind(RepositorioComprobantes::class, EloquentRepositorioComprobantes::class);

        $this->app->bind(AlmacenDocumentos::class, function () {
            return new AlmacenLocalDocumentos(Storage::disk(config('facturacion.disco', 'local')));
        });

        // Registro de proveedores (Strategy) como singleton
        $this->app->singleton(RegistroProveedores::class, function ($app) {
            $registro = new RegistroProveedores();
            $this->registrarProveedores($registro, $app);

            return $registro;
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Infrastructure/Persistence/migrations');

        if (file_exists($rutas = __DIR__.'/../Interface/routes.php')) {
            $this->loadRoutesFrom($rutas);
        }

        $this->publishes([
            __DIR__.'/../Config/facturacion.php' => config_path('facturacion.php'),
        ], 'facturacion-config');
    }

    /**
     * Alta declarativa de países. Para incorporar uno nuevo, se añade su
     * adaptador aquí y nada más (Open/Closed Principle).
     */
    private function registrarProveedores(RegistroProveedores $registro, $app): void
    {
        $cfg = config('facturacion.proveedores', []);

        // Perú — real vía Greenter con credenciales por empresa / simulado si no habilitado
        $registro->registrar(new SunatProvider(
            new ResolverConfiguracionSunat(Storage::disk(config('facturacion.disco', 'local'))),
            $app->make(AlmacenDocumentos::class),
            new ConstructorUblGreenter(),
        ));

        // Stubs: se activan a medida que se implementan (ver hoja de ruta)
        $registro->registrar(new DianProvider($cfg['CO'] ?? []));
        $registro->registrar(new SiiProvider($cfg['CL'] ?? []));
        $registro->registrar(new ArcaProvider($cfg['AR'] ?? []));
        $registro->registrar(new SatProvider($cfg['MX'] ?? []));
    }
}

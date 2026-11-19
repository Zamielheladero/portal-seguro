<?php

namespace App\Providers;

use App\Services\Cotizador;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Diseño AgroPoste: una sola instancia de la cotización por petición.
        $this->app->scoped(Cotizador::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));

            return Limit::perMinute(5)
                ->by($email.'|'.$request->ip());
        });

        // Pesos colombianos sin decimales: @cop(22900) => $ 22.900 (escapado con e()).
        Blade::directive('cop', fn (string $expresion) => "<?php echo e('$ '.number_format((int) round((float) ({$expresion})), 0, ',', '.')); ?>");

        // Contador del carrito de la cabecera.
        View::composer('layouts.app', function ($view): void {
            $view->with('cantidadCotizacion', app(Cotizador::class)->conteo());
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            $prefix = '';
            if (php_sapi_name() !== 'cli' && isset($_SERVER['SCRIPT_NAME'])) {
                $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
                if ($base && $base !== '/' && $base !== '\\') {
                    $prefix = ltrim(str_replace('\\', '/', $base), '/');
                }
            }
            if (!$prefix) {
                $path = parse_url(config('app.url'), PHP_URL_PATH);
                $prefix = ($path && $path !== '/') ? trim($path, '/') : '';
            }

            Route::middleware('api')
                ->prefix($prefix ? $prefix . '/api' : 'api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->prefix($prefix)
                ->group(base_path('routes/web.php'));
        });
    }
}

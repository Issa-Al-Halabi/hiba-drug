<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{

    protected $namespace = 'App\Http\Controllers';


    public const HOME = '/home';

    public function boot()
    {
        parent::boot();
    }


    public function map()
    {
        $this->mapApiAlameenRoutes();

        $this->mapApiRoutes();
        $this->mapApiv2Routes();

        $this->mapAdminRoutes();
        $this->mapSellerRoutes();
        $this->mapCustomerRoutes();
        $this->mapWebRoutes();
        $this->mapSharedRoutes();

        //$this->mapInstallRoutes();
        //$this->mapUpdateRoutes();
    }

    protected function mapAdminRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/admin.php'));
    }

    protected function mapSellerRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/seller.php'));
    }

    protected function mapCustomerRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/customer.php'));
    }

    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    protected function mapSharedRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/shared.php'));
    }

    protected function mapTestRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/test.php'));
    }

    protected function mapInstallRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/install.php'));
    }

    protected function mapUpdateRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/update.php'));
    }

    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api/v1/api.php'));
    }

    protected function mapApiv2Routes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api/v2/api.php'));
    }

     protected function mapApiAlameenRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api/Alameen/api.php'));
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()->id ?: $request->ip());
        });
    }
}

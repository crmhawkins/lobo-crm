<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\Settings;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Alertas;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        // $empresa = Settings::whereNull('deleted_at')->first();
        // View::share('empresa', $empresa);
        Schema::defaultStringLength(191);
        setlocale(LC_TIME, 'es_ES');
        Carbon::setlocale('es');
        Carbon::setUTF8(true);
        Paginator::useBootstrap();

        View::composer('layouts.header', function ($view) {
        if (Auth::check()) { // Asegúrate de que el usuario está autenticado
            $alertasPendientes = Alertas::where('user_id', Auth::id())->whereNull('leida')->count();
            $view->with('alertasPendientes', $alertasPendientes);
        }
    });
    }
}


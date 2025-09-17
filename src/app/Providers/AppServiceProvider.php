<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\NavigationController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Instancia del NavigationController
        $navigationController = new NavigationController();

        // Compartir $menu con todas las vistas
        view()->composer('*', function ($view) use ($navigationController) {
            $menu = $navigationController->getMenu(); // Llama al método getMenu del controlador
            $view->with('menu', $menu); // Compartir $menu en todas las vistas
        });
    }
}

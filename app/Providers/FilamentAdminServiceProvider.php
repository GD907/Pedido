<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;


class FilamentAdminServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Filament::serving(function () {
            Filament::registerRenderHook(
                'header.end', // Lo registra al final de la barra de encabezado
                fn () => view('filament.components.notifications-icon') // Vista personalizada
            );
        });
    }
}

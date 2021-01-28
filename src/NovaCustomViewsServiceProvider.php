<?php

namespace NovaCustomViews;

use Laravel\Nova\Nova;
use Illuminate\Support\Collection;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\ServiceProvider;
use NovaCustomViews\Commands\ViewsCommand;
use NovaCustomViews\Commands\Error403ViewCommand;
use NovaCustomViews\Commands\Error404ViewCommand;
use NovaCustomViews\Commands\DashboardViewCommand;

class NovaCustomViewsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {
        Nova::serving(function (ServingNova $event) {
            Nova::script('nova-custom-views', __DIR__ . '/../dist/js/nova-custom-views.js');
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([ViewsCommand::class, DashboardViewCommand::class, Error403ViewCommand::class, Error404ViewCommand::class]);
    }
}

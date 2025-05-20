<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Load helpers
        $this->loadHelpers();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::automaticallyEagerLoadRelationships();
    }

    /**
     * Load helper functions.
     */
    protected function loadHelpers(): void
    {
        if (file_exists($file = app_path('helpers/helpers.php'))) {
            require_once $file;
        }
    }
}

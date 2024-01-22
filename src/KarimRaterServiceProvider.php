<?php

namespace Karimaouaouda\LaravelRater;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class KarimRaterServiceProvider extends ServiceProvider{


    public function register() {

    }

    /**
     * @throws \Exception
     */
    private function assertProperlyConfigured(): void
    {
        Rater::assertConfiguration();
    }


    /**
     * @throws \Exception
     */
    public function boot(): void
    {

        require_once(__DIR__."/../functions/helpers.php");

        Route::middleware("web")
        ->group(__DIR__."/../routes/web.php");


        $this->loadMigrationsFrom(__DIR__."/../database/migrations/");

        if(!Config::has("rater")){
            $this->mergeConfigFrom(__DIR__."/../config/rater.php", "rater");
        }

        $this->assertProperlyConfigured();

    }
}

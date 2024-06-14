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
        //
    }
}

<?php

namespace Karimaouaouda\LaravelRater\Traits;

use App\Exceptions\TestException;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Karimaouaouda\LaravelRater\Ratable;
use Karimaouaouda\LaravelRater\RateBuilder;
use Karimaouaouda\LaravelRater\Rater;

trait CanRate{

    /**
     * @throws TestException
     */
    public function rate(Ratable $model ): RateBuilder
    {
        return new RateBuilder($this, $model);
    }

    public function unrate(Ratable $model){
        return Rater::unrate($this, $model);
    }

    public function getRatesAttribute(){
        return Rater::getUserRates($this);
    }


    public function rated(Ratable $model){
        return Rater::isUserRated($this, $model);
    }


    public function ratesWhere($callback){
        return Rater::getUserRatesWhere($this, $callback);
    }


}

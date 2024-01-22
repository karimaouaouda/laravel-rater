<?php

use Karimaouaouda\LaravelRater\Ratable;

if( !function_exists('encodeType') ){
    function encodeType($some_object){
        if( ! is_object($some_object) ){
            throw new BadMethodCallException("expected an object to be passed to encodeType function");
        }

        return base64_encode( get_class($some_object) );
    }
}

if( !function_exists('buildRateUrl') ){
    function buildRateUrl(Ratable $model): string
    {
        $class = get_class($model);

        $class = base64_encode($class);

        $id = $model->id;

        return env('APP_URL')."/rate/$class/$id";
    }
}

if( !function_exists('buildRatesUrl') ){
    function buildRatesUrl(\Illuminate\Foundation\Auth\User $user) : string
    {
        $prefix = "/rates/";
        $suffix = $prefix . base64_encode(get_class($user)) ."/". $user->id;

        return env('APP_URL') . $suffix;
    }
}


if( !function_exists('buildUnRateUrl') ){
    function buildUnRateUrl(Ratable $model): string
    {

        $class = get_class($model);

        $class = base64_encode($class);

        $id = $model->id;

        return env('APP_URL')."/unrate/$class/$id";
    }
}


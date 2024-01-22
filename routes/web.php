<?php


use Illuminate\Support\Facades\Route;
use Karimaouaouda\LaravelRater\Http\Controllers\RaterController;



Route::middleware("auth")->controller(RaterController::class)->group(function(){

    Route::post('/rate/{model_type_encoded}/{model_id}', [RaterController::class, 'rate'])->name('rate');

    Route::post('/unrate/{model_type_encoded}/{model_id}', [RaterController::class, 'unrate'])->name('unrate');

    Route::get('/rates/{rater_type_encoded}/{rater_id}', [RaterController::class, 'rates'])->name('rates');

});

<?php

use Illuminate\Support\Facades\Route;

Route::get('/tuijncode/laravel-version', [Tuijncode\LaravelVersion\Controllers\LaravelVersionController::class, 'index'])
    ->name('tuijncode.laravel-version');

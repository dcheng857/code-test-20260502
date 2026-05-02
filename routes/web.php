<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home', [
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

Route::get('/edit/{id}', function ($id) {
    return Inertia::render('EditItem', [
        'id' => $id,
    ]);
})->where('id', '[0-9]+');

Route::get('/new', function () {
    return Inertia::render('NewItem');
});

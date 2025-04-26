<?php

use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return 'Ceci est une route de test.';
});

Route::get('/test-admin', function () {
    return 'Vous êtes un admin.';
})->middleware(\App\Http\Middleware\AdminMiddleware::class); 
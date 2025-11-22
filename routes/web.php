<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Ruta Raíz: Muestra el estado de carga (animación)
Route::get('/', function () {
    // Corregido el nombre del componente a 'Loading'
    return Inertia::render('Loading');
});

// Ruta Welcome: La landing page a la que se redirige después de cargar
Route::get('/inicio', function () {
    return Inertia::render('Welcome'); // Asegúrate de tener un componente Welcome.vue
})->name('welcome');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

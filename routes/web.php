<?php

use App\Http\Controllers\HetznerController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/hetzner', [HetznerController::class, 'edit'])->name('hetzner.edit');
    Route::put('/hetzner', [HetznerController::class, 'update'])->name('hetzner.update');
    Route::delete('/hetzner', [HetznerController::class, 'destroy'])->name('hetzner.destroy');
    Route::get('/hetzner/servers', [HetznerController::class, 'servers'])->name('hetzner.servers');
});

require __DIR__.'/auth.php';

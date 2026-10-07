<?php

use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::get('users', [UserManagementController::class, 'index'])
        ->name('users.index');

    Route::get('users/create', [UserManagementController::class, 'create'])
        ->name('users.create');
});

require __DIR__.'/settings.php';

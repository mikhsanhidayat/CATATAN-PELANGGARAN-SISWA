<?php

use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::get('users', [UserManagementController::class, 'index'])
        ->name('users.index');

    Route::post('users', [UserManagementController::class, 'store'])
        ->name('users.store');

    Route::get('users/create', [UserManagementController::class, 'create'])
        ->name('users.create');

    Route::get('users/{id_users}', [UserManagementController::class, 'show'])
        ->name('users.show')
        ->whereNumber('id_users');

    Route::put('users/{id_users}', [UserManagementController::class, 'update'])
        ->name('users.update')
        ->whereNumber('id_users');

    Route::delete('users/{id_users}', [UserManagementController::class, 'destroy'])
        ->name('users.destroy')
        ->whereNumber('id_users');
});

require __DIR__.'/settings.php';

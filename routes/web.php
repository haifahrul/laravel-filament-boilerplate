<?php

use Illuminate\Support\Facades\Route;

// Untuk router dummy, handle error: Route [login] not defined.
Route::get('/login', function () {
    return response()->json(['message' => 'Login required.'], 401);
})->name('login');

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/stats', fn () => ['data' => 'stats']);
});

Route::get('/', function () {
    return view('welcome');
});

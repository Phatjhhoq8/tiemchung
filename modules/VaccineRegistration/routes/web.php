<?php

use Illuminate\Support\Facades\Route;
use Modules\VaccineRegistration\Http\Controllers\VaccineController;

Route::middleware('web')->group(function () {
    Route::get('/', [VaccineController::class, 'index'])->name('vaccine.index');
    Route::post('/cart/add', [VaccineController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove', [VaccineController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/register', [VaccineController::class, 'showRegister'])->name('register.show');
    Route::post('/register', [VaccineController::class, 'postRegister'])->name('register.post');
    Route::get('/success', [VaccineController::class, 'showSuccess'])->name('register.success');
});

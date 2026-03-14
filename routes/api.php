<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

// M-Pesa callback — no CSRF, must return 200
Route::post('/callback', [PaymentController::class, 'callback'])->name('mpesa.callback');

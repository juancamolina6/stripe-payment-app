<?php

use App\Http\Controllers\TransactionController;

Route::post('/webhook', [TransactionController::class, 'handleWebhook']);
Route::get('/transaction', [TransactionController::class, 'showTransactionForm']);
Route::post('/transaction', [TransactionController::class, 'checkTransactionStatus']);


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

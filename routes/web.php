<?php

use App\Http\Controllers\AddTransactionController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [AddTransactionController::class, 'index']);
Route::post('/', [AddTransactionController::class, 'formSubmit']);

Route::post('select_recipient', [AddTransactionController::class, 'selectRecipient'])->name('select_recipient');
Route::post('sender_max_amount', [AddTransactionController::class, 'senderMaxAmount'])->name('sender_max_amount');

<?php

use App\Http\Controllers\PlaidApiController;
use App\Http\Controllers\Account;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/plaid/create-link-token', [PlaidApiController::class, 'createLinkToken']);
Route::post('/plaid/exchange-public-token', [PlaidApiController::class, 'exchangePublicToken']);
Route::post('/plaid/monthly-transactions', [PlaidApiController::class, 'getMonthlyTransactions']);
Route::get('/plaid/transactions', [PlaidApiController::class, 'getTransactions']);
Route::get('/plaid/get-bank-data', [PlaidApiController::class, 'getBankData']);
Route::get('/plaid/get-account-identity', [PlaidApiController::class, 'getAccountIdentity']);
Route::get('/plaid/get-account-balance', [PlaidApiController::class, 'getAccountBalance']);


Route::get('/up/get-connected-banks', [Account::class, 'getConnectedBanks']);

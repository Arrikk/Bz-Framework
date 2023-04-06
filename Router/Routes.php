<?php

use App\Controllers\Finance\Wallets\Wallets;
use Core\Router\Router as Route;
Route::get('', 'home@index');

// Auth Route
Route::post('register', 'users@register@auth');
Route::post('login', 'users@login@auth');

// Password
Route::post('api/password/change', 'password@change@auth');
Route::post('api/password/forgot', 'password@forgot@auth');
Route::post('api/password/reset', 'password@reset@auth');
Route::get('api/password/token/{token:[\da-f]+}', 'password@token@auth');

// Profile
Route::get('api/account', 'account@profile');
Route::post('api/account', 'account@update');

// MY

Route::get('my/wallets', [Wallets::class, 'wallet-balance']);

// Finance Routes
Route::get('wallets', [Wallets::class, 'get']);
Route::post('wallets', [Wallets::class, 'create']);
Route::post('credit', [Wallets::class, 'credit']);
Route::post('debit', [Wallets::class, 'debit']);




<?php

use App\Controllers\Company\Companies;
use App\Controllers\Features\Features;
use App\Controllers\Finance\Wallets\Wallets;
use App\Controllers\Members\Members;
use App\Controllers\Plans\Plans;
use App\Controllers\Stripe\Checkout\CallbackUrl;
use App\Controllers\Stripe\Portal;
use App\Controllers\Stripe\Webhook\Webhook;
use App\Controllers\Subscriptions\Subscriptions;
use Core\Router\Router as Route;

Route::get('', 'home@index');

// Auth Route
Route::post('register', 'users@register@auth');
Route::post('login', 'users@login@auth');

// Password
Route::put('password/change', 'password@change@auth');
Route::post('password/forgot', 'password@forgot@auth');
Route::post('password/reset', 'password@reset@auth');
Route::get('password/token/{token:[\da-f]+}', 'password@token@auth');

// Profile
Route::get('profile', 'account@profile');
Route::put('profile', 'account@update')->guard('member');

// MY

Route::get('my/wallets', [Wallets::class, 'wallet-balance']);

// Finance Routes
Route::get('wallets', [Wallets::class, 'get']);
Route::post('wallets', [Wallets::class, 'create']);
Route::post('credit', [Wallets::class, 'credit']);
Route::post('debit', [Wallets::class, 'debit']);

//------------------------------------------------------------------------------------------------
// ========================= COMPANIES ===================================
//------------------------------------------------------------------------------------------------

Route::put('company', [Companies::class, 'update-company'])->guard('admin');
Route::get('company', [Companies::class, 'company'])->guard(['admin', 'member']);
//------------------------------------------------------------------------------------------------
// ========================= MANAGERS ===================================
//------------------------------------------------------------------------------------------------
Route::post('account/member', [Members::class, 'add-member'])->guard('admin');
Route::get('account/members', [Members::class, 'members']);
Route::put('account/member', [Members::class, 'update-member'])->guard('admin');

//------------------------------------------------------------------------------------------------
// ========================= FEATURES  ===================================
//------------------------------------------------------------------------------------------------
Route::post('plan-features', [Features::class, 'create-feature']);
Route::get('plan-features', [Features::class, 'get-feature']);
Route::put('plan-features/{id:[\d]+}', [Features::class, 'update-feature']);
//------------------------------------------------------------------------------------------------
// ========================= SUBSCRIPTION PLAN ===================================
//------------------------------------------------------------------------------------------------
Route::post('subscription-plan', [Plans::class, 'create-plan']);
Route::get('subscription-plan', [Plans::class, 'get-plan']);
//  -----------------------------subscribe---------------------------
Route::post('subscribe', [Subscriptions::class, 'subscribe']);
Route::post('subscriptions', [Subscriptions::class, 'subscribe']);
Route::get('subscription', [Subscriptions::class, 'subscription']);
Route::post('manage-subscription', [Portal::class, 'portal']);

Route::get('payment/success', [CallbackUrl::class, 'success']);
Route::get('payment/cancel', [CallbackUrl::class, 'cancel']);
Route::post('webhook', [Webhook::class, 'webhook']);
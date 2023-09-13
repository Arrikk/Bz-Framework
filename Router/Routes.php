<?php

use App\Controllers\Company\Companies;
use App\Controllers\Features\Features;
use App\Controllers\FileManager\Files\Files;
use App\Controllers\FileManager\Folders\Folders;
use App\Controllers\Finance\Wallets\Wallets;
use App\Controllers\Home;
use App\Controllers\Members\Members;
use App\Controllers\Plans\Plans;
use App\Controllers\Podcast\Categories;
use App\Controllers\Podcast\Podcasts;
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
Route::post('profile', 'account@update');

// MY

Route::get('my/wallets', [Wallets::class, 'wallet-balance']);

// Finance Routes
Route::get('wallets', [Wallets::class, 'get']);
Route::post('wallets', [Wallets::class, 'create']);
Route::post('credit', [Wallets::class, 'credit']);
Route::post('debit', [Wallets::class, 'debit']);

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

// -------------------------------------------------------------------------------------
// ======================= DOCUMENTS AND FILES MANAGER =======================================
//------------------------------------------------------------------------------------------

Route::post('folder-create', [Folders::class, 'create']); // Create a new folder
Route::get('folders-get', [Folders::class, 'folders']); // Get all my Folders
Route::get('folder-get', [Folders::class, 'folder']); // Get a folder by their params
Route::post('folder-update', [Folders::class, 'update-folder']); // Get a folder by their params
Route::delete('folder-delete/{id:[\d\w]+}', [Folders::class, 'delete-folder']); // Get a folder by their params

Route::post('file-upload', [Files::class, 'upload']);
Route::delete('file-delete', [Files::class, 'delete-perm']);
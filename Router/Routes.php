<?php

use Core\Router\Router as Route;
Route::get('', 'home@index');

// Auth Route
Route::post('api/register', 'users@register@auth');
Route::post('api/login', 'users@login@auth');

// Password
Route::post('api/password/change', 'password@change@auth');
Route::post('api/password/forgot', 'password@forgot@auth');
Route::post('api/password/reset', 'password@reset@auth');
Route::get('api/password/token/{token:[\da-f]+}', 'password@token@auth');

// Profile
Route::get('api/account', 'account@profile');
Route::post('api/account', 'account@update');

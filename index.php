<?php

/**
 * Index Page
 * 
 * Created By Bruiz(@~codeHart~) 2022
 * 
 * PHP Version 8
 */

/**
 * Autoload
 */
require 'vendor/autoload.php';
/**
 * Add route to the Routing Table
 */

use Core\Request;
use Router\Route;

/**
 * Twig
 */
Twig_Autoloader::register();

ini_set('max_execution_time', 130);
date_default_timezone_set('Africa/Lagos');

/**
 * Error
 */

error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

// define('URL', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/');
/**
 * Session
 */
session_start();

Request::cors();
Route::Route();

// echo $_SERVER['REQUEST_SCHEME'];

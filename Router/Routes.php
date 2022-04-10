<?php

/**
 * Add routes
 */

// $router->add('', ['controller' => 'install', 'action' => 'index', 'namespace' => 'install']);
// $router->add('{controller}/{action}', ['namespace' => 'install']);
// Route automatically set to get

$router->add('', ['controller' => 'home', 'action' => 'index'])->get();
$router->add('login', ['controller' => 'auth', 'action' => 'login']);
$router->add('register', ['controller' => 'auth', 'action' => 'register']);
$router->add('form', ['controller' => 'home', 'action' => 'test'])->post();
$router->add('{controller}/{action}')->get();
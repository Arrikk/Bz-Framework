<?php
namespace Core\Interfaces;

interface Router {
    public static function post(string $route, $args = '') :Router;
    public static function get(string $route, $args = '') :Router;
    public static function put(string $route, $args = '') :Router;
    public static function delete(string $route, $args = '') :Router;
}
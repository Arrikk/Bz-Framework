<?php
namespace Core\Interfaces;

/**
 * Response Interface
 */

interface Resinterface 
{
    public static function status(int $status = 200);
    public static function json($message = null);
    public static function send($message = null);
    public static function raw($message = null);
    public static function ip();
}
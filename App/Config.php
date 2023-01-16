<?php
namespace App;

/**
 * Config Settings
 * 
 * PHP version 7.4.8
 */

class Config
{
    /**
     * DB Host
     * 
     * @var string
     */
    public static $DB_HOST = 'localhost';
    /**
     * DB name
     * 
     * @var string
     */
    public static $DB_NAME = 'naijaconnect';
    /**
     * DB username
     * 
     * @var string
     */
    public static $DB_USER = 'root';
    /**
     * DB Password
     * 
     * @var string
     */
    public static $DB_PASSWORD =  '';
    /**
     * Error
     * 
     * @var bool
     */
    CONST SHOW_ERROR = true;
    /**
     * Base Url
     * 
     * @var string
     */
    CONST BASE_URL = '';
    /**
     * Secret Key
     * 
     * @var string
     */
    CONST SECRET_KEY = 'Op9O+=/CO3eE+9+Cs222p/qdEFeneD';
    /**
     * Show flash message
     * 
     * @var bool
     */
    CONST FLASH = true;
    /**
     * Set default email address
     * 
     * @return string
     */
    CONST DEFAULT_EMAIL = '';

    const BASE_URL_REQUESTS = '';

    public static function clearDB()
    {
        $url = "mysql://b2decb00293d0c:1c909481@us-cdbr-east-06.cleardb.net/heroku_55850ce38f770e4?reconnect=true";

        $cleardbURl = parse_url(getenv('CLEARDB_DATABASE_URL'));

        // $cleardbURl = parse_url($url);

        self::$DB_HOST = $cleardbURl['host'] ?? 'localhost';
        self::$DB_PASSWORD = $cleardbURl['pass'] ?? 'mysql';
        self::$DB_USER = $cleardbURl['user'] ?? 'root';

        $name = $cleardbURl['path'] ?? null;
        if($name) $name = str_replace('/', '', $name);

        self::$DB_NAME =  $name ? $name : 'aruku';
    }
}
            
            
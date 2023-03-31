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
    public static $DB_HOST = '';
    /**
     * DB name
     * 
     * @var string
     */
    public static $DB_NAME = '';
    /**
     * DB username
     * 
     * @var string
     */
    public static $DB_USER = '';
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
    CONST SECRET_KEY = "";
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
        $cleardbURl = parse_url(getenv('CLEARDB_DATABASE_URL'));
        // $cleardbURl = parse_url($url);
        self::$DB_HOST = $cleardbURl['host'] ?? $_ENV['DB_HOST'];
        self::$DB_PASSWORD = $cleardbURl['pass'] ?? $_ENV['DB_PASSWORD'];
        self::$DB_USER = $cleardbURl['user'] ?? $_ENV['DB_USER'];
        self::$DB_NAME = str_replace('/', '', ($cleardbURl['path'] == "" ? $_ENV['DB_NAME'] : $cleardbURl['path']));
    }
}
            
            
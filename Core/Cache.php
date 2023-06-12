<?php

namespace Core;

use Core\Http\Res;

class Cache
{
    /**
     * @var string cache storage path
     */
    const CACHE_STORAGE = 'Cache/';

    public static function get($name)
    {
        $cache = self::CACHE_STORAGE . strtolower(str_replace([' ', '-'], '_', $name)) . '.txt';
        if (!file_exists($cache))
            return null;
        $data = file_get_contents($cache);
        return $data;
    }

    public static function set($name, $data)
    {
        $cache = self::CACHE_STORAGE . strtolower(str_replace([' ', '-'], '_', $name)) . '.txt';

        if (!is_dir(self::CACHE_STORAGE)) {
            mkdir(self::CACHE_STORAGE);
        }

        $file = fopen($cache, 'w');
        fwrite($file, $data);
        fclose($file);
        // file_put_contents($cache, $data);
        return true;
    }
}

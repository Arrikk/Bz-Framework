<?php

namespace App;

/**
 * Token Hashes
 */

class Token
{
    /**
     * The token value
     * 
     * @var string
     */
    protected $token;
    private static $decoded;

    /**
     * Class Constructor create a new random token
     * 
     * @return void
     */
    public function __construct($token =  null)
    {
        if ($token) {
            $this->token = $token;
        } else {
            $this->token = bin2hex(random_bytes(16));
        }
    }

    /**
     * Get token value
     * 
     * @return string The value
     */
    public function getValue()
    {
        return $this->token;
    }

    /**
     * Get Hashed token
     * 
     * @return string
     */
    public function getHashed()
    {
        return hash_hmac('sha256', $this->token, SECRET_KEY);
    }

    /**
     * Encrypt and decrypt data ..(message, string, int, func etc...)
     * @param string $type Encrypt = enc Decrypt = dec
     * @param string $string any
     * @return string
     */
    public static function mkToken($type, $string)
    {
        $output = '';

        $enc_type = 'AES-256-CBC';
        $secret = SECRET_KEY;
        $secret_iv = \substr($secret, 0, 14);

        $key = \hash('sha256', $secret);
        $initVect = \substr(\hash('sha256', $secret_iv), 0, 16);

        if ($type == 'enc') {
            $output = \openssl_encrypt($string, $enc_type, $key, 0, $initVect);
            $output = \base64_encode($output);
        }
        if ($type == 'dec') {
            $output = \base64_decode($string);
            $output = \openssl_decrypt($output, $enc_type, $key, 0, $initVect);
        }

        return $output;
    }

    public static function encodeJSON(array $data)
    {
        return self::mkToken('enc', json_encode($data, JSON_FORCE_OBJECT));
    }

    public static function encode($data)
    {
        return self::mkToken('enc', ($data));
    }

    public static function decode(string $encoded)
    {
        return self::mkToken('dec', $encoded);
    }

    public static function decodeJSON(string $decoded)
    {
        return json_decode(self::mkToken('dec', $decoded));
    }

    static function uuid() {
        return bin2hex(openssl_random_pseudo_bytes((14)));
    }

    public function string()
    {
        return  self::$decoded;
    }
}

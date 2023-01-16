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
     * @var array
     */
    protected $token;

    /**
     * Class Constructor create a new random token
     * 
     * @return void
     */
    public function __construct($token =  null){
        if($token){
            $this->token = $token;
        }else{
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
        return hash_hmac('sha256', $this->token, \App\Config::SECRET_KEY);
    }
}
<?php

namespace App;

use App\Models\User;
use App\Models\RememberedLogin;

/**
 * Authentication
 *
 * PHP version 7.0
 */
class Auth
{
    /**
     * Login the user
     *
     * @param User $user The user model
     * @param boolean $remember_me Remember the login if true
     *
     * @return void
     */
    public static function login($user, $remember_me = null)
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->id;
        if($remember_me){
            if($user->rememberLogin()){
                setcookie('Remember', $user->token_value, $user->expiry, '/');
            }
        }
    }
    public static function loginAdmin($user, $remember_me = null)
    {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $user->id;

        if($remember_me){
            if($user->rememberLogin()){
                setcookie('Remember', $user->token_value, $user->expiry, '/');
            }
        }
    }

    /**
     * Logout the user
     *
     * @return void
     */
    public static function logout()
    {
      // Unset all of the session variables
      $_SESSION = [];

      // Delete the session cookie
      if (ini_get('session.use_cookies')) {
          $params = session_get_cookie_params();

          setcookie(
              session_name(),
              '',
              time() - 42000,
              $params['path'],
              $params['domain'],
              $params['secure'],
              $params['httponly']
          );
      }

      // Finally destroy the session
      session_destroy();
      static::forgetLogin();
    }

    /**
     * Remember the originally-requested page in the session
     *
     * @return void
     */
    public static function rememberRequestedPage()
    {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }

    /**
     * Get the originally-requested page to return to after requiring login, or default to the homepage
     *
     * @return void
     */
    public static function getReturnToPage()
    {
        return $_SESSION['return_to'] ?? '/';
    }

    /**
     * Get the current logged-in user, from the session or the remember-me cookie
     *
     * @return mixed The user model or null if not logged in
     */
    public static function getUser()
    {
        if(isset($_SESSION['user_id'])){
            $user = User::getUser($_SESSION['user_id']);
            return $user;
        }else{
            $user = static::loginFromRememberedCookie();
            return $user;
        }
    }
    /**
     * Get the current logged-in user, from the session or the remember-me cookie
     *
     * @return mixed The user model or null if not logged in
     */
    public static function getAdmin()
    {
        if(isset($_SESSION['admin_id'])){
            $user = User::findById($_SESSION['admin_id']);
            return $user;
        }
    }

    /**
     * Login from a remembered login cookie
     * 
     * @return mixed The user model if login null otherwise
     */
    protected static function loginFromRememberedCookie()
    {
        $cookie = $_COOKIE['Remember'] ?? false;
        if($cookie){
            $remember = RememberedLogin::findByToken($cookie);
            if($remember && $remember->notExpired()){
                $user = $remember->getUser();
                self::login($user, null);
            }
        }
    }
    
    /**
     * Forget RememberedLogin
     * 
     * @return void
     */
    protected static function forgetLogin()
    {
        $cookie = $_COOKIE['Remember'] ?? false;
        if($cookie){
            $forget = RememberedLogin::findByToken($cookie);
            if($forget){
                if($forget->delete()){
                    setcookie('Remember', '', time() - 60*60*24*30);
                }
            }
        }
    }
}

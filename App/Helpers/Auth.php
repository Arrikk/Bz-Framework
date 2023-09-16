<?php

namespace App\Helpers;

use App\Models\User;
use App\Token;
use Core\Http\Res;

class Auth extends User
{
    /**
     * Add incorrect logins to log
     * @return bool
     */
    private static function badLogin()
    {
        $ip = IP_ADDRESS;
        if (empty($ip)) return true;

        return (self::use('bad_login')::dump([
            'ip' => $ip,
            'time' => CURRENT_TIME
        ], 'bad_login'));
    }

    /**
     * Create login session
     * get user information who is logginin
     * store in session db
     * @param int $userId -> id of user to store session for
     * @param string $platform -> plattform device of user.... (web, app)
     * 
     */
    public static function loginSession(int $userId, string $platform = 'web')
    {
        $hash = sha1(rand(111111111, 999999999)) . md5(microtime()) . rand(11111111, 99999999) . md5(rand(5555, 9999));
        self::use('sessions')::findAndDelete(['session_id' => $hash]);

        $qr = self::use('sessions')::dump([
            'user_id' => $userId,
            'session_id' => $hash,
            'platform' => $platform,
            'platform_details' => serialize(BROWSER),
            'time' => CURRENT_TIME
        ], 'sessions');

        if ($qr) return $qr;
    }

    /**
     * Confirm if a user can login
     * @return bool
     */
    private function canLogin(): bool
    {
        $ip = IP_ADDRESS;
        if (empty($ip)) return true;

        $logins = self::find(['ip' => $ip]);
        if (count((array) $logins) > LOGIN_LIMIT) return false;
    }

    public static function loginWithMetamask($token)
    {
        $account = User::findOne(['metamask_token' => $token],);
        if (!$account) :
            $account = User::dump([
                'metamask_token' => $token,
            ]);
        endif;

        $token = Token::mkToken('enc', json_encode([
            'id' => $account->_id,
            '_id' => $account->id,
            'expires' => strtotime('+1MONTH')
        ]));
        return [
            'token' => $token,
            'user' => $account
        ];
    }

    public static function login($email, $password)
    {
        $loggedIn = User::authenticate(Secure($email), $password);
        if (!$loggedIn) {
            // self::badLogin();
            Res::status(400)->error([
                'identifier' => $email,
                'password' => $password,
                'message' => "Bad login credentials"
            ]);
        };

        if ($loggedIn) :
            $token =  Token::mkToken('enc', json_encode([
                'id' => $loggedIn->_id,
                '_id' => (int) $loggedIn->id,
                'expires' => strtotime('+1MONTH')

            ]));
            // self::loginSession($loggedIn->id);
            return $loggedIn->append(['token' => $token]);
        endif;
    }
}

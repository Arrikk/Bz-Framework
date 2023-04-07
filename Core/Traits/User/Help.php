<?php

namespace Core\Traits\User;

use App\Token;
use Core\Http\Res;

trait Help
{

    // ---------------------------------------------------------------
    // ---------------------------------------------------------------
    // =================== Save Remembered Login =====================
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------

    /**
     * Remember the login by inserting a new unique token into the remembered_logins table
     * for this user record
     *
     * @return boolean  True if the login was remembered successfully, false otherwise
     */
    public function rememberLogin()
    {
        $token = new Token();
        $token_hash = $token->getHashed();
        $this->token_value = $token->getValue();
        $this->expiry = time() + 60 * 60 * 24 * 30;

        self::dump([
            'token_hash' => $token_hash,
            'user_id' => $this->user_id,
            'expires_at' => date('Y-m-d H:i:s', $this->expiry)
        ], 'remembered_logins');
        return Res::send(true);
    }



    /**
     * 
     */
    public function verifyEmail()
    {
        self::findAndUpdate(
            [
                'email' => $this->email,
                'and.email_code' => $this->email_code
            ],
            [
                'verified' => VERIFIED,
                'email_code' => ''
            ]
        );
        Res::json(['verification' => "Email successfully verified"]);
    }

    // ---------------------------------------------------------------
    // ---------------------------------------------------------------
    // =================== Password Reset Starts =====================
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------

    /**
     * Verify user email to send Reset link
     * 
     * @param string $email user email
     */
    public static function sendPasswordReset($email)
    {
        $user = self::findByEmail($email);
        if (!$user) Res::status(400)->error(['message' => 'Request not found', "email" => "invalid email addrss"]);
        $user->useToken()->startPasswordReset();
        // if (!$user->forgotEmail()) Res::status(400)->json(['error' => 'Unable to send verification email']);
        Res::json(['message' => 'Email Successfully sent']);
    }

    /**
     * Start password reset by generating a new token and expiry
     * 
     * @return mixed
     */
    public function startPasswordReset()
    {
        if (isset($this->useOtp)) return;

        $token = new Token();
        $token_hash = $token->getHashed();
        $this->token = $token->getValue();

        $expiry = time() + 60 * 60 * 2;
        return self::findAndUpdate(
            ['id' => $this->id],
            [
                'password_reset_hash' => $token_hash,
                'password_reset_expiry' => date('Y-m-d H:i:s', $expiry)
            ]
        );
    }

    public function useToken()
    {
        $this->useToken = true;
        $token = new Token();
        $this->token = $token->getValue();
        $this->hashed = $token->getHashed();
        return $this;
    }

    public function useOtp()
    {
        $this->useOtp = true;
        $this->token = RANDOM_CODE;
        self::findAndUpdate(
            ['id' => $this->id],
            ['email_code' => $this->token]
        );
        return $this;
    }

    /**
     * Find user Model by token
     * 
     * @param string $token User token
     * 
     * @return mixed
     */
    public static function findByPasswordReset($token)
    {
        $token = new Token($token);
        $token_hash = $token->getHashed();
        $user = static::findOne([
            'password_reset_hash' => $token_hash
        ]);
        if (!$user) Res::status(400)->json(['error' => 'Invalid Token']);

        if (strtotime($user->password_reset_expiry) > time()) return $user;
        Res::status(400)->json(['error' => 'Token Expired']);
    }

    /**
     * Verify  Password 
     * 
     * @return mixed
     */
    public function verifyPassword($password)
    {
        if (\password_verify($password, $this->password_hash)) {
            return true;
        }
        return false;
    }

    /**
     * Reset account Password
     * 
     * @param string $password New password
     * 
     * @return void
     */
    public function resetPassword($password)
    {
        $this->password = $password;
        // $this->validate();
        $password = password_hash($this->password, PASSWORD_DEFAULT, ['cost' => 11]);

        $success =  static::findAndUpdate(
            ['id' => $this->id],
            [
                'password_hash' => $password,
                'password_reset_hash' => NULL,
                'password_reset_expiry' => NULL
            ]
        );
        if (!$success) return Res::status(400)->json(['error' => 'Password Reset Failed']);
        Res::json(['message' => 'Password Successfully Changed']);
    }
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------
    // ================= Email Activation Processes ==================
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------

    /**
     * Send email activation link
     * 
     * @return void
     */
    public function sendEmailActivation()
    {
        // if(isset($this->useOtp))
        $this->expiry = date('y-m-d H:i:s', time() + 60 * 5);
        if ($activation = $this->useOtp()) {
            if ($activation->activationEmail()) {
                return Res::send(true);
            }
        }
    }

    /**
     * Start Email activation process 
     * 
     * @return bool
     */
    protected function startEmailReset()
    {
        if (isset($this->useToken))
            return static::findAndUpdate([
                'id', $this->id
            ], [
                'password_reset_hash' => $this->hashed,
                'password_reset_expiry' => $this->expiry
            ]);
        return $this;
    }
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------
    // ==================== Send Email Templates =====================
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------
    protected function activationEmail()
    {
        $to = $this->email;
        $from = SMTP_USERNAME;
        $subject = 'Verify you want to use this emaill address';
        $body = \Core\View::template('emailTemplates/emails_activate.html', [
            'email' => $this->email,
            'token' => $this->token,
            'URL' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
        ]);
        return \App\Mail::mail($to, $from, $subject, $body);
    }

    protected function welcomeEmail()
    {
        $to = $this->email;
        $from = SMTP_USERNAME;
        $subject = 'Thank you for signing up';
        $body = \Core\View::template('emailTemplates/emails_welcome.html', [
            'email' => $this->email,
            'URL' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
        ]);
        return \App\Mail::mail($to, $from, $subject, $body);
    }

    protected function forgotEmail()
    {
        $to = $this->email;
        $from = SMTP_USERNAME;
        $subject = 'Reset Account Password';
        $body = \Core\View::template('emailTemplates/emails_forgot.html', [
            'email' => $this->email,
            'token' => $this->token,
            'URL' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
        ]);
        return \App\Mail::mail($to, $from, $subject, $body);
    }
}

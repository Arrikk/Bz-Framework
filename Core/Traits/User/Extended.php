<?php

namespace Core\Traits\User;

use App\Models\User;
use App\Token;
use Core\Http\Res;

/**
 * User model
 *
 * PHP version 7.4.8
 */
trait Extended
{
    /**
     * ************************************************************************ 
     * **(Use ClassName/self/static::find() to find many)********************** 
     * **(Use ClassName/self/static::findOne() to find one)********************
     * **(Use ClassName/self/static::findAndUpdate() to find and update)*******
     * **(Use ClassName/self/static::findAndDelete() to find and delete)*******
     * **(Use ClassName/self/static::findAndByEmail() to find by email)********
     * **(Use ClassName/self/static::findAndById() to find by Id*******)*******
     * ************************************************************************ 
     */

    /**
     * Save the user model with the current property values
     *
     * @return boolean  True if the user was saved, false otherwise
     */
    public static function save($data = null)
    {
        $This = new static($data);
        $token = new Token();
        $This->hashed = $token->getHashed();
        $This->token = $token->getValue();

        $This->validate();
        if (!empty($This->errors)) Res::status(400)::error($This->errors);

        $password = password_hash($This->password_hash, PASSWORD_DEFAULT);
        $data->password_hash = $password;

        $user = $This->dump((array) $data)->remove('password_hash');
        return $user;
    }

    public static function isVerified($id)
    {
        $user = self::findOne(['id' => $id], 'is_verified');
        if ($user) return $user->is_verified;
        else Res::status(404)->json(['error' => 'User not found']);
    }

    /**
     * Delete a user account
     * @param int $id id of user to update
     * @return bool
     */
    public static function deleteUser($id)
    {
        return self::findAndDelete(['id' => $id]);
    }

    // public static function getUser()
    // {
    //     # code...
    // }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    protected function validate()
    {
        if (isset($this->email))
            if ($this->userExists($this->email, $this->id ?? null))
                $this->errors['email'] = 'Email already exists';

        if (isset($this->username))
            if ($this->userExists($this->username, $this->id ?? null))
                $this->errors['username'] = 'Username already exists';
        // if (isset($this->password) && !empty($this->password)) {
        // if ($this->password == '')
        //     $this->errors[] = 'Password cannot be empty';
        // if (!preg_match('/.*\d+.*/', $this->password)) $this->errors[] = 'Password Must contain atleast a number';
        // // }

        return true;
    }

    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
    public static function emailExists($email, $ignore_id = null)
    {
        $user = self::findByEmail($email);
        if ($user) :
            if ($user->id !== $ignore_id) {
                return true;
            }
        endif;
        return false;
    }
    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
    public static function userExists($user, $ignore_id = null)
    {
        $user = self::findOne(['username' => $user, 'or.email' => $user]);
        if ($user) :
            if ($user->id !== $ignore_id) {
                return true;
            }
        endif;
        return false;
    }

    /**
     * Authenticate a user
     * @param string $email user email
     * @param string $password user password
     * @return object
     */
    public static function authenticate($email, $password)
    {
        // extract($array);
        $user = User::findByEmail($email);

        if (!$user) return false;
        if (!password_verify($password, $user->password_hash)) return false;
        return $user;
    }

    public static function getUserById($id)
    {
        $user = self::findOne(['id' => $id]);
        if (!$user) Res::status(404)->json(['error' => 'User not Found']);
        return $user;
    }

    public static function getUserBy($data)
    {
        $user = self::findOne($data);
        if (!$user) Res::status(404)->json(['error' => 'User not Found']);
        return $user;
    }
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------
    // =================== Update Account =====================
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------

    public function updateUser($update)
    {
        // $this->validate();

        # Set update pref according to form data

        if (empty($this->errors))
            return self::findAndUpdate(['id' => $this->id], $update);
        return Res::status(400)->json($this->errors);
    }

    //===================================================== ===============
    //===================================================== ===============
    //===================================================== ===============
    //===================================================== ===============
    //===================================================== ===============
    //===================================================== ===============

}

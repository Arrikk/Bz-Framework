<?php

namespace App\Models;

use App\Models\Wallet\Transactions;
use App\Models\Lists\Blocked;
use App\Models\Lists\Bookmark;
use App\Models\Lists\CloseFriends;
use App\Models\Lists\Lists;
use App\Models\Lists\Restrict;
use App\Models\Settings\Display;
use App\Models\Settings\Privacy;
use App\Models\Subscriptions\Subscriptions;
use App\Models\Subscriptions\SubscriptionSettings;
use App\Models\User\Friends;
use App\Models\Wallet\Wallet;
use App\Token;
use Core\Traits\Model;
use Core\Http\Res;
use Module\Image;

/**
 * User model
 *
 * PHP version 7.4.8
 */
class User extends \Core\Model
{
    use Model; # Use trait only if using the find methods

    /**
     * Each model class requires a unique table base on field
     * @return string $table ..... the table name e.g 
     * (users, posts, products etc based on your Model)
     */
    public static $table = "users"; # declear table only if using traitModel
    public static $error = [];

    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if ($key == 'request') continue;
            $this->$key = $value;
        }
    }

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
    public function save()
    {
        $token = new Token();
        $this->hashed = $token->getHashed();
        $this->token = $token->getValue();

        $this->validate();

        if (empty($this->errors)) {

            $password = password_hash($this->password, PASSWORD_DEFAULT);
            $this->time = date('y-m-d H:i:s', time() + 60 * 60 * 3);
            $user = static::dump([
                'email' => static::clean($this->email),
                'username' => static::clean($this->username),
                'display_name' => static::clean($this->name),
                'is_admin' => $this->is_admin ?? 0,
                'password_hash' => $password,
                'password_reset_hash' => $this->hashed,
                'password_reset_expiry' => $this->time
            ]);
            if (!$user) return Res::status(500)->send('Server Error');
            $user->referralCode = $this->referral ?? '';
            $this->makeDefaultSettings($user);
            return $this->getUser($user->id);
        };
        return Res::status(400)->json($this->errors);
    }

    private function makeDefaultSettings($user, $name = '')
    {
        $id = $user->id;
        Wallet::createWallet($id);
        Friends::makeFriends($id);
        CloseFriends::makeCloseFriends($id);
        Blocked::makeBlock($id);
        Bookmark::makeBookmark($id);
        Restrict::makeRestrict($id);
        Display::display($id);
        Privacy::privacy($id, SHOW_ACTIVITY);
        Privacy::privacy($id, SHOW_SUBSCRIPTION);
        Referrals::make($user);
        SubscriptionSettings::set($id);
    }

    public static function isVerified($id)
    {
        $user = self::findOne(['id' => $id], 'is_verified');
        if ($user) return $user->is_verified;
        else Res::status(404)->json(['error' => 'User not found']);
    }

    /**
     * Update a user account
     * @param int $id id of user to update
     * @param array>object $item Data fields
     * @return bool>object
     */
    public static function updateUser($id, $item, $file = null)
    {
        $data = [];
        foreach ($item as $key => $value) {
            if ($key == 'request') continue;
            $data[$key] = is_int($key) ?
                (int) static::clean($value) :
                (string) static::clean($value);
        }
        # if profile images are avaliable
        if (isset($file)) :
            if (isset($file['avatar'])) :
                $avatar = Image::upload($file, 'avatar');
                $data['avatar'] = $avatar->fullpath;
            endif;
            if (isset($file['profileCover'])) :
                $cover = Image::upload($file, 'profileCover');
                $data['profile_cover'] = $cover->fullpath;
            endif;
        endif;
        
        if (!empty($data)) :
            $user = static::findAndUpdate(['id' => $id], $data);
            return self::getUser($user->id);
        endif;
    }

    /**
     * Delete a user account
     * @param int $id id of user to update
     * @return bool
     */
    public static function deleteUser($id)
    {
        return User::findAndDelete(['id' => $id]);
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
        if ($this->email == '')
            $this->errors[] = 'Email is required';

        if (!isset($this->name) || $this->name == '')
            $this->errors[] = 'Choose a display name';

        if ($this->emailExists($this->email, $this->id ?? null))
            $this->errors[] = 'Email already exists';

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL))
            $this->errors[] = 'Invalid Email';

        // if (isset($this->password) && !empty($this->password)) {
        if ($this->password == '')
            $this->errors[] = 'Password cannot be empty';
        if (!preg_match('/.*\d+.*/', $this->password)) $this->errors[] = 'Password Must contain atleast a number';
        // }

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
        if (!$user) return Res::status(400)->json(['Invalid Email Address']);
        if (!password_verify($password, $user->password_hash)) return Res::status(400)->json(['Password Mismatch']);
        return $user;
    }

    public static function getUserById($id)
    {
        $user = self::findOne(['id' => $id, 'or.username' => $id]);
        if (!$user) Res::status(404)->json(['error' => 'User not Found']);
        return $user;
    }

    public static function getUserMinified($userId)
    {
        $user = self::findById($userId, 'id', 'username, is_verified, id, email, display_name, phone_number, avatar');
        if (!$user) Res::status(404)->json(['error' => 'User Not Found']);
        return (object) [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'display_name' => $user->display_name,
            'phone_number' => $user->phone_number,
            'avatar' => $user->avatar,
            'isVerified' => $user->is_verified == 1
        ];
    }

    /**
     * Get profile details of a user
     * @param int $id ... id of ther user to get
     * @param int $user ... current loggedin user
     * 
     */
    public static function getUser($id, $user = null, $withFriends = false)
    {
        # check if this current logged in user is not null
        # if the user is not null check if userId to find is
        # not in the current user filtered lists
        $blocked = false;
        $restricted = false;
        $bookmark = false;
        $closeFrnds = false;
        $followers = false;
        $followings = false;
        $isSubscribedToUserProfile = false;

        if ($user) :
            $blocked = Lists::inList($user, BLOCKED, $id);
            $restricted = Lists::inList($user, RESTRICT, $id);
            $bookmark = Lists::inList($user, BOOKMARK, $id);
            $closeFrnds = Lists::inList($user, CLOSE_FRIENDS, $id);
            $followers = Friends::isFollower($user, $id);
            $followings = Friends::isFollowing($user, $id);
            $loggedIn = User::findById($user);
            # Check if current loggedIn user is subscribed to userToGet Profile
            $isSubscribedToUserProfile = Subscriptions::isSubscribed($id, $user);
            $subscription = Subscriptions::subscription($user, $id);
        endif;


        $_ = User::findById($id);
        $b = Wallet::getWallet($id);

        # Get the user to check subscription settings
        $userSubscriptionSetting = SubscriptionSettings::subSettings($id);


        $resp = [
            'userId' => $_->id,
            'username' => $_->username,
            'name' => $_->display_name,
            'email' => !$user ? $_->email : 'hidden',
            'bio' => !$user ? $_->bio : 'hidden',
            'location' => $_->location,
            'avatar' => $_->avatar,
            'website_url' => $_->website_url,
            'amazon_url' => $_->amazon_url,
            'phone_number' => !$user ? $_->phone_number : 'hidden',
            'isActive' => $_->is_active ? true : false,
            'isVerified' => $_->is_verified ? true : false,
            'joinedOn' => $_->createdAt,
            'wallet' => !$user ? $b->balance : 'hidden',
            'subscriptionSetting' => $userSubscriptionSetting,
        ];

        $isMyProfile = $user == $_->id;
        $resp['isMyProfile'] = $isMyProfile;

        if ($user && !$isMyProfile) :
            $needsRenewal = $subscription->needsRenewal ?? false;
            if ($isMyProfile) $needsRenewal = false;
            $resp = array_merge($resp, [
                'isBlocked' => $blocked ? true : false,
                'isRestricted' => $restricted ? true : false,
                'isCloseFriend' => $closeFrnds ? true : false,
                'isBookmarked' => $bookmark ? true : false,
                'isFollowing' => $followers ? true : false,
                'isFollower' => $followings ? true : false,
                'canFollow' => !$followings ? true : false,
                'canChat' => !$restricted ? true : false,
                'canRestrict' => $restricted ? false : true,
                'canBlock' => $blocked ? false : true,
                'canViewProfile' => $blocked ? false : true,
                'canViewFeed' => !$blocked && !$needsRenewal ? true : false,
                'canLikeFeed' => !$blocked  || !$needsRenewal ? true : false,
                'canCommentFeed' => $blocked || $restricted || $needsRenewal ? false : true,
                'canReceiveChatMessage' => $blocked || $restricted ? false : true,
                'isSubscribed' => $isSubscribedToUserProfile,
                'canSubscribe' => !$isSubscribedToUserProfile,
                'needsRenewal' => $subscription->needsRenewal ?? false,
                'subscription' => $subscription,
            ]);
        endif;

        if ($user && $loggedIn->is_admin) {
            $resp['wallet'] = $b->balance;
            $resp['email'] = $_->email;
            $resp['phone_number'] = $_->phone_number;
        }

        return (object) $resp;
    }

    public static function users($admin = null, $extra = null)
    {

        $currentPage = $extra->page ?? 1;
        $currentPage = (int) $currentPage;
        $limit = $extra->limit ?? LIMIT;
        $order = $extra->order ?? DESC;


        if ($currentPage < 1) $currentPage = 1;
        $startAt = ($currentPage - 1) * $limit;

        $users = self::find([
            '$.order' => "id $order",
            '$.limit' => "$startAt, $limit"
        ]);

        $res = [];
        foreach ($users as $user) {
            $res[] = self::getUser($user->id, $admin);
        }
        return $res;
    }

    public static function userListCount($limit = null)
    {
        $totalUsers = static::find([], 'count(*) as totalUsers');
        return [
            'total' => $totalUsers[0]->totalUsers,
            'limit' => $limit->limit ?? LIMIT
        ];
    }

    /**
     * Get full Profile details of a user
     * @param int $userId id of user to get
     * @return array
     */
    public static function getFullProfile($userId)
    {
        $userMin = User::getUserMinified($userId);
        $userId = $userMin->id;
        $kyc = Kyc::kycByUser($userId);
        $wallet = Wallet::getWallet($userId);
        $withdrawals = Withdrawals::withdrawalHistory($userId);
        $transactions = Transactions::transactionByUser($userId);


        return [
            'wallet' => $wallet,
            'details' => $userMin,
            'withdrawals' => $withdrawals,
            'kyc' => $kyc,
            'transactions' => $transactions
        ];
    }

    // ---------------------------------------------------------------
    // ---------------------------------------------------------------
    // =================== Update Account =====================
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------

    public function put(array $update = [])
    {
        extract($update);
        $this->email = $email;
        $this->validate();

        # Set update pref according to form data

        if (empty($this->errors))
            return User::findAndUpdate(['id' => $this->id], $update);
        return Res::status(400)->json($this->errors);
    }

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

        User::dump([
            'token_hash' => $token_hash,
            'user_id' => $this->user_id,
            'expires_at' => date('Y-m-d H:i:s', $this->expiry)
        ], 'remembered_logins');
        return Res::send(true);
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
        $user = User::findByEmail($email);
        if (!$user) Res::status(400)->json(['error' => 'Request not found']);
        $user->startPasswordReset();
        if (!$user->forgotEmail()) Res::status(400)->json(['error' => 'Unable to send verification email']);
        Res::json(['message' => 'Email Successfully sent']);
    }

    /**
     * Start password reset by generating a new token and expiry
     * 
     * @return mixed
     */
    public function startPasswordReset()
    {
        $token = new Token();
        $token_hash = $token->getHashed();
        $this->token = $token->getValue();

        $expiry = time() + 60 * 60 * 2;
        return User::findAndUpdate(
            ['id' => $this->id],
            [
                'password_reset_hash' => $token_hash,
                'password_reset_expiry' => date('Y-m-d H:i:s', $expiry)
            ]
        );
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
        $password = password_hash($this->password, PASSWORD_DEFAULT);

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
        $token = new Token();
        $this->token = $token->getValue();
        $this->hashed = $token->getHashed();
        $this->expiry = date('y-m-d H:i:s', time() + 60 * 5);
        if ($activation = $this->startEmailReset()) {
            if ($this->activationEmail()) {
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
        return static::findAndUpdate([
            'id', $this->id
        ], [
            'password_reset_hash' => $this->hashed,
            'password_reset_expiry' => $this->expiry
        ]);
    }
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------
    // ==================== Send Email Templates =====================
    // ---------------------------------------------------------------
    // ---------------------------------------------------------------
    protected function activationEmail()
    {
        $to = $this->email;
        $from = \App\Models\Settings::emailSetting()->smtp_username;
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
        $from = \App\Models\Settings::emailSetting()->smtp_username;
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
        $from = \App\Models\Settings::emailSetting()->smtp_username;
        $subject = 'Reset Account Password';
        $body = \Core\View::template('emailTemplates/emails_forgot.html', [
            'email' => $this->email,
            'token' => $this->token,
            'URL' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
        ]);
        return \App\Mail::mail($to, $from, $subject, $body);
    }




    //===================================================== ===============
    //===================================================== ===============
    //===================================================== ===============
    //===================================================== ===============
    //===================================================== ===============
    //===================================================== ===============
}

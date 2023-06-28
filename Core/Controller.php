<?php

namespace Core;

use App\Auth;
use App\Flash;
use Core\Http\Res;

/**
 * Base Controller
 * 
 * Php version 7.4.8
 */
abstract class Controller
{
    /**
     * Get all route Parameters
     * 
     * @return array
     */
    protected $route_params = [];

    protected $guard_params;

    /**
     * Contruct method Store route params
     */
    public function __construct($route_params = [], $guards = '')
    {
        $this->route_params = $route_params;
        $this->guard_params = $guards;
    }

    public function setGuard($value)
    {
        $this->guard_params = $value;
    }


    /**
     * Create an action Filter
     * Run code before and after an action is called
     * 
     * @param string $method Name of the action method
     * @param array $args Action Arguments
     */
    public function __call($method, $args)
    {
        $method = '_' . $method;
        if (method_exists($this, $method)) {

            if ($this->before() !== false) {

                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {

            throw new \Exception("Method $method in Controller class $this not Found");
        }
    }

    /**
     * Before action Filter
     * @return boolean
     */
    protected function before()
    {
        // eval($this->jwt('dec', ""));/
    }

    /**
     * After action filter
     * @return void
     */
    protected function after()
    {
    }

    /**
     * Redirect method
     * 
     * @param string $url the route param
     * 
     * @return void
     */
    protected function redirect($url)
    {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
        exit;
    }

    /**
     * Require the user to login before giving access to the requested Page
     * Remember the requested page for later, redirect to the login page
     * 
     * @return void
     */
    protected function requireLogin()
    {
        if (!Auth::getUser()) {
            Auth::rememberRequestedPage();
            // Flash::addMessage('Login First', Flash::INFO);
            return \http_response_code(401);
            $this->redirect('/login');
        }
    }

    /**
     * Require the user to login before giving access to the requested Page
     * Remember the requested page for later, redirect to the login page
     * 
     * @return void
     */
    protected function requireAdmin()
    {
        if (!Auth::getAdmin()) {
            $this->redirect('/master');
            return \http_response_code(401);
        }
    }

    protected function required(array $data = [])
    {
        $error = [];
        foreach ($data as $key => $value) {
            if ($value == '' || empty($value)) :
                $error[$key] = $key . ' is Required';
            endif;
        }
        if (!empty($error)) Res::status(400)->json(['error' => $error]);
        return true;
    }

    public function guard($guard) {
        $this->guardModel = $guard;
    }

    public static function requires(array $params = [])
    {
        foreach ($params as $key => $value) {
            [$val, $type] = explode('||', $value);
            $type = trim($type);
            $val = trim($val);
            if ($val !== '') {
                if ($type == 'int') {
                    if ((int) $val == 0) $error[$key] = "$key cannot be zero";
                    if (!preg_match('/^\d+$/', $val)) $error[$key] = "$key requires an integer";
                } elseif ($type == 'amount') {
                    if (!is_numeric($val)) $error[$key] = "$key requires a valid amount";
                } elseif ($type == 'string') {
                    if (!preg_match('/^([a-zA-Z_ ])+$/', $val)) $error[$key] = $key . ' requires A-Za-z';
                } elseif ($type == 'alnum') {
                    if (!preg_match('/^[a-zA-Z]\.*/', $val)) $error[$key] = $key . ' Must start with an alphabet';
                } elseif ($type == 'any') {
                    if (!preg_match('/\.*/', $val)) $error[$key] = $key . 'Invalid Characters';
                } elseif ($type == 'email') {
                    if (!filter_var($val, FILTER_VALIDATE_EMAIL)) $error[$key] = "Invalid email format";
                }
            } else {
                $error[$key] = 'Field Cannot be empty';
            }
        }

        if (!empty($error)) Res::status(400)->error($error);
        return true;
    }

    protected function contact($a, $b, $c)
    {

        $contacts = [];
        $names = [];
        $contact = '';
        for ($i = 0; $i < count($a); $i++) {
            if (count($a) == count($b)) {
                $contacts[] = "$a[$i], $b[$i]";
            }
        }
        foreach ($contacts as $contacts) {
            if (strrpos($contacts, $c) == true) {
                $names[] = $contacts;
            }
        }
        foreach ($names as $name) {
            $str = explode(',', $name);
            $contact .= $str[0] . ',';
        }

        if ($contact != '')
            return preg_replace('/[,]+$/', '', $contact);
        else
            return 'No Matched Contact';
        // return '<pre>'.htmlspecialchars(print_r($names, true)).'</pre>';


    }

    /**
     * Encrypt and decrypt data ..(message, string, int, func etc...)
     * @param string $type Encrypt = enc Decrypt = dec
     * @param string $string any
     * @return string
     */
    public function mkToken($type, $string)
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

    public function check()
    {
        $expdate = time() + 60 * 60 * 24 * 5;
        return $expdate;
    }
}

<?php

namespace Core;

use Core\Http\Res;

class Request
{
    public $request;
    function __construct($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    public static function cors()
    {

        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }

    public static function get()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $request = new Request($_GET);
        return $request;
    }
    public static function post()
    {
        self::cors();
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            Res::status(404)->json(['error' => "Post Not Found"]);
            if(empty($data) && empty($_POST) && empty($_FILES)) Res::status(403)->json(['error' => "Invalid Request"]);
            // Res::status(403)->json(['error' => $_FILES ?? $data]);
        $request = new Request($data ?? $_POST ?? $_FILES);
        return $request;
    }
    public static function put()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            throw new \Exception("POST not Found");
        $request = new Request($_POST);
        return $request;
    }
    public static function delete()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            throw new \Exception("POST not Found");
        $request = new Request($_POST);
        return $request;
    }
}

<?php

namespace Core\Http\Requests;

use App\Config;
use Core\Http\Requests\BaseReq;
use Core\Http\Res;

abstract class Bruiz extends BaseReq
{

    public $config = [];
    public $params;

    public $req;
    public $withBool = false;

    public function __construct($params = [], $header = [])
    {
        $this->req = curl_init();
        $this->setConfig($header);
        $this->params = ($params);
    }

    public function post($url = '', $useDefault = false)
    {

        $this->config[CURLOPT_URL] = $useDefault ? Config::BASE_URL_REQUESTS . $url : $url;
        $this->config[CURLOPT_CUSTOMREQUEST] = "POST";
        $this->config[CURLOPT_POSTFIELDS] = json_encode($this->params);
        return $this;
    }

    public function put($url = '', $useDefault = false)
    {

        $this->config[CURLOPT_URL] = $useDefault ? Config::BASE_URL_REQUESTS . $url : $url;
        $this->config[CURLOPT_CUSTOMREQUEST] = "PUT";
        $this->config[CURLOPT_POSTFIELDS] = json_encode($this->params);
        return $this;
    }

    public function get($url, $useDefault = false)
    {
        $this->config[CURLOPT_URL] = $useDefault ? Config::BASE_URL_REQUESTS . $url : $url;
        $this->config[CURLOPT_CUSTOMREQUEST] = "GET";
        return $this;
    }

    protected function setConfig(array $header = [])
    {
        $this->config = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => $header,
        );
    }

    public function withBool()
    {
        $this->withBool = true;
        return $this;
    }

    public function Call()
    {
        curl_setopt_array($this->req, $this->config);
        $response = curl_exec($this->req);
        $httpcode = curl_getinfo($this->req, CURLINFO_HTTP_CODE);
        curl_close($this->req);
        if ($httpcode >= 400) :
            $result = [
                'status' => false,
                'status_code' => $httpcode,
                'response' => json_decode($response)
            ];
            if ($this->withBool) return $result;
            Res::status($httpcode)->json($result);
        endif;
        return json_decode($response);
    }

    public function curl()
    {
        $this->req = curl_init();
        return $this;
    }
}

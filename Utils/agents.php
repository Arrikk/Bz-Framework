<?php

function GetIpAddress()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && ValidateIpAddress($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($iplist as $ip) {
                if (ValidateIpAddress($ip))
                    return $ip;
            }
        } else {
            if (ValidateIpAddress($_SERVER['HTTP_X_FORWARDED_FOR']))
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED']) && ValidateIpAddress($_SERVER['HTTP_X_FORWARDED']))
        return $_SERVER['HTTP_X_FORWARDED'];
    if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && ValidateIpAddress($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && ValidateIpAddress($_SERVER['HTTP_FORWARDED_FOR']))
        return $_SERVER['HTTP_FORWARDED_FOR'];
    if (!empty($_SERVER['HTTP_FORWARDED']) && ValidateIpAddress($_SERVER['HTTP_FORWARDED']))
        return $_SERVER['HTTP_FORWARDED'];
    return $_SERVER['REMOTE_ADDR'];
}

function ValidateIpAddress($ip)
{
    if (strtolower($ip) === 'unknown')
        return false;
    $ip = ip2long($ip);
    if ($ip !== false && $ip !== -1) {
        $ip = sprintf('%u', $ip);
        if ($ip >= 0 && $ip <= 50331647)
            return false;
        if ($ip >= 167772160 && $ip <= 184549375)
            return false;
        if ($ip >= 2130706432 && $ip <= 2147483647)
            return false;
        if ($ip >= 2851995648 && $ip <= 2852061183)
            return false;
        if ($ip >= 2886729728 && $ip <= 2887778303)
            return false;
        if ($ip >= 3221225984 && $ip <= 3221226239)
            return false;
        if ($ip >= 3232235520 && $ip <= 3232301055)
            return false;
        if ($ip >= 4294967040)
            return false;
    }
    return true;
}
function GetBrowser()
{
    $ub    = '';
    $u_agent  = $_SERVER['HTTP_USER_AGENT'];
    $bname    = 'Unknown';
    $platform = 'Unknown';
    $version  = '';
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub    = 'MSIE';
    } elseif (preg_match('/Firefox/i', $u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub    = 'Firefox';
    } elseif (preg_match('/Chrome/i', $u_agent)) {
        $bname = 'Google Chrome';
        $ub    = 'Chrome';
    } elseif (preg_match('/Safari/i', $u_agent)) {
        $bname = 'Apple Safari';
        $ub    = 'Safari';
    } elseif (preg_match('/Opera/i', $u_agent)) {
        $bname = 'Opera';
        $ub    = 'Opera';
    } elseif (preg_match('/Netscape/i', $u_agent)) {
        $bname = 'Netscape';
        $ub    = 'Netscape';
    }
    $known   = array(
        'Version',
        $ub,
        'other'
    );
    $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
    }
    $i = count($matches['browser']);
    if ($i != 1) {
        if (strripos($u_agent, 'Version') < strripos($u_agent, $ub)) {
            $version = $matches['version'][0];
        } else {
            $version = $matches['version'][1];
        }
    } else {
        $version = $matches['version'][0];
    }
    if ($version == null || $version == "") {
        $version = '?';
    }
    return array(
        'userAgent' => $u_agent,
        'name' => $bname,
        'version' => $version,
        'platform' => $platform,
        'pattern' => $pattern
    );
}
function GetDeviceType()
{
    $deviceName = '';
    $userAgent    = $_SERVER['HTTP_USER_AGENT'];
    $devicesTypes = array(
        'computer' => array(
            'msie 10',
            'msie 9',
            'msie 8',
            'windows.*firefox',
            'windows.*chrome',
            'x11.*chrome',
            'x11.*firefox',
            'macintosh.*chrome',
            'macintosh.*firefox',
            'opera'
        ),
        'tablet' => array(
            'tablet',
            'android',
            'ipad',
            'tablet.*firefox'
        ),
        'mobile' => array(
            'mobile ',
            'android.*mobile',
            'iphone',
            'ipod',
            'opera mobi',
            'opera mini'
        ),
        'bot' => array(
            'googlebot',
            'mediapartners-google',
            'adsbot-google',
            'duckduckbot',
            'msnbot',
            'bingbot',
            'ask',
            'facebook',
            'yahoo',
            'addthis'
        )
    );
    foreach ($devicesTypes as $deviceType => $devices) {
        foreach ($devices as $device) {
            if (preg_match('/' . $device . '/i', $userAgent)) {
                $deviceName = $deviceType;
            }
        }
    }
    return ucfirst($deviceName);
}
function GetDeviceToken()
{
    $finger_print               = array();
    $browser                    = GetBrowser();
    $finger_print['ip']         = GetIpAddress();
    $finger_print['browser']    = $browser['name'] . " " . $browser['version'];
    $finger_print['os']         = $browser['platform'];
    $finger_print['deviceType'] = GetDeviceType();
    $device                     = serialize($finger_print);
    return $device;
}

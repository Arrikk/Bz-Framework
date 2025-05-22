<?php

use function Clue\StreamFilter\fun;

require 'clean.php';
require 'agents.php';
function userFilters()
{

    return ['password_hash', 'email_code', 'src', 'type', 'password_reset_hash', 'paystack_ref', 'securionpay_key', 'password_reset_expiry', 'conversation_id'];
}

function getCurrentClass()
{
    $class = explode('\\', get_called_class());
    $class = strtolower(end($class)) . 's';
    return $class;
}

/**
 * Advanced JSON Decoder
 *
 * @param string $json The JSON data to decode.
 * @param bool $assoc Whether to return an associative array (true) or objects (false).
 * @param int $depth The maximum depth to traverse for nested JSON (default is 512).
 *
 * @return mixed The decoded JSON data.
 */
function jsonDecode($json, $assoc = true, $depth = 512) {

    if(!$json) return;
    // Attempt to decode the JSON data.
    $decodedData = json_decode($json, $assoc, $depth);

    // Check for JSON decoding errors.
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            return $decodedData; // No errors, return decoded data.
        case JSON_ERROR_DEPTH:
            throw new Exception('JSON decoding error: Maximum depth reached.');
        case JSON_ERROR_STATE_MISMATCH:
            throw new Exception('JSON decoding error: Invalid or malformed JSON.');
        case JSON_ERROR_CTRL_CHAR:
            throw new Exception('JSON decoding error: Unexpected control character found.');
        case JSON_ERROR_SYNTAX:
            throw new Exception('JSON decoding error: Syntax error.');
        case JSON_ERROR_UTF8:
            throw new Exception('JSON decoding error: Malformed UTF-8 characters.');
        default:
            throw new Exception('JSON decoding error: Unknown error.');
    }
}

function generatePassword(int $min = 6, int $max = 8, $algorithim = PASSWORD_DEFAULT): string
{
    return password_hash(GenerateKey($min, $max), $algorithim);
}


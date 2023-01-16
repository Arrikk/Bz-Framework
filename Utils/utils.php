<?php
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

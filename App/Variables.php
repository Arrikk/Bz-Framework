<?php

# user agents

use Core\Env;

define('IP_ADDRESS', GetIpAddress());
define('BROWSER', GetBrowser());

define('LOGIN_LIMIT', 3);
define('LIMIT', 3);
define('ASC', 'ASC');
define('DESC', 'DESC');

# account verification status
define('NOT_VERIFIED', '0');
define('VERIFIED', '1');
define('RANDOM_CODE', rand(111111, 999999));
# time
define('CURRENT_TIME', time());
define('CURRENT_DATE', date('Y-m-d H:i:s'));

define('FILE_PATH', 'Public/images');

define('SMTP_HOST', Env::SMTP_HOST());
define('SMTP_PORT', Env::SMTP_PORT());
define('SMTP_SECURE', Env::SMTP_SECURE());
define('SMTP_USERNAME', Env::SMTP_USERNAME());
define('SMTP_PASSWORD', Env::SMTP_PASSWORD());
define('MAIL_FROM', Env::MAIL_FROM());
define('SECRET_KEY', Env::SECRET_KEY());
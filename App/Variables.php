<?php

# user agents
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

define('SMTP_HOST', $_ENV['SMTP_HOST']);
define('SMTP_PORT', $_ENV['SMTP_PORT']);
define('SMTP_SECURE', $_ENV['SMTP_SECURE']);
define('SMTP_USERNAME', $_ENV['SMTP_USERNAME']);
define('SMTP_PASSWORD', $_ENV['SMTP_PASSWORD']);
define('MAIL_FROM', $_ENV['MAIL_FROM']);
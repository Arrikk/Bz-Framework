<?php

# user agents

use Core\Env;

define('IP_ADDRESS', GetIpAddress());
define('BROWSER', GetBrowser());

define('LOGIN_LIMIT', 3);
define('LIMIT', 20);
define('ASC', 'ASC');
define('DESC', 'DESC');

# account verification status
define('NOT_VERIFIED', '0');
define('VERIFIED', '1');
define('DRAFT', 'draft');
define('PUBLISHED', 'published');
define('RANDOM_CODE', rand(111111, 999999));

define('GEN_KEY', GenerateKey());
# time
define('CURRENT_TIME', time());
define('CURRENT_DATE', date('Y-m-d H:i:s'));

define('FILE_PATH', 'Public/images');

define('SMTP_HOST', Env::SMTP_HOST());
define('SMTP_PORT', Env::SMTP_PORT());
define('SMTP_SECURE', ENV::EnvSMTP_SECURE());
define('SMTP_USERNAME', ENV::SMEnvTP_USERNAME());
define('SMTP_PASSWORD', ENV::SMEnvTP_PASSWORD());
define('MAIL_FROM', Env::MAIL_FROM());

define('SECRET_KEY', ENV::SECRET_KEY());

define('YES', 'yes');
define('NO', 'no');

define('ENABLED', 'enabled');
define('DISABLED', 'disabled');
define('PRIVATE_F', 'private');
define('PUBLIC_F', 'public');

define('DELETED', 'deleted');
define('SUSPENDED', 'suspended');
define('BLOCKED', 'blocked');
define('READ', 'read');
define('UNREAD', 'unread');
define('STAR', 'star');
define('UNSTAR', 'unstar');

define('BANNED', '0');
define('TO_BE_REVIEWED', '1');
define('PENDING_SIGNATURE', '2');
define('TO_BE_SEEN_TODAY', '3');
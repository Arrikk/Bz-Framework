<?php

# user agents

use Core\Env;

define('IP_ADDRESS', function_exists('GetIpAddress') ? GetIpAddress() : '');
define('BROWSER', function_exists('GetBrowser') ? GetBrowser() : '');

define('LOGIN_LIMIT', 3);
define('LIMIT', 20);
define('ASC', 'ASC');
define('DESC', 'DESC');

# account verification status
define('NOT_VERIFIED', '0');
define('VERIFIED', '1');
define('DRAFT', 'draft');
define('PUBLISHED', 'published');
define('RANDOM_CODE', function_exists('rand') ? rand(111111, 999999) : '');

define('GEN_KEY', function_exists('GenerateKey') ? GenerateKey() : '');
# time
define('CURRENT_TIME', function_exists('time') ? time() : '');
define('CURRENT_DATE', function_exists('date') ? date('Y-m-d H:i:s') : '');

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

define('TO_BE_SIGNED', '2');
define('SIGNED', '1');
define('TO_BE_SEEN_TODAY', '3');

define('ADMIN', 'admin');
define('USER', 'user');

define('PENDING', 'pending');
define('CONVERTED', 'converted');
define('APPROVED', 'approved');
define('EXPIRED', 'expired');

define('CLOSED', 'closed');
define('OPEN', 'open');
define('AWAITING_REPLY', 'awaiting_reply');
define('IN_PROGRESS', 'in_progress');
define('RESOLVED', 'resolved');

define('LOW', 'low');
define('HIGH', 'high');
define('MEDIUM', 'medium');

define('CANCELLED', 'canceled');
define('ACTIVE', 'active');
<?php

namespace Core\Helpers\Aws;

use AsyncAws\Core\Credentials\Credentials;
use Core\Env;

class AwsConfig
{
    protected static function awsConfig()
    {
        return [
            'accessKeyId' => Env::AWS_ACCESS_KEY(),
            'accessKeySecret' => Env::AWS_SECRET_KEY(),
            'region' => Env::AWS_REGION() // Change to your desired region
        ];
    }

    private static function awsCredentials()
    {
        return new Credentials(Env::AWS_ACCESS_KEY(), Env::AWS_SECRET_KEY());
    }
}

<?php

namespace Amazon\Factory;

use Amazon\Service\Config;


class AmazonConfig
{
    public static function getConfig(): Config
    {

        $amazonConfig = new Config();

        return $amazonConfig;
    }
}


<?php

namespace Amazon\Service;

class Config
{
    
    public function getConfig() {
        $configFilePath = getcwd() . "/config/amazon-config.php";

        include($configFilePath);

        return $amazonConfig;
    }
}
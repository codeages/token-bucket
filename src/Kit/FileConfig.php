<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16-8-3
 * Time: 下午3:46
 */
namespace Codeages\TokenBucket\Kit;

class FileConfig
{
    const Redis = "redis";
    const Db = "database";

    public static function config($key, $default = null)
    {
        $config = self::read();
        if (empty($config[$key])) {
            return $default;
        }
        return $config[$key];
    }

    private static function read()
    {
        $configFile = __DIR__ . "/../../app/config.php";
        if (!file_exists($configFile)) {
            return array();
        }
        $config = include $configFile;
        return $config;
    }
}
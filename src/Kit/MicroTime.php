<?php
/**
 * Created by PhpStorm.
 * User: lihan
 * Date: 17/1/4
 * Time: 10:07
 */
namespace Codeages\TokenBucket\Kit;

class MicroTime
{
    public static function get()
    {
        return intval(microtime(true) * 1000);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16-8-3
 * Time: 下午3:25
 */
namespace Codeages\TokenBucket\Driver;

abstract class Driver
{
    protected $storage = null;

    public abstract function setStorage($storage);

    public abstract function get($key);

    public abstract function update($key, $bucket);
}
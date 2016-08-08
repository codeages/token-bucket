<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16-8-3
 * Time: 下午3:25
 */
namespace Codeages\TokenBucket\Proxy;

abstract class Proxy
{
    protected $storage = null;

    public abstract function setStorage($storage);

    public abstract function get($key);

    public abstract function update($key, $bucket);
}
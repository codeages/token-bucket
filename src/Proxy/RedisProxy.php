<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16-8-3
 * Time: 下午4:56
 */
namespace Codeages\TokenBucket\Proxy;

use Codeages\TokenBucket\Kit\FileConfig;

class RedisProxy extends Proxy
{
    private $config;

    public function __construct($options = array())
    {
        $default = FileConfig::config(FileConfig::Redis, array());
        $this->config = array_merge($default, $options);
    }

    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

    public function redis()
    {
        return $this->storage;
    }

    public function get($key)
    {
        $this->tryToInitStorage();
        if ($this->redis()->exists($key)) {
            return $this->redis()->hGetAll($key);
        }
        return array();
    }

    public function update($key, $bucket)
    {
        $this->tryToInitStorage();
        return $this->redis()->hMset($key, $bucket);
    }

    private function tryToInitStorage()
    {
        if (!$this->redis()) {
            $this->storage = new \Redis();
            $this->storage->connect($this->config['host'], $this->config['port'], $this->config['timeout']);
            $this->storage->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_NONE);
        }
    }
}
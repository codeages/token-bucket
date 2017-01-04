<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16-8-3
 * Time: 下午4:56
 */
namespace Codeages\TokenBucket\Driver;

use bandwidthThrottle\tokenBucket\TokenBucketException;
use malkusch\lock\mutex\PHPRedisMutex;

class RedisDriver extends Driver
{
    private $mux;
    private $key;

    public function __construct($key, $storage = null)
    {
        $this->key = $key;
        $this->storage = $storage;
        $this->setMux();
    }

    public function setStorage($storage)
    {
        $this->storage = $storage;
        $this->setMux();
    }

    public function redis()
    {
        if (empty($this->storage)) {
            throw new TokenBucketException("需先设置redis");
        }
        return $this->storage;
    }

    public function get($key)
    {
        if ($this->redis()->exists($key)) {
            return $this->redis()->hGetAll($key);
        }
        return array();
    }

    public function update($key, $bucket)
    {
        return $this->redis()->hMset($key, $bucket);
    }

    public function getMux()
    {
        return $this->mux;
    }

    private function setMux()
    {
        if (!empty($this->storage)) {
            $this->mux = new PHPRedisMutex(array($this->storage), $this->key);
        }
    }
}
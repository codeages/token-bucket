<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16-8-3
 * Time: 下午3:22
 */
namespace Codeages\TokenBucket\Service;

use bandwidthThrottle\tokenBucket\TokenBucketException;
use Codeages\TokenBucket\Driver\Driver;
use Codeages\TokenBucket\Kit\MicroTime;
use malkusch\lock\exception\MutexException;

class TokenBucket
{
    const TOKEN_BUCKET_KEY = "token_bucket_%s";

    protected $capacity;
    protected $tokens;
    protected $rates;
    protected $executedTime;
    protected $key;
    protected $driver;

    public function __construct($tokens, $rates, $key)
    {
        $this->capacity = $tokens;
        $this->tokens = $tokens;
        $this->rates = $rates;
        $this->key = sprintf(self::TOKEN_BUCKET_KEY, $key);
        $this->executedTime = MicroTime::get();
    }

    public function setDriver(Driver $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    public function watch()
    {
        $bucket = $this->driver->get($this->key);
        $this->setByBucket($bucket);
    }

    public function clear()
    {
        $this->tokens = $this->capacity;
        $this->executedTime = time();
        $this->update();
    }

    public function update()
    {
        $this->driver->update($this->key, $this->getBucket());
    }

    public function show()
    {
        return $this->getBucket();
    }

    public function consume($tokens)
    {
        try {
            return $this->driver->getMux()->synchronized(
                function () use ($tokens){
                    $this->pull();
                    $consumeFlag = false;
                    if ($tokens <= $this->tokens()) {
                        $this->tokens -= $tokens;
                        $consumeFlag = true;
                    }
                    $this->update();
                    return $consumeFlag;
                }
            );
        } catch (MutexException $e) {
            throw new TokenBucketException("could not lock token consumption");
        }
    }

    private function pull()
    {
        $bucket = $this->driver->get($this->key);
        $this->setByBucket($bucket);
    }

    private function getBucket()
    {
        return array(
            'capacity' => $this->capacity,
            'tokens' => $this->tokens,
            'rates' => $this->rates,
            'executedTime' => $this->executedTime,
        );
    }

    private function tokens()
    {
        $now = MicroTime::get();
        if ($this->tokens < $this->capacity) {
            $delta = intval($this->rates * ($now - $this->executedTime) / 1000);
            $this->tokens = min($this->capacity, $this->tokens + $delta);
        }
        $this->executedTime = $now;

        return $this->tokens;
    }

    private function setByBucket($bucket)
    {
        if (empty($bucket)) {
            return;
        }
        $this->capacity = $bucket['capacity'];
        $this->tokens = $bucket['tokens'];
        $this->rates = $bucket['rates'];
        $this->executedTime = $bucket['executedTime'];
    }
}
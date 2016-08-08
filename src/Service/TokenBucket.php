<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16-8-3
 * Time: 下午3:22
 */
namespace Codeages\TokenBucket\Service;

use Codeages\TokenBucket\Proxy\Proxy;

class TokenBucket
{
    protected $capacity;
    protected $tokens;
    protected $rates;
    protected $executedTime;
    protected $key;
    protected $proxy;

    public function __construct($tokens, $rates, $key)
    {
        $this->capacity = $tokens;
        $this->tokens = $tokens;
        $this->rates = $rates;
        $this->key = $key;
        $this->executedTime = time();
    }

    public function setProxy(Proxy $proxy)
    {
        $this->proxy = $proxy;
        return $this;
    }

    public function watch()
    {
        $bucket = $this->proxy->get($this->key);
        $this->_setByBucket($bucket);
    }

    public function clear()
    {
        $this->tokens = $this->capacity;
        $this->executedTime = time();
        $this->update();
    }

    public function update()
    {
        $this->proxy->update($this->key, $this->_getBucket());
    }

    public function show()
    {
        return $this->_getBucket();
    }

    public function consume($tokens)
    {
        $consumeFlag = false;
        if ($tokens <= $this->_tokens()) {
            $this->tokens -= $tokens;
            $consumeFlag = true;
        }
        $this->update();
        return $consumeFlag;
    }

    private function _getBucket()
    {
        return array(
            'capacity' => $this->capacity,
            'tokens' => $this->tokens,
            'rates' => $this->rates,
            'executedTime' => $this->executedTime,
        );
    }

    private function _tokens()
    {
        $now = time();
        if ($this->tokens < $this->capacity) {
            $delta = $this->rates * ($now - $this->executedTime);
            $this->tokens = min($this->capacity, $this->tokens + $delta);
        }
        $this->executedTime = $now;

        return $this->tokens;
    }

    private function _setByBucket($bucket)
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
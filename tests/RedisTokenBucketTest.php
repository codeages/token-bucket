<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16-8-3
 * Time: 下午5:04
 */
namespace Codeages\TokenBucket\Test;

use Codeages\TokenBucket\Proxy\RedisProxy;
use Codeages\TokenBucket\Service\TokenBucket;

class RedisTokenBucketTest extends \PHPUnit_Framework_TestCase
{
    private $tokenBucket;
    private $key = "test";
    private $tokens = 30;
    private $rates = 10;
    private $consumeRate = 30;
    private $proxy;

    public function setUp()
    {
        $this->proxy = new RedisProxy(array(
            'host' => '127.0.0.1',
            'port' => 6379,
            'timeout' => 1,
        ));
        $this->tokenBucket = new TokenBucket($this->tokens, $this->rates, $this->key);
        $this->tokenBucket->setProxy($this->proxy)->watch();
    }

    public function testTokenBucket()
    {
        $this->assertTrue($this->tokenBucket->consume($this->consumeRate), "it should be consume");
        $i = 3;
        while ($i > 0) {
            sleep(1);
            if ($i == 1) {
                $this->assertTrue($this->tokenBucket->consume($this->consumeRate), "it should be consume after sleep 3 seconds");
            } else {
                $this->assertFalse($this->tokenBucket->consume($this->consumeRate), "unexpected consume");
            }
            $i--;
        }
        $this->tokenBucket->clear();
        $this->assertTrue($this->tokenBucket->consume($this->consumeRate), "it should be consume after clear");
    }
}
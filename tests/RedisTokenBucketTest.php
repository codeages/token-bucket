<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16-8-3
 * Time: 下午5:04
 */
namespace Codeages\TokenBucket\Test;

use Codeages\TokenBucket\Driver\RedisDriver;
use Codeages\TokenBucket\Service\TokenBucket;
use FSth\Redis\Client;
use FSth\Redis\Proxy;

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
        $client = new Client('127.0.0.1', 6379, 1);
        $proxy = new Proxy($client);
        $client->connect();
        $this->proxy = new RedisDriver($this->key, $proxy);
        $this->tokenBucket = new TokenBucket($this->tokens, $this->rates, $this->key);
        $this->tokenBucket->setDriver($this->proxy)->watch();
    }

    public function testTokenBucket()
    {
        $this->assertTrue($this->tokenBucket->consume($this->consumeRate), "it should be consume enable");
        $i = 3;
        while ($i > 0) {
            sleep(1);
            if ($i == 1) {
                $this->assertTrue($this->tokenBucket->consume($this->consumeRate), "it should be consume enable after sleep 3 seconds");
            } else {
                $this->assertFalse($this->tokenBucket->consume($this->consumeRate), "unexpected consume");
            }
            $i--;
        }
        $this->tokenBucket->clear();
        $this->assertTrue($this->tokenBucket->consume($this->consumeRate), "it should be consume enable after clear");
    }
}
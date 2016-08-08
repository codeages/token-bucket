<?php
/**
 * Created by PhpStorm.
 * User: li
 * Date: 16-8-3
 * Time: 下午4:34
 */
namespace Codeages\TokenBucket\Test;

use Codeages\TokenBucket\Proxy\DbProxy;
use Codeages\TokenBucket\Service\TokenBucket;

class DbTokenBucketTest extends \PHPUnit_Framework_TestCase
{
    private $tokenBucket;
    private $key = "test";
    private $tokens = 30;
    private $rates = 10;
    private $consumeRate = 30;
    private $proxy;

    public function setUp()
    {
        $this->proxy = new DbProxy(array(
            'driver' => 'pdo_mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'name' => 'bucket',
            'user' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
        ));
        $this->proxy->setTable('token');
        $this->tokenBucket = new TokenBucket($this->tokens, $this->rates, $this->key);
        $this->tokenBucket->setProxy($this->proxy)->watch();
    }

    public function testTokenBucket()
    {
        $consume = $this->tokenBucket->consume($this->consumeRate);
        $this->assertTrue($consume, "it should be consume");
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
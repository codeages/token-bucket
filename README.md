## Installation

  * use composer 
    ```
    composer require codeages/token-bucket
    ```

## notice
    
  * add redis mux key support for concurrency
  * remove the db support 

## Usage

  * example
    ```
    $tokens = 30; //the bucket capacity
    $rates = 10; //recover tokens per second
    $consume = 10; //consume tokens
    $key = "test"; //bucket name
    
    $redis = new \Redis();
    $redis->connect('127.0.0.1', 6379, 1);
    $driver = new RedisDriver("test", $redis);
    $tokenBucket = new TokenBucket($tokens, $rates, $name);
    $tokenBucket->setDriver($driver)->watch();
    
    $tokenBucket->consume($consume);
    ```    
        
         
         
    
  

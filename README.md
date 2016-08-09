## installation

  * use composer 

        composer require codeages/token-bucket

## usage

  * example
        
        $proxy = new RedisProxy(array( 
            'host' => '127.0.0.1',
            'port' => 6379,
            'timeout' => 1,
        ));
         
        or
                
        $proxy = new DbProxy(array(
            'driver' => 'pdo_mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'name' => 'bucket',
            'user' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
        ));
        $this->proxy->setTable('token');
        
        or
        
        $proxy = new RedisProxy();
        $proxy->setStorage($redis); //$redis instance of \Redis
        
        or 
        
        $proxy = new DbProxy();
        $proxy->setStorage($db);
        $proxy->setTable('token');
        
        $tokens = 30; //remain tokens
        $rates = 10; //the rate of token resume
        $tokenName = "test";
        $tokenBucket = new TokenBucket($tokens, $rates, $tokenName);
        $tokenBucket->setProxy($proxy)->watch();
        
        $consumeTokens = 30;
        $tokenBucket->consume($consumeTokens);
        
        
         
         
    
  

## Installation

  * use composer 

        composer require codeages/token-bucket

## Usage

  * example
        
        $driver = new RedisDriver(array( 
            'host' => '127.0.0.1',
            'port' => 6379,
            'timeout' => 1,
        ));
        
        /*
        $driver = new DbDriver(array(
            'driver' => 'pdo_mysql',
            'host' => '127.0.0.1',
            'port' => 3306,
            'name' => 'bucket',
            'user' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
        ));
        $this->driver->setTable('token');
        */
        
        /*
        $driver = new RedisDriver();
        $driver->setStorage($redis); //$redis instance of \Redis
        */
        
        /*
        $driver = new DbDriver();
        $driver->setStorage($db);
        $driver->setTable('token');
        */
        
        $tokens = 30; //remain tokens
        $rates = 10; //the rate of token resume
        $tokenName = "test";
        $tokenBucket = new TokenBucket($tokens, $rates, $tokenName);
        $tokenBucket->setDriver($driver)->watch();
        
        $consumeTokens = 30;
        $tokenBucket->consume($consumeTokens);
        
        
         
         
    
  

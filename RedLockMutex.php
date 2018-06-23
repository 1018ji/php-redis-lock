<?php

abstract class RedLockMutex extends SpinlockMutex
{
    private $token;

    private $redisAPIs;

    public function __construct(array $redisAPIs, $name, $timeout = 3)
    {
        parent::__construct($name, $timeout);

        $this->redisAPIs = $redisAPIs;
    }

    protected function acquire($key, $expire)
    {
        // 使用时间戳不使用毫秒数， 防止 32 位机器溢出
        $time = microtime(true);

        $acquired = 0;
        $errored  = 0;
        # random_int 在 PHP 7 可用。如为 PHP 5 请使用 paragonie/random_compat
        $this->token = random_int(0, 2147483647);

        // 循环 redis 获取锁，并进行计数，根据计数确定是否获取锁成功
        // 若果一个 redis 掉线，另外一个需要获取锁，恰好跟上一个锁随机数一致，切上一个 redis 中 key 未失效，是不是能尴尬？
        $exception   = null;
        foreach ($this->redisAPIs as $redis) {
            try {
                if ($this->add($redis, $key, $this->token, $expire)) {
                    $acquired++;
                }
            } catch (LockAcquireException $exception) {
                // TODO LOG
                $errored++;
            }
        }

        $elapsedTime = microtime(true) - $time;
        $isAcquired  = $this->isMajority($acquired) && $elapsedTime <= $expire;
        
        if ($isAcquired) {
            return true;
        } else {
            // 释放锁
            $this->release($key);
            
            if (!$this->isMajority(count($this->redisAPIs) - $errored)) {
                throw new LockAcquireException(
                    "It's not possible to acquire a lock because at least half of the Redis server are not available.",
                    LockAcquireException::REDIS_NOT_ENOUGH_SERVERS,
                    $exception
                );
            }

            return false;
        }
    }

    protected function release($key)
    {
        $script = '
            if redis.call("get",KEYS[1]) == ARGV[1] then
                return redis.call("del", KEYS[1])
            else
                return 0
            end
        ';

        $released = 0;
        foreach ($this->redisAPIs as $redis) {
            try {
                if ($this->evalScript($redis, $script, 1, [$key, $this->token])) {
                    $released++;
                }
            } catch (LockReleaseException $exception) {
                // TODO LOG
            }
        }
        return $this->isMajority($released);
    }

    // 奇数个 redis，大于一半成功
    private function isMajority($count)
    {
        return $count > count($this->redisAPIs) / 2;
    }

    abstract protected function add($redisAPI, $key, $value, $expire);

    abstract protected function evalScript($redisAPI, $script, $numkeys, array $arguments);

    abstract protected function getRedisIdentifier($redisAPI);
}

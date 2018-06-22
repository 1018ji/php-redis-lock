<?php

abstract class SingleMutex extends SpinlockMutex
{
    // 锁随机值
    private $token;

    // redis 实例
    private $redisAPI;

    public function __construct(array $redisAPI, $name, $timeout = 3)
    {
        parent::__construct($name, $timeout);

        $this->redisAPI = $redisAPI;
    }

    // 这里不能随便排除异常，否则 loop 会停止
    protected function acquire($key, $expire)
    {
        // 使用时间戳不使用毫秒数， 防止 32 位机器溢出
        $time = microtime(true);

        # random_int 在 PHP 7 可用。如为 PHP 5 请使用 paragonie/random_compat
        $this->token = random_int(0, 2147483647);

        $exception = null;
        try {
            $acquired = $this->add($redisAPI, $key, $this->token, $expire) ? true : false;
        } catch (LockAcquireException $exception) {
            $acquired = false;
        }

        // 判断写入状态 以及 是否超过设置的过期时间（超过过期时间直接异常，无需重试）
        $elapsedTime = microtime(true) - $time;
        $isAcquired = $acquired && $elapsedTime <= $expire;
        if (!$isAcquired) {
            throw new LockAcquireException(
                "It's not possible to acquire a lock.",
                0, $exception
            );
        }

        return $acquired;
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

        // 如果删除锁失败，直接异常
        return $this->evalScript($redis, $script, 1, [$key, $this->token]);
    }

    abstract protected function add($redisAPI, $key, $value, $expire);

    abstract protected function evalScript($redisAPI, $script, $numkeys, array $arguments);
}

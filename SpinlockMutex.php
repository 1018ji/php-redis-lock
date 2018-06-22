<?php

abstract class SpinlockMutex extends LockMutex
{
    // 超时时间
    private $timeout;

    // 循环部分
    private $loop;

    // redis key
    private $key;
    
    // 记录真正开始获取锁的时间戳，用于超时爆出异常
    private $acquired;

    // redis key 前缀
    const PREFIX = 'lock_';

    public function __construct($name, $timeout = 3)
    {
        $this->timeout = $timeout;
        $this->loop    = new Loop($this->timeout);
        $this->key     = static::PREFIX . $name;
    }
    
    protected function lock()
    {
        $this->loop->execute(function () {
            $this->acquired = microtime(true);

            // 上锁成功，只要单次直接使用 acquire 判断爆出异常即可
            if ($this->acquire($this->key, $this->timeout + 1)) {
                $this->loop->end(); // 使用 end 放弃 loop
            }
        });
    }

    protected function unlock()
    {
        $elapsed = microtime(true) - $this->acquired;

        // 执行完成也会因为超时执行失败
        if ($elapsed >= $this->timeout) {
            $message = sprintf(
                "The code executed for %.2F seconds. But the timeout is %d " .
                "seconds. The last %.2F seconds were executed outside the lock.",
                $elapsed,
                $this->timeout,
                $elapsed - $this->timeout
            );
            throw new ExecutionOutsideLockException($message);
        }

        // 释放锁，执行删除操作
        if (!$this->release($this->key)) {
            throw new LockReleaseException("Failed to release the lock.");
        }
    }

    // 获取锁
    abstract protected function acquire($key, $expire);

    // 释放锁
    abstract protected function release($key);
}

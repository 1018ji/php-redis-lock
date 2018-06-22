<?php

abstract class LockMutex extends Mutex
{
    // 加锁
    abstract protected function lock();

    // 解锁
    abstract protected function unlock();

    // 实际执行
    public function synchronized(callable $code)
    {
        $this->lock();

        try {
            return call_user_func($code);
        } finally {
            $this->unlock();
        }
    }

}

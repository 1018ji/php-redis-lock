<?php

class Loop
{
    // 超时时间
    private $timeout;

    // 是否循环
    private $looping = false;
    
    // 构造函数
    public function __construct($timeout = 3)
    {
        if ($timeout <= 0) {
            throw new LengthException("The timeout must be greater than 0. '{$timeout}' was given");
        }

        $this->timeout = $timeout;
    }

    // 结束 loop 信号
    public function end()
    {
        $this->looping = false;
    }

    # 循环执行，但是需要调用 end 函数结束
    public function execute(callable $code)
    {
        $this->looping = true;
        $minWait = 100; // 毫秒数
        $timeout = microtime(true) + $this->timeout; // 计算最终的超时时间
        $result = null; // 返回默认值

        for ($i = 0; $this->looping && microtime(true) < $timeout; $i++) {
            $result = call_user_func($code); // 执行一次
            if (!$this->looping) { // 成功执行，则跳出，否则继续执行
                break;
            }

            // 算一个延迟区间
            $min    = (int) $minWait * 1.5 ** $i;
            $max    = $min * 2;

            // 如果相差小于 1e6 则为 0，计算的时候 microtime(true) 为浮点数计算
            $usecRemaining = intval(($timeout - microtime(true))  * 1e6);
            if ($usecRemaining <= 0) {
                throw new TimeoutException("Timeout of {$this->timeout} seconds exceeded.");
            }

            // 随机延迟 延迟区间 以及 $usecRemaining 较小数
            # random_int 在 PHP 7 可用。如为 PHP 5 请使用 paragonie/random_compat
            $usleep = min($usecRemaining, random_int($min, $max));

            usleep($usleep);
        }

        if (microtime(true) >= $timeout) {
            throw new TimeoutException("Timeout of {$this->timeout} seconds exceeded.");
        }

        return $result;
    }
}

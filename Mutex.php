<?php

abstract class Mutex
{
    abstract public function synchronized(callable $code);

}

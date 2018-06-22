<?php

class SingleInstance extends SingleMutex
{
    public function __construct(array $clients, $name, $timeout = 3)
    {
        parent::__construct($clients, $name, $timeout);
    }

    protected function add($client, $key, $value, $expire)
    {
        try {
            return $client->set($key, $value, "EX", $expire, "NX");
        } catch (PredisException $exception) {
            throw new LockAcquireException("Failed to acquire lock for key {$key}", 0, $exception);
        }
    }

    protected function evalScript($client, $script, $numkeys, array $arguments)
    {
        try {
            return $client->eval(...array_merge([$script, $numkeys], $arguments));
        } catch (PredisException $exception) {
            throw new LockReleaseException("Failed to release lock", 0, $exception);
        }
    }
}
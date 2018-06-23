<?php

class SingleClient extends SingleMutex
{
    public function __construct($clients, $name, $timeout = 3)
    {
        parent::__construct($clients, $name, $timeout);
    }

    protected function add($client, $key, $value, $expire)
    {
        try {
            return $client->set($key, $value, ['nx', 'ex' => $expire]);
        } catch (PredisException $exception) {
            throw new LockAcquireException("Failed to acquire lock for key {$key}", 0, $exception);
        }
    }

    protected function evalScript($client, $script, $numkeys, array $arguments)
    {
        try {
            return $client->eval($script, $arguments, $numkeys);
        } catch (PredisException $exception) {
            throw new LockReleaseException("Failed to release lock", 0, $exception);
        }
    }
}
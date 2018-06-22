<?php

class RedLockClient extends RedLockMutex
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
            $message = sprintf(
                "Failed to acquire lock for key '%s' at %s",
                $key,
                $this->getRedisIdentifier($client)
            );
            throw new LockAcquireException($message, 0, $exception);
        }
    }

    protected function evalScript($client, $script, $numkeys, array $arguments)
    {
        try {
            return $client->eval(...array_merge([$script, $numkeys], $arguments));
        } catch (PredisException $exception) {
            $message = sprintf(
                "Failed to release lock at %s",
                $this->getRedisIdentifier($client)
            );
            throw new LockReleaseException($message, 0, $exception);
        }
    }

    protected function getRedisIdentifier($client)
    {
        return (string) $client->getConnection();
    }
}

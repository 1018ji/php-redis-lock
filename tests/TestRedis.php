<?php

require_once('../LockInit.php');

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$redisKey = 'redis_key_test';
$redisValue = time();
$setStatus = $redis->set($redisKey, $redisValue);

if (!$setStatus) {
    throw new Exception("Redis set key {$redisKey} error", -1);
}

echo "{$redisKey}: {$redisValue}" . PHP_EOL;

$delStatus = $redis->del($redisKey);

if (!$delStatus) {
    throw new Exception("Redis del key {$redisKey} error", -1);
}
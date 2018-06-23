<?php

require_once('../LockInit.php');

$redisOne = new Redis();
$redisOne->connect('127.0.0.1', 6379);

$redisTwo = new Redis();
$redisTwo->connect('10.188.40.15', 6379);

$redisAPIs = [
    $redisOne,
    $redisTwo,
];

$instance = new RedLockClient($redisAPIs, 'redlock_client');
$instance->synchronized(function() {
    echo 'work start' . PHP_EOL;

    sleep(1);
    //sleep(10);

    echo 'work done' . PHP_EOL;
});
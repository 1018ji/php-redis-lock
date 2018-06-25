<?php

require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR .'LockInit.php');

$redisAPIs = [
    (new Redis)->connect('127.0.0.1', 6379),
    (new Redis)->connect('127.0.0.1', 6380),
    (new Redis)->connect('127.0.0.1', 6381),
    (new Redis)->connect('127.0.0.1', 6382),
    (new Redis)->connect('127.0.0.1', 6383),
];

$instance = new RedLockClient($redisAPIs, 'redlock_client');
$instance->synchronized(function() {
    echo 'work start' . PHP_EOL;

    sleep(1);
    //sleep(10);

    echo 'work done' . PHP_EOL;
});
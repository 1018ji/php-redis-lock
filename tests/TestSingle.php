<?php

require_once(dirname(__DIR__) . DIRECTORY_SEPARATOR .'LockInit.php');

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$instance = new SingleClient($redis, 'single_client');
$instance->synchronized(function() {
    echo 'work start' . PHP_EOL;

    sleep(1);
    // sleep(10);

    echo 'work done' . PHP_EOL;
});
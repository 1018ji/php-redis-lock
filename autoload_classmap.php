<?php

$basePath = __DIR__;

return [
    'ExecutionOutsideLockException' => $basePath . 'exception' . DIRECTORY_SEPARATOR . 'ExecutionOutsideLockException.php',
    'LockAcquireException' => $basePath . 'exception' . DIRECTORY_SEPARATOR . 'LockAcquireException.php',
    'LockReleaseException' => $basePath . 'exception' . DIRECTORY_SEPARATOR . 'LockReleaseException.php',
    'MutexException' => $basePath . 'exception' . DIRECTORY_SEPARATOR . 'MutexException.php',
    'TimeoutException' => $basePath . 'exception' . DIRECTORY_SEPARATOR . 'TimeoutException.php',

    'LockMutex' => $basePath . 'LockMutex.php',
    'Mutex' => $basePath . 'Mutex.php',
    'RedLockClient' => $basePath . 'RedLockClient.php',
    'RedLockMutex' => $basePath . 'RedLockMutex.php',
    'SingleClient' => $basePath . 'SingleClient.php',
    'SingleMutex' => $basePath . 'SingleMutex.php',
    'SpinlockMutex' => $basePath . 'SpinlockMutex.php',

    'Loop' => $basePath . 'util' . DIRECTORY_SEPARATOR . 'Loop.php',
];

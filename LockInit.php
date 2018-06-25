<?php

$classMap = require __DIR__ . DIRECTORY_SEPARATOR . 'autoload_classmap.php';

spl_autoload_register(function($class) use ($classMap) {
    if (isset($classMap[$class]) && file_exists($path = $classMap[$class])) {
        require_once($path);
    }
});

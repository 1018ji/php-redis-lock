<?php
function autoLoad($class) {
    $basePath = __DIR__ . DIRECTORY_SEPARATOR;
    $fileName = $class . '.php';

    $path = $basePath . $fileName;
    if (file_exists($path)) {
        require($path);
        return;
    }

    $path = $basePath . 'exception' . DIRECTORY_SEPARATOR . $fileName;
    if (file_exists($path)) {
        require($path);
        return;
    }

    $path = $basePath . 'util' . DIRECTORY_SEPARATOR . $fileName;
    if (file_exists($path)) {
        require($path);
        return;
    }
}

spl_autoload_register('autoLoad');

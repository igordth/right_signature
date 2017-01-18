<?php
/**
 * Simple autoload
 */

spl_autoload_register(function($class) {
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = $_SERVER['DOCUMENT_ROOT'] . (empty($file) ? '' : DIRECTORY_SEPARATOR) . $file . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
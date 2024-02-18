<?php
require_once("config.php");

// Class autoloader (PSR-4).
spl_autoload_register(function ($className) {
    $baseDir = RESOURCES_PATH;
    $className = str_replace("\\", "/", $className);
    $file = "{$baseDir}/{$className}.php";

    if (file_exists($file)) {
        require_once($file);
    }
});
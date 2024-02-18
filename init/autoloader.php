<?php

// PSR-4 class autoloader.
spl_autoload_register(function ($className) {
    // Replace namespace separators with directory separators
    // in the relative class name.
    $className = str_replace('\\', '/', $className);
    $file = PROJECT_DIR . "/{$className}.php";

    // Include file if exists.
    if (file_exists($file)) {
        require_once($file);
    } else {
        echo "Failed to include class: {$className}";
        die;
    }
});

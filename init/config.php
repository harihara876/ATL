<?php

// Select config file based on the server name configured in Apache HTTP server.
// Loads local config if domain name does not match.

switch ($_SERVER["SERVER_NAME"]) {
    case "dev.plat4minc.com": // Development environment.
        require_once("config.dev.php");
        break;
    case "mystore.plat4minc.com": // Production environment.
        require_once("config.prod.php");
        break;
    default: // Local environment.
        require_once("config.local.php");
}

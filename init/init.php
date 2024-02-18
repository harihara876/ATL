<?php
// Initilization file.
// Includes all required components.

// Report all errors.
ini_set("display_errors", 1);
error_reporting(E_ALL);

// Include necessary files and libraries.
require_once(__DIR__ . "/config.php");
require_once(__DIR__ . "/constants.php");
require_once(__DIR__ . "/error.php");
require_once(PROJECT_DIR . "/dep/vendor/autoload.php"); // Always on top of custom autoloader.
require_once(__DIR__ . "/autoloader.php");
require_once(PROJECT_DIR . "/dist/vendor/autoload.php"); // Always on top of custom autoloader.


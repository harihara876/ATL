<?php

// This file contains required handlers to handle errors and exceptions.
// Writes error logs to a directory.

// Set error and exception handlers.
set_error_handler("errorHandler");
set_exception_handler("exceptionHandler");

/**
 * Handles errors.
 * Converts general errors to ErrorException.
 * @param mixed $level Error level.
 * @param mixed $message Error message.
 * @param mixed $file File in which error ocurred.
 * @param mixed $line Line number in which error occurred.
 * @throws ErrorException
 */
function errorHandler($level, $message, $file, $line)
{
    if (error_reporting() !== 0) {  // To keep the @ operator working.
        throw new \ErrorException($message, 0, $level, $file, $line);
    }
}

/**
 * Handles exceptions.
 * Writes exceptions to a file.
 * @param Exception $exception Exception.
 */
function exceptionHandler($exception)
{
    // Set log file.
    $log = ERROR_LOGS_DIR . "/" . date("ymd") . ".log";
    ini_set('error_log', $log);

    // Build error message.
    $exClass = get_class($exception);
    $exMessage = $exception->getMessage();
    $exStackTrace = $exception->getTraceAsString();
    $exFile = $exception->getFile();
    $exLine = $exception->getLine();

    $message = "Uncaught exception: '{$exClass}'\n";
    $message .= "Message: {$exMessage}\n";
    $message .= "Stack Trace: {$exStackTrace}\n";
    $message .= "Thrown in {$exFile} on line {$exLine}\n\n";

    // Write error message.
    error_log($message);
}

<?php

namespace Plat4mAPI\Util;

class Logger
{
    // Log type for information.
    const INFO = "INFO";

    // Log type for debugging.
    const DEBUG = "DEBUG";

    // Log type for warnings.
    const WARN = "WARN";

    // Log type for errors.
    const ERROR = "ERROR";

    // Default path for log files.
    const DEFAULT_LOGS_DIR = LOGS_DIR;

    // File name with full path.
    // E.g. /var/log/plat4m/logs/210318.log
    private static $file = NULL;

    // Flag to serialize array while writing to log file.
    private static $serializeArray = FALSE;

    // Unique ID to group multiple log messages.
    private static $tag = NULL;

    // Request URI.
    private static $requestURI = NULL;

    /**
     * Sets file name.
     * @param string $file File name with full path.
     * @return object Self.
     */
    public static function setFile($file)
    {
        // TODO: Add validation.
        self::$file = $file;
        return new self();
    }

    /**
     * Get log file.
     * @return string File name with full path.
     */
    private static function getFile()
    {
        if (empty(self::$file)) {
            // E.g. /var/log/plat4m/logs/210318.log
            self::$file = self::DEFAULT_LOGS_DIR . "/" . date("ymd") . ".log";
        }

        return self::$file;
    }

    /**
     * Sets serializeArray flag.
     * @return object Self.
     */
    public static function serializeMode()
    {
        self::$serializeArray = TRUE;
        return new self();
    }

    /**
     * Returns a tag (Unique ID).
     * This function tries to create unique identifier,
     * but it does not guarantee 100% uniqueness of return value.
     * @return mixed Tag.
     */
    private static function getTag()
    {
        if (empty(self::$tag)) {
            self::$tag = uniqid();
        }

        return self::$tag;
    }

    /**
     * Returns request URI.
     * @return string Request URI.
     */
    private static function getRequestURI()
    {
        if (empty(self::$requestURI)) {
            self::$requestURI = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : "";
        }

        return self::$requestURI;
    }

    /**
     * Writes log messages to file.
     * @param string
     */
    private static function write($type, $message, $isHTTPMsg = FALSE)
    {
        $currentTime = date("Y-m-d H:i:s");
        $tag = self::getTag();

        // If message is an array, serialize or json_encode it.
        if (is_array($message)) {
            if (self::$serializeArray) {
                $message = serialize($message);
            } else {
                $message = json_encode($message);
            }
        }

        if ($isHTTPMsg) {
            $remoteAddress = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "NA";
            $requestMethod = isset($_SERVER["REQUEST_METHOD"]) ? $_SERVER["REQUEST_METHOD"] : "NA";
            $requestURI = self::getRequestURI();

            // Format: TIME MESSAGE_TYPE MESSAGE_TAG REMOTE_ADDR REQ_METHOD REQ_URL MESSAGE_BODY
            $formattedMessage = "{$currentTime} [$type] {$tag} {$remoteAddress} {$requestMethod} {$requestURI} {$message}";
        } else {
            // Format: TIME MESSAGE_TYPE MESSAGE_TAG MESSAGE_BODY
            $formattedMessage = "{$currentTime} [$type] {$tag} {$message}";
        }

        $formattedMessage .= PHP_EOL;
        file_put_contents(self::getFile(), $formattedMessage, FILE_APPEND | LOCK_EX);
    }

    /**
     * Writes HTTP request related message.
     * @param mixed $message Message.
     * @return
     */
    public static function httpMsg($message = "")
    {
        self::write(self::INFO, $message, TRUE);
    }

    /**
     * Writes info messages.
     * @param mixed $message Message.
     * @return
     */
    public static function infoMsg($message)
    {
        self::write(self::INFO, $message);
    }

    /**
     * Writes debug messages.
     * @param mixed $message Message.
     * @return
     */
    public static function debugMsg($message)
    {
        self::write(self::DEBUG, $message);
    }

    /**
     * Writes warning messages.
     * @param mixed $message Message.
     * @return
     */
    public static function warnMsg($message)
    {
        self::write(self::WARN, $message);
    }

    /**
     * Writes error messages.
     * @param mixed $message Message.
     * @return
     */
    public static function errorMsg($message)
    {
        self::write(self::ERROR, $message);
    }

    /**
     * Writes error exception messages.
     * @param Exception $ex Exception object.
     * @return
     */
    public static function errExcept($ex)
    {
        if (in_array($ex->getCode(), [401])) {
            self::errorMsg("Code: " . $ex->getCode() . " Message: " . $ex->getMessage());
            return;
        }

        $message = "";

        // Build error message.
        $exClass        = get_class($ex);
        $exCode         = $ex->getCode();
        $exMessage      = $ex->getMessage();
        $exStackTrace   = $ex->getTraceAsString();
        $exFile         = $ex->getFile();
        $exLine         = $ex->getLine();

        $message .= "Uncaught exception: '{$exClass}'\n";
        $message .= "Code: {$exCode}\n";
        $message .= "Message: {$exMessage}\n";
        $message .= "Stack Trace: {$exStackTrace}\n";
        $message .= "Thrown in {$exFile} on line {$exLine}";

        self::write(self::ERROR, $message);
    }
}

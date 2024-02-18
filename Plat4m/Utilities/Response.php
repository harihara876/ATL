<?php

namespace Plat4m\Utilities;

class Response
{
    // HTTP status code.
    private static $statusCode = 200;

    // Headers.
    private static $headers = [];

    // Payload.
    private static $body = [];

    /**
     * Sets HTTP status code.
     * @param $statusCode HTTP status code.
     * @return object Self.
     */
    public static function statusCode($statusCode)
    {
        self::$statusCode = (int) $statusCode;
        return new static();
    }

    /**
     * Sets HTTP payload.
     * @param $bodt HTTP payload.
     * @return object Self.
     */
    public static function body($body)
    {
        self::$body = $body;
        return new static();
    }

    /**
     * Writes JSON to HTTP response.
     * @param int $flags JSON encode flags.
     */
    public static function json($flags = 0)
    {
        http_response_code(self::$statusCode);
        header("Content-Type: application/json");
        echo json_encode(self::$body, $flags);
        die;
    }

    /**
     * Sends empty body.
     */
    public static function sendEmpty()
    {
        self::$statusCode = empty(self::$statusCode) ? 200 : self::$statusCode;
        http_response_code(self::$statusCode);

        foreach (self::$headers as $header) {
            header($header);
        }

        die;
    }
}

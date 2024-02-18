<?php

namespace Plat4m\Utilities;

class Request
{
    /**
     * Parses request payload.
     * @return array
     */
    public static function payload()
    {
        $payload = file_get_contents("php://input");
        return json_decode($payload, TRUE);
    }

    /**
     * Return value associated to a key from $_POST.
     * @param string $key Key.
     * @param mixed $defaultValue Default value.
     * @return mixed Value associated to key.
     */
    public static function postVal($key, $defaultValue = NULL)
    {
        if (!isset($_POST[$key])) {
            return $defaultValue;
        }

        return $_POST[$key];
    }

    /**
     * Return value associated to a key from $_GET.
     * @param string $key Key.
     * @param mixed $defaultValue Default value.
     * @return mixed Value associated to key.
     */
    public static function getVal($key, $defaultValue = NULL)
    {
        if (!isset($_GET[$key])) {
            return $defaultValue;
        }

        return $_GET[$key];
    }

    /**
     * Returns bearer token from Authorization header.
     * @return string Token.
     */
    public static function getAuthBearerToken()
    {
        if (empty($_SERVER["HTTP_AUTHORIZATION"])) {
            return NULL;
        }

        list($type, $data) = explode(" ", $_SERVER["HTTP_AUTHORIZATION"], 2);

        if (strcasecmp($type, "Bearer") == 0) {
            return $data;
        }

        return NULL;
    }
}

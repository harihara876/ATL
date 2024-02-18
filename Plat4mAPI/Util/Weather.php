<?php

namespace Plat4mAPI\Util;

class Weather
{
    /**
     * Builds Weather API URL.
     * @param string $apiKey API key.
     * @param string $lat Latitude.
     * @param string $lon Longitude.
     * @return string URL.
     */
    private static function buildURL($lat, $lon)
    {
        $params = WEATHER_API_PARAMS;
        $params["lat"] = $lat;
        $params["lon"] = $lon;
        $queryString = http_build_query($params);
        return WEATHER_API_URL . "?" . $queryString;
    }

    /**
     * Fetches weather update.
     * @param string $lat Latitude.
     * @param string $lon Longitude.
     * @return string Response.
     */
    public static function getUpdate($lat, $lon)
    {
        $url = self::buildURL($lat, $lon);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $data = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (200 == $statusCode) {
            return $data
        }

        return NULL;
    }
}

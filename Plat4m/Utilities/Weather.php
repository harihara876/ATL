<?php

namespace Plat4m\Utilities;

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
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}

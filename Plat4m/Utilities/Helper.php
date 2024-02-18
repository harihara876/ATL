<?php

namespace Plat4m\Utilities;

use DateTime;
use DateTimeZone;
use Exception;
use Plat4m\Utilities\Logger;

class Helper
{
    /**
     * Returns value if set or default value from an array.
     * @param array $arr Array.
     * @param string $key Key.
     * @param mixed Any value.
     * @return mixed
     */
    public static function arrVal(&$arr, $key, $defaultValue = NULL)
    {
        if (!isset($arr[$key])) {
            Logger::infoMsg("Empty '{$key}'. Setting to default: {$defaultValue}");
            return $defaultValue;
        }

        return $arr[$key];
    }

    /**
     * Converts datetime from one format to other.
     * @param string $datetime Datetime.
     * @param string $fromFormat Source format.
     * @param string $toFormat Destination format.
     * @return string Converted datetime.
     */
    public static function convertDateTimeFormat($datetime, $fromFormat, $toFormat)
    {
        $dt = DateTime::createFromFormat($fromFormat, $datetime);

        return $dt->format($toFormat);
    }

    /**
     * Modify timestamp. Add or subtract seconds.
     * @param string $datetime Datetime in YYYY-MM-DD HH:MM:SS
     * @param int $seconds Seconds to add or subtract.
     * @return string Modified timestamp.
     */
    public static function modifyDateTime($datetime, $seconds)
    {
        $dt = new DateTime($datetime);
        $dt->modify("{$seconds} second");

        return $dt->format("Y-m-d H:i:s");
    }

    /**
     * Converts datetime to a timezone.
     * @param string $datetime Datetime in format YYYY-MM-DD HH:MM:SS (Y-m-d H:i:s).
     * @param string $fromTimezone From Timezone. E.g. UTC, Asia/Calcutta, America/Los_Angeles etc.
     * @param string $toTimezone To Timezone. E.g. UTC, Asia/Calcutta, America/Los_Angeles etc.
     * @return string Converted datetime.
     * @throws Exception
     */
    public static function convertTimezone($datetime, $fromTimezone, $toTimezone)
    {
        try {
            $dt = new DateTime($datetime, new DateTimeZone($fromTimezone));
            $dt->setTimeZone(new DateTimeZone($toTimezone));

            return $dt->format("Y-m-d H:i:s");
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * Format price.
     * @param $price Price. E.g. 5.232, 0, 0.129 etc.
     * @return string Formatted price. E.g. 5.23, 0.00, 0.12 etc.
     */
    public static function formatPrice($price)
    {
        $roundedPrice = round($price, 2);

        // return sprintf("%.2f", $roundedPrice);
        return number_format($roundedPrice, 2);
    }
}

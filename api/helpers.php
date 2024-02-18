<?php

/**
 * Returns JSON decoded request payload.
 * @return array Payload.
 */
function payload()
{
    $payload = json_decode(file_get_contents("php://input"), TRUE);

    return ($payload == NULL) ? [] : $payload;
}

/**
 * Return value associated to a key from $_POST.
 * @param string $key Key.
 * @param mixed $defaultValue Default value.
 * @return mixed Value associated to key.
 */
function postVal($key, $defaultValue = NULL)
{
    return (isset($_POST[$key])) ? $_POST[$key] : $defaultValue;
}

/**
 * Return value associated to a key from $_GET.
 * @param string $key Key.
 * @param mixed $defaultValue Default value.
 * @return mixed Value associated to key.
 */
function getVal($key, $defaultValue = NULL)
{
    return (isset($_GET[$key])) ? $_GET[$key] : $defaultValue;
}

/**
 * Return value associated to a key from an array.
 * @param array $arr Associative array.
 * @param string $key Key.
 * @param mixed $defaultValue Default value.
 * @return mixed Value associated to key.
 */
function arrVal(&$arr, $key, $defaultValue = NULL)
{
    if (!isset($arr[$key])) {
        return $defaultValue;
    }

    if (is_string($arr[$key])) {
        return trim($arr[$key]);
    }

    return $arr[$key];
}

/**
 * Sends empty response body.
 * @param int $statusCode HTTP Status code.
 * @param array $headers Headers.
 */
function sendEmpty($statusCode, $headers = [])
{
    http_response_code($statusCode);

    foreach ($headers as $header) {
        header($header);
    }

    die;
}

/**
 * Sends JSON response.
 * @param int $statusCode HTTP Status code.
 * @param array $body Body.
 * @param int $flags JSON flags.
 */
function sendJSON($statusCode, $body, $flags = 0)
{
    http_response_code($statusCode);
    header("Content-Type: application/json");
    echo json_encode($body, $flags);
    die;
}

/**
 * Sends error JSON response.
 * @param int $statusCode HTTP Status code.
 * @param string $errCode Error constant.
 * @param string $customMsg Custom error message.
 */
function sendErrJSON($statusCode, $errCode, $customMsg = NULL)
{
    sendJSON($statusCode, [
        "error" => [
            "code"      => $errCode,
            "message"   => $customMsg ?? ERR_MSG[$errCode],
            "trace_id"  => randStr(),
        ]
    ]);
}

/**
 * Sends generic message JSON response.
 * @param int $statusCode HTTP Status code.
 * @param string $msg Message.
 */
function sendMsgJSON($statusCode, $msg)
{
    sendJSON($statusCode, [
        "message" => $msg,
    ]);
}

/**
 * Generates random alphanumeric string.
 * @param int $byteLength Byte length.
 * @return string Random string.
 */
function randStr($byteLength = 8)
{
    return bin2hex(random_bytes($byteLength));
}

/**
 * Generates OTP.
 * @param int $n Number of digits.
 * @return int OTP.
 */
function generateOTP($n = 4)
{
    $generator = "1357902468";
    $result = "";

    for ($i = 1; $i <= $n; $i++) {
        $result .= substr($generator, (rand() % (strlen($generator))), 1);
    }

    return $result;
}

/**
 * Checks if timezone is valid.
 * @param string $timezone Timezone. E.g. Asia/Kolkata
 * @return bool
 */
function validTimezone($timezone)
{
    return in_array($timezone, timezone_identifiers_list());
}

/**
 * Converts datetime from one format to other.
 * @param string $datetime Datetime.
 * @param string $fromFormat Source format.
 * @param string $toFormat Destination format.
 * @return string Converted datetime.
 */
function convertDateTimeFormat($datetime, $fromFormat, $toFormat)
{
    return (DateTime::createFromFormat($fromFormat, $datetime))->format($toFormat);
}

/**
 * Modify timestamp. Add or subtract seconds.
 * @param string $datetime Datetime in YYYY-MM-DD HH:MM:SS
 * @param int $seconds Seconds to add or subtract.
 * @return string Modified timestamp.
 */
function modifyDateTime($datetime, $seconds)
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
function convertTimezone($datetime, $fromTimezone, $toTimezone)
{
    $dt = new DateTime($datetime, new DateTimeZone($fromTimezone));
    $dt->setTimeZone(new DateTimeZone($toTimezone));

    return $dt->format("Y-m-d H:i:s");
}

/**
 * Format price.
 * @param $price Price. E.g. 5.232, 0, 0.129 etc.
 * @return string Formatted price. E.g. 5.23, 0.00, 0.12 etc.
 */
function formatPrice($price)
{
    $roundedPrice = round($price, 2);
    return number_format($roundedPrice, 2);
}

/**
 * Fetches JWT from request headers.
 * @return string JWT.
 */
function getAuthBearerToken()
{
    $token = NULL;

    if (isset($_SERVER["Authorization"])) {
        $token = trim($_SERVER["Authorization"]);
    } elseif (isset($_SERVER["HTTP_AUTHORIZATION"])) { // Nginx or fast CGI.
        $token = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists("apache_request_headers")) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect
        // of this fix means we don't care about capitalization for Authorization).
        $requestHeaders = array_combine(
            array_map('ucwords', array_keys($requestHeaders)),
            array_values($requestHeaders)
        );

        if (isset($requestHeaders["Authorization"])) {
            $token = trim($requestHeaders["Authorization"]);
        }
    }

    if (!empty($token) && preg_match('/Bearer\s(\S+)/', $token, $matches)) {
        return $matches[1];
    }

    return $token;
}

/**
 * Hashes the password.
 * @param string $password Password.
 * @return string Password hash.
 */
function pwHash($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Hashes password with salt.
 * @param string $password Password.
 * @param string $salt Salt.
 * @return Password hash.
 */
function pwHashWithSalt($password, $salt)
{
    $password = "{$password}{$salt}";
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verifies password.
 * @param string $password Password.
 * @param string $hash Password hash.
 * @return bool
 */
function pwVerify($password, $hash)
{
    return password_verify($password, $hash);
}

/**
 * Verifies password.
 * @param string $password Password.
 * @param string $hash Password hash.
 * @param string $salt Salt.
 * @return bool
 */
function pwWithSaltVerify($password, $hash, $salt)
{
    return password_verify("{$password}{$salt}", $hash);
}

/**
 * Returns currency codes based on country.
 * @param string $country Country.
 * @return array Currency, symbol.
 */
function currencyCodes($country)
{
    $country = strtoupper($country);
    return isset(CURRENCY_CODES[$country])
        ? CURRENCY_CODES[$country]
        : [DEFAULT_CURRENCY, DEFAULT_CURRENCY_SYMBOL];
}

/**
 * Returns difference between two timestamps in minutes.
 * @param string $fromDate From date. E.g. 2021-11-09 09:56:18
 * @param string $toDate To date. E.g. 2021-11-09 09:56:18
 * @return int Difference in mins.
 */
function timeDiffInMins($fromDate, $toDate)
{
    $interval = date_diff(date_create($fromDate), date_create($toDate));
    $min = $interval->days * 24 * 60;
    $min += $interval->h * 60;
    $min += $interval->i;

    return $min;
}

/**
 * Check if order status is valid.
 * @param string $status Order status.
 * @return bool Status.
 */
function validOrderStatus($status)
{
    return in_array($status, [
        ORDER_COMPLETED,
        ORDER_IN_PROCESSING,
        ORDER_CANCELLED,
    ]);
}

/**
 * Check if payment mode is valid.
 * @param string $mode Payment mode.
 * @return bool Status.
 */
function validPaymentMode($mode)
{
    return in_array($mode, [
        ORDER_PAYMENT_MODE_CASH,
        ORDER_PAYMENT_MODE_CARD,
        ORDER_PAYMENT_MODE_PAYTM,
    ]);
}

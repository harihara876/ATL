<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Utilities\Helper;
use Plat4m\Core\API\Reports;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;

try {
    Middleware::verifyAuth();

    // Get input params.
    $payload = Request::payload();
    Logger::httpMsg($payload);
    $input = [
        "storeid"  => Helper::arrVal($payload, "storeid"),                          // E.g. 26
        "fromDate" => Helper::arrVal($payload, "from_date"),                        // Formatted in m/d/Y
        "toDate"   => Helper::arrVal($payload, "to_date"),                          // Formatted in H:i a
        "fromTime" => Helper::arrVal($payload, "from_time"),                        // Formatted in m/d/Y
        "toTime"   => Helper::arrVal($payload, "to_time"),                          // Formatted in H:i a
        "tzID"     => Helper::arrVal($payload, "tzID", SERVER_TIMEZONE_ID),         // Timezone ID. E.g. Asia/Calcutta
        "tzShort"  => Helper::arrVal($payload, "tzShort", SERVER_TIMEZONE_SHORT),   // Timezone short name. E.g. IST, GMT, PST
    ];
    $datesSet = function (&$input) {
        return (!empty($input["fromDate"]) && !empty($input["toDate"]));
    };
    $timesSet = function (&$input) {
        return (!empty($input["fromTime"]) && !empty($input["toTime"]));
    };

    if ($datesSet($input)) {
        $input["fromDate"] = Helper::convertDateTimeFormat($input["fromDate"], "m/d/Y", "Y-m-d");
        $input["toDate"] = Helper::convertDateTimeFormat($input["toDate"], "m/d/Y", "Y-m-d");
    }

    if ($timesSet($input)) {
        $input["fromTime"] = Helper::convertDateTimeFormat($input["fromTime"], "H:i A", "H:i:s");
        $input["toTime"] = Helper::convertDateTimeFormat($input["toTime"], "H:i A", "H:i:s");
    } else {
        $input["fromTime"] = "00:00:00";
        $input["toTime"] = "23:59:59";
    }

    $from = Helper::convertTimezone(
        "{$input["fromDate"]} {$input["fromTime"]}",
        $input["tzID"],
        SERVER_TIMEZONE_ID
    );
    $to = Helper::convertTimezone(
        "{$input["toDate"]} {$input["toTime"]}",
        $input["tzID"],
        SERVER_TIMEZONE_ID
    );

    Logger::infoMsg("Product sales from {$from} to {$to}");

    $db = (new DB)->getConn();

    // If from date and to date are set, fetch summary in that period.
    // Else consider all.
    if ($datesSet($input)) {
        $reportsHandler = (new Reports($db))
            ->setStoreAdminID($input["storeid"])
            ->setFromDate($from)
            ->setToDate($to);
        $productSales = $reportsHandler->productSalesInPeriod();
    } else {
        $reportsHandler     = (new Reports($db))->setStoreAdminID($input["storeid"]);
        $productSales       = $reportsHandler->productSales();
    }

    $payload = [
        "product_sales" => $productSales
    ];

    Response::statusCode(200)::body($payload)::json(JSON_PRESERVE_ZERO_FRACTION);
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}

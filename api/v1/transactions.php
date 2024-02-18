<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\Admin;
use Plat4m\Core\API\Reports;
use Plat4m\Utilities\Helper;
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

    // Validate input.
    $datesSet = function (&$input) {
        return (!empty($input["fromDate"]) && !empty($input["toDate"]));
    };
    $timesSet = function (&$input) {
        return (!empty($input["fromTime"]) && !empty($input["toTime"]));
    };
    $validationErrors = [];

    if (empty($input["fromDate"])) {
        $validationErrors[] = "From date must not be empty";
    }

    if (empty($input["toDate"])) {
        $validationErrors[] = "To date must not be empty";
    }

    if (empty($input["fromTime"])) {
        $validationErrors[] = "From time must not be empty";
    }

    if (empty($input["toTime"])) {
        $validationErrors[] = "To time must not be empty";
    }

    // Return error if validation failed.
    if (!empty($validationErrors)) {
        throw new Exception(implode("; ", $validationErrors), 400);
    }

    // Convert input to required format.
    $input["fromDate"] = Helper::convertDateTimeFormat($input["fromDate"], "m/d/Y", "Y-m-d"); // 05/25/2021 to 2021-05-25
    $input["toDate"] = Helper::convertDateTimeFormat($input["toDate"], "m/d/Y", "Y-m-d");     // 05/25/2021 to 2021-05-25
    $input["fromTime"] = Helper::convertDateTimeFormat($input["fromTime"], "H:i A", "H:i:s"); // 12:00 AM to 00:00:00
    $input["toTime"] = Helper::convertDateTimeFormat($input["toTime"], "H:i A", "H:i:s");     // 12:00 PM to 12:00:00

    // Convert to server timezone (UTC).
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

    // if ($input["toTime"] != "00:00:00") {
    //     $to = Helper::modifyDateTime($to, -1); // 00:00:00 (today) to 23:59:59 (yesterday)
    // }

    Logger::infoMsg("Transactions from {$from} to {$to}");

    $db = (new DB)->getConn();

    // Fetch admin and device users info.
    $adminHandler = new Admin($db);
    $admin = $adminHandler->getInfoByID($input["storeid"]);
    $deviceUsers = $adminHandler->getDeviceUsersByAdminID($input["storeid"]);

    // Build a map of id->name for admin and all device users.
    // ALERT: BAD DESIGN: What if admin and device user has same id from different tables?
    $people[$admin["id"]] = $admin["name"];

    foreach ($deviceUsers as $user) {
        $people[$user["id"]] = "{$user["first_name"]} {$user["last_name"]}";
    }

    // Fetch orders.
    $reportsHandler = (new Reports($db))
        ->setStoreAdminID($input["storeid"])
        ->setFromDate($from)
        ->setToDate($to);
    $orders = $reportsHandler->ordersInPeriod();
    Logger::infoMsg(sprintf("Returned transactions count: %d", count($orders)));

    // Update order fields.
    foreach ($orders as &$order) {
        $order["amount"] = (float) $order["amount"];
        $order["tms"] = Helper::convertTimezone($order["tms"], SERVER_TIMEZONE_ID, $input["tzID"]);
        $order["payment_mode"] = !empty($order["payment_mode"]) ? ucwords($order["payment_mode"]) : "NA";
        $order["clerk"] = isset($people[(int) $order["uid"]]) ? $people[(int) $order["uid"]] : "NA";
    }

    // Send response.
    Response::statusCode(200)::body([
        "transaction_history" => $orders
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}

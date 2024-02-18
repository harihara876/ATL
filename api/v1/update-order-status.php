<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\Order;
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
        "orderID" => Helper::arrVal($payload, "order_id"),
        "status"  => Helper::arrVal($payload, "status"),
    ];

    // Validate.
    if (empty($input["orderID"])) {
        throw new Exception("Order ID is required", HTTP_STATUS_BAD_REQUEST);
    }

    if (empty($input["status"])) {
        throw new Exception("Order status is required", HTTP_STATUS_BAD_REQUEST);
    }

    if (!in_array($input["status"], ORDER_STATUS)) {
        throw new Exception("Invalid order status", HTTP_STATUS_BAD_REQUEST);
    }

    // Fetch order details.
    $db = (new DB)->getConn();
    $orderHandler = new Order($db);
    $currentStatus = $orderHandler->getStatus($input["orderID"]);

    if (!$currentStatus) {
        throw new Exception("Order not found", HTTP_STATUS_BAD_REQUEST);
    }

    if ($currentStatus == $input["status"]) {
        Response::statusCode(HTTP_STATUS_OK)::body([
            "message" => "success"
        ])::json();
    }

    $count = $orderHandler->updateStatus($input["orderID"], $input["status"]);
    if (!$count) {
        throw new Exception("Failed to update status", HTTP_STATUS_INTERNAL_SERVER_ERROR);
    }

    Response::statusCode(HTTP_STATUS_OK)::body([
        "message" => "success"
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}
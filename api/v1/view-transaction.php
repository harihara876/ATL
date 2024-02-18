<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\Transaction;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Response;

try {
    Middleware::verifyAuth();

    Logger::httpMsg();
    $orderID = isset($_GET["order_id"]) ? trim($_GET["order_id"]) : NULL;

    if (!$orderID) {
        throw new Exception("Order ID is required", 400);
    }

    $db = (new DB)->getConn();
    $transactionHandler = new Transaction($db);
    $orderInfo = $transactionHandler->getInfoByOrderID($orderID);

    if (!$orderInfo) {
        throw new Exception("Order not found", 404);
    }

    $orderProducts = $transactionHandler->getOrderProductsByOrderRowID($orderInfo["id"]);
    $orderInfo["products"] = $orderProducts;

    Response::statusCode(200)::body([
        "orderInfo" => $orderInfo
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}

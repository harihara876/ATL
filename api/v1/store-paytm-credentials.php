<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\Admin;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;

try {
    Middleware::verifyAuth();

    Logger::httpMsg();
    $payload = Request::payload();

    // Validate.
    if (empty($payload["store_id"])) {
        throw new Exception("Store ID required", HTTP_STATUS_BAD_REQUEST);
    }

    if (empty($payload["mid"])) {
        throw new Exception("Merchant ID required", HTTP_STATUS_BAD_REQUEST);
    }

    if (empty($payload["mkey"])) {
        throw new Exception("Merchant key required", HTTP_STATUS_BAD_REQUEST);
    }

    $paytmCredentials = base64_encode(json_encode([
        "merchant_id" => $payload["mid"],
        "merchant_key" => $payload["mkey"]
    ]));

    $db = (new DB)->getConn();
    $count = (new Admin($db))->storePaytmCredentials($payload["store_id"], $paytmCredentials);
    Logger::infoMsg("Store Paytm credentials; Count: {$count}");

    Response::statusCode(HTTP_STATUS_CREATED)::body(["message" => "success"])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}
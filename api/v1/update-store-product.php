<?php

// TODO: Currently updating description only.
// Use this API to update all info of a store product.
// Compare fields of existing product and update.

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\StoreProduct;
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
        "storeID"       => Helper::arrVal($payload, "storeid"),
        "productID"     => Helper::arrVal($payload, "product_id"),
        "description"   => Helper::arrVal($payload, "description"),
    ];

    // Validate.
    if (empty($input["storeID"])) {
        throw new Exception("Store ID is required", HTTP_STATUS_BAD_REQUEST);
    }

    if (empty($input["productID"])) {
        throw new Exception("Product ID is required", HTTP_STATUS_BAD_REQUEST);
    }

    // Fetch product info.
    $db = (new DB)->getConn();
    $storeProductHandler = (new StoreProduct($db))->setStoreAdminID($input["storeID"]);
    $product = $storeProductHandler->getInfo($input["productID"]);

    if (!$product) {
        throw new Exception("Product does not exist", HTTP_STATUS_BAD_REQUEST);
    }

    // Update only if there is no change in description.
    if ($product["description"] !== $input["description"]) {
        $affectedRows = $storeProductHandler->updateDescription(
            $input["productID"],
            $input["description"]
        );

        if ($affectedRows < 1) {
            throw new Exception("Failed to update", HTTP_STATUS_INTERNAL_SERVER_ERROR);
        }
    }

    Response::statusCode(HTTP_STATUS_ACCEPTED)::body([
        "message" => "success"
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}
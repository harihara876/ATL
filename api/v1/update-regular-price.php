<?php

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
        "storeID"           => Helper::arrVal($payload, "storeid"),
        "productID"         => Helper::arrVal($payload, "product_id"),
        "regularPrice"      => Helper::arrVal($payload, "regular_price"),
        "multiItemQuantity" => Helper::arrVal($payload, "multi_item_quantity"),
        "multiItemPrice"    => Helper::arrVal($payload, "multi_item_price"),
        "discount_percent"  => Helper::arrVal($payload, "discount_percent", 0),
        "discount_pretax"   => Helper::arrVal($payload, "discount_pretax", 0),
        "discount_posttax"  => Helper::arrVal($payload, "discount_posttax", 0),
    ];

    if (empty($input["productID"])) {
        throw new Exception("Product ID must not be empty", 400);
    }

    // Convert product ID string to int.
    $input["productID"] = intval($input["productID"]);

    // if (preg_match('/^\d+\.\d+$/', $input["regularPrice"]) !== 1) {
    //     throw new Exception("Invalid regular price", 400);
    // }

    if (!is_float($input["regularPrice"])) {
        throw new Exception("Invalid regular price", 400);
    }

    $db = (new DB)->getConn();
    $storeProductHandler = (new StoreProduct($db))->setStoreAdminID($input["storeID"]);
    $product = $storeProductHandler->getInfo($input["productID"]);

    if (!$product) {
        throw new Exception("Product does not exist", 400);
    }

    $changed = FALSE;
    $changed = ((float) $product["Regular_Price"] !== (float) $input["regularPrice"]) || $changed;
    $changed = ((int) $product["multi_item_quantity"] !== (int) $input["multiItemQuantity"]) || $changed;
    $changed = ((float) $product["multi_item_price"] !== (float) $input["multiItemPrice"]) || $changed;
    $changed = ((int) $product["discount_percent"] !== (int) $input["discount_percent"]) || $changed;
    $changed = ((int) $product["discount_pretax"] !== (int) $input["discount_pretax"]) || $changed;
    $changed = ((int) $product["discount_posttax"] !== (int) $input["discount_posttax"]) || $changed;

    if ($changed) {
        $affectedRows = $storeProductHandler->updateRegularPrice(
            $input["productID"],
            $input["regularPrice"],
            $input["multiItemQuantity"],
            $input["multiItemPrice"],
            $input["discount_percent"],
            $input["discount_pretax"],
            $input["discount_posttax"]
        );

        if ($affectedRows < 1) {
            throw new Exception("Unable to update regular price", HTTP_STATUS_INTERNAL_SERVER_ERROR);
        }
    }

    Response::statusCode(201)::body([
        "msg" => "success",
        "err" => ""
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body([
        "msg" => "failed",
        "err" => $ex->getMessage()
    ])::json();
}

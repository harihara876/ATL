<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\Product;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;

try {
    Middleware::verifyAuth();

    // Get input params.
    $payload = Request::payload();
    Logger::httpMsg($payload);

    if (empty($payload["storeid"])) {
        throw new Exception("Store ID is required", 400);
    }

    $db = (new DB)->getConn();
    $productHandler = new Product($db);
    $products = $productHandler->getAllByStoreAdminID($payload["storeid"]);
    Logger::infoMsg(sprintf("Returned products: %d", count($products)));

    if (count($products) == 0) {
        throw new Exception("No products found", 404);
    }

    foreach ($products as &$product) {
        $product["multi_item_quantity"] = ($product["multi_item_quantity"] === NULL)
            ? (int) 0
            : (int) ($product["multi_item_quantity"]);
        $product["multi_item_price"] = $product["multi_item_price"] ?? $product["Regular_Price"];
    }

    Response::statusCode(200)::body([
        "output" => $products
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}

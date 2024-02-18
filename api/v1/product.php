<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\Product;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Response;

try {
    Middleware::verifyAuth();

    $db = (new DB)->getConn();
    $productHandler = new Product($db);
    $products = $productHandler->getAllByStoreAdminID(1);
    Logger::infoMsg(sprintf("Returned products: %d", count($products)));

    if (count($products) == 0) {
        throw new Exception("No products found", 404);
    }

    Response::statusCode(200)::body([
        "output" => $products
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}

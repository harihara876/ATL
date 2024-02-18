<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\StoreProduct;
use Plat4m\Core\API\ProductRepository;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;

try {
    Middleware::verifyAuth();

    $payload = Request::payload();
    Logger::httpMsg($payload);

    $storeID = (int) Request::getVal("store_id");

    if (empty($storeID)) {
        throw new Exception("Store ID is required", 400);
    }

    $db = (new DB)->getConn();
    $storeProductHandler = new StoreProduct($db);
    $storeProduct = $storeProductHandler->setStoreAdminID($storeID)->getInfoByUPC($payload["upc"]);

    if (!$storeProduct) {
        $repoProduct = (new ProductRepository($db))->getInfoByUPC($payload["upc"]);

        if (!$repoProduct) {
            throw new Exception("Unable to create. Product not found in repository.", 500);
        }

        $insertID = $storeProductHandler->create($repoProduct);

        if (!$insertID) {
            throw new Exception("Failed to create store product.", 500);
        }

        $storeProduct = $storeProductHandler->getInfoByUPC($payload["upc"]);
    }

    Response::statusCode(201)::body($storeProduct)::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body([
        "error" => $ex->getMessage()
    ])::json();
}

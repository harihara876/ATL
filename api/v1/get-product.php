<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\StoreProduct;
use Plat4m\Core\API\TemporaryProduct;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;

try {
    Middleware::verifyAuth();

    // Get input params.
    $payload = Request::payload();
    Logger::httpMsg($payload);

    $db = (new DB)->getConn();

    $message = "";
    $product = [];
    $category = [];
    $subcategory = [];

    // Override store admin ID to super admin.
    // Because store product exists only if global product exists.
    // So check for global product only.
    $payload["storeadmin_id"] = 1;

    $upcFoundInStoreProducts = (new StoreProduct($db))->checkIfUPCExists($payload['upc'], $payload['storeadmin_id']);

    if (!$upcFoundInStoreProducts) {
        Logger::infoMsg("Admin ID: {$payload["storeadmin_id"]}. Product not found.");
        $error = TRUE;  // if product upc exist in temp product
        $message = "product upc not exist in products table";
        $product_exist = FALSE;
    } else {
        $products = (new TemporaryProduct($db))->getStoreProduct($payload['upc'], $payload['storeadmin_id']);
        $category[] = (new TemporaryProduct($db))->getCategory($products->Category_Id);
        $subcategory[] = (new TemporaryProduct($db))->getSubCategory($products->Category_Type);

        $error = FALSE;  // if product upc exist in temp product
        $message = "product upc exist in products table";
        $product[] = $products;
        $product_exist = TRUE;
    }

    Response::statusCode(200)::body([
        "error"         => $error,
        "message"       => $message,
        "product_exist" => $product_exist,
        "product"       => $product,
        "category"      => $category,
        "subcategory"   => $subcategory
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}

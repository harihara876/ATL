<?php
require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\ProductRepository;
use Plat4m\Core\API\StoreProduct;
use Plat4m\Core\API\TemporaryProduct2;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;
use Plat4m\Utilities\Weather;

try {
    Middleware::verifyAuth();

    // Get input params.
    $productInfo = Request::payload();
    Logger::httpMsg($productInfo);

    if (!isset($productInfo['storeadmin_id'])) {
        throw new Exception("Store admin ID is required", 400);
    }

    if (!isset($productInfo['upc'])) {
        throw new Exception("UPC is required", 400);
    }

    if (!isset($productInfo['price'])) {
        throw new Exception("Price is required", 400);
    }

    if (!isset($productInfo['phone'])) {
        throw new Exception("Phone is required", 400);
    }

    if (!isset($productInfo['email'])) {
        throw new Exception("Email is required", 400);
    }

    if (!isset($productInfo['checkbit'])) {
        throw new Exception("Checkbit is required", 400);
    }

    if (empty($productInfo["category_id"])) {
        $productInfo["category_id"] = DEFAULT_CATEGORY;
    }

    if (empty($productInfo["category_type"])) {
        $productInfo["category_type"] = DEFAULT_SUBCATEGORY;
    }

    $productInfo["latitude"] = isset($productInfo["latitude"]) ? $productInfo["latitude"] : NULL;
    $productInfo["longitude"] = isset($productInfo["longitude"]) ? $productInfo["longitude"] : NULL;
    $productInfo["localtime"] = isset($productInfo["localtime"]) ? $productInfo["localtime"] : NULL;
    $productInfo["weather"] = NULL;

    if ($productInfo["latitude"] && $productInfo["longitude"]) {
        $productInfo["weather"] = Weather::getUpdate($productInfo["latitude"], $productInfo["longitude"]);
    }

    $db = (new DB)->getConn();
    $db->beginTransaction();
    $productHandler = new TemporaryProduct2($db);
    $insertID = $productHandler->setInfo($productInfo)->create();

    if ($insertID) {
        $product = $productHandler->getInfoByID($insertID);

        if (!$product) {
            throw new Exception("Created but failed to fetch info", 500);
        }

        // Create product in repository.
        $repoCreated = (new ProductRepository($db))->create([
            "productID"         => $product["product_id"],
            "productName"       => $product["product_name"] ?: "Temp Product",
            "category"          => $product["category_id"],
            "upc"               => $product["upc"],
            "categoryID"        => $product["category_id"],
            "subcategoryID"     => $product["category_type"],
            "createdAt"         => $product["created_on"],
            "image"             => $product["image"],
            "manufacturer"      => $product["manufacturer"],
            "brand"             => $product["brand"],
            "vendor"            => $product["vendor"],
            "status"            => 1,
        ]);
        $storeProductHandler = (new StoreProduct($db))->setStoreAdminID(1);
        $info = [
            "product_id"        => $product["product_id"],
            "description"       => $product["description"],
            "POS_description"   => NULL,
            "price"             => $product["price"],
            "sellprice"         => $product["price"], // Must be selling_price
            "color"             => $product["color"],
            "size"              => $product["size"],
            "product_status"    => $product["product_status"] ?: "Instock",
            "quantity"          => $product["quantity"] ?: 20,
            "plimit"            => $product["p_limit"] ?: 1,
            "Regular_Price"     => $product["regular_price"],
            "Buying_Price"      => $product["buying_price"],
            "Tax_Status"        => $product["tax_status"],
            "Tax_Value"         => $product["tax_value"],
            "Special_Value"     => $product["special_value"],
            "Date_Created"      => $product["created_on"],
            "SKU"               => $product["sku"],
            "Stock_Quantity"    => $product["stock_quantity"],
            "ProductMode"       => $product["product_mode"],
            "Age_Restriction"   => $product["age_restriction"],
            "sale_type"         => $product["sale_type"],
            "status"            => 1
            // "discount_percent"  => $product["discount_percent"],
            // "discount_pretax"   => $product["discount_pretax"],
            // "discount_posttax"  => $product["discount_posttax"],
        ];
        $storeProductHandler->create($info);
        $storeProductHandler->setStoreAdminID($productInfo["storeadmin_id"]);
        $storeProductHandler->create($info);
        $db->commit();
    } else {
        throw new Exception("Failed to create", 500);
    }

    $product["checkbit"] = (int) $product["checkbit"];
    Response::statusCode(200)::body($product)::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}

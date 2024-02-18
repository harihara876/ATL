<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\TemporaryProduct;
use Plat4m\Utilities\Helper;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;

try {
    Middleware::verifyAuth();

    // Get input params.
    $payload = Request::payload();
    Logger::httpMsg($payload);

    $storeAdminID = Helper::arrVal($payload, "storeid");
    $db = (new DB)->getConn();
    $tmpProductHandler = new TemporaryProduct($db);

    if ($storeAdminID) {
        $products = $tmpProductHandler->getAllByStoreID($storeAdminID);
    } else {
        $products = $tmpProductHandler->getAll();
    }

    $formattedProducts = [];

    if ($products) {
        foreach ($products as $product) {
            $multiItemQuantity = ($product["multi_item_quantity"] === NULL)
                ? (int) 0
                : (int) ($product["multi_item_quantity"]);
            $multiItemPrice = $product["multi_item_price"] ?? $product["regular_price"];
            $formattedProducts[] = [
                "id"                    => $product["id"],
                "product_name"          => $product["product_name"],
                "product_id"            => $product["product_id"],
                "cat_id"                => $product["cat_id"],
                "description"           => $product["description"],
                "price"                 => $product["price"],
                "sellprice"             => $product["selling_price"],
                "color"                 => $product["color"],
                "size"                  => $product["size"],
                "product_status"        => $product["product_status"],
                "quantity"              => $product["quantity"],
                "date"                  => $product["date"],
                "plimit"                => $product["p_limit"],
                "UPC"                   => $product["upc"],
                "Regular_Price"         => $product["regular_price"],
                "Buying_Price"          => $product["buying_price"],
                "Tax_Status"            => $product["tax_status"],
                "Tax_Value"             => $product["tax_value"],
                "Special_Value"         => $product["special_value"],
                "Category_Id"           => $product["category_id"],
                "Category_Type"         => $product["category_type"],
                "Date_Created"          => $product["date_created"],
                "SKU"                   => $product["sku"],
                "Image"                 => $product["image"],
                "Stock_Quantity"        => $product["stock_quantity"],
                "Manufacturer"          => $product["manufacturer"],
                "Brand"                 => $product["brand"],
                "Vendor"                => $product["vendor"],
                "ProductMode"           => $product["product_mode"],
                "Age_Restriction"       => $product["age_restriction"],
                "sale_type"             => $product["sale_type"],
                "storeadmin_id"         => $product["storeadmin_id"],
                "multi_item_quantity"   => $multiItemQuantity,
                "multi_item_price"      => $multiItemPrice,
                "discount_percent"      => $product["discount_percent"],
                "discount_pretax"       => $product["discount_pretax"],
                "discount_posttax"      => $product["discount_posttax"],
            ];
        }
    } else {
        throw new Exception("No products found", 404);
    }

    Logger::infoMsg(sprintf("Returned temp products count: %d", count($formattedProducts)));
    Response::statusCode(200)::body([
        "output" => $formattedProducts
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}

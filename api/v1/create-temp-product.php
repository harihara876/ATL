<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\Category;
use Plat4m\Core\API\Subcategory;
use Plat4m\Core\API\ProductFormatter;
use Plat4m\Core\API\ProductRepository;
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

    // Check if UPC exists in `products` table.
    $upcFoundInProducts = (new ProductRepository($db))->upcExists($payload["upc"]);

    if ($upcFoundInProducts) {
        // Check if UPC exists in store products.
        $upcFoundInStoreProducts = (new StoreProduct($db))->checkIfUPCExists(
            $payload["upc"],
            $payload["storeadmin_id"]
        );

        if (!$upcFoundInStoreProducts) {
            $productInfo = (new ProductRepository($db))->getInfoByUPC($payload["upc"]);
            $insertID = (new StoreProduct($db))
                ->setStoreAdminID($payload["storeadmin_id"])
                ->create([
                    "product_id"            => $productInfo["product_id"],
                    "description"           => $productInfo["description"],
                    "POS_description"       => $productInfo["POS_description"],
                    "price"                 => $payload["price"],
                    "sellprice"             => $payload["selling_price"],
                    "color"                 => $productInfo["color"],
                    "size"                  => $productInfo["size"],
                    "product_status"        => $productInfo["product_status"],
                    "quantity"              => $productInfo["quantity"],
                    "plimit"                => $productInfo["plimit"],
                    "Regular_Price"         => $payload["regular_price"],
                    "Buying_Price"          => $payload["buying_price"],
                    "Tax_Status"            => $payload["tax_status"],
                    "Tax_Value"             => $payload["tax_value"],
                    "Special_Value"         => $payload["special_value"],
                    "Date_Created"          => $productInfo["Date_Created"],
                    "SKU"                   => $productInfo["SKU"],
                    "Stock_Quantity"        => $productInfo["Stock_Quantity"],
                    "ProductMode"           => $productInfo["ProductMode"],
                    "Age_Restriction"       => $productInfo["Age_Restriction"],
                    "sale_type"             => $productInfo["sale_type"],
                    "status"                => $productInfo["status"],
                    "multi_item_quantity"   => $payload["multi_item_quantity"],
                    "multi_item_price"      => $payload["multi_item_price"],
                    "discount_percent"      => $payload["discount_percent"],
                    "discount_pretax"       => $payload["discount_pretax"],
                    "discount_posttax"      => $payload["discount_posttax"],
                ]);

            if (!$insertID) {
                Logger::errorMsg("Failed to copy from repo to store.");
                throw new Exception("Failed to create product", 500);
            }
        }

        $product = (new StoreProduct($db))
            ->setStoreAdminID($payload['storeadmin_id'])
            ->getFullInfo($payload['upc']);
        Response::statusCode(200)::body([
            "product"       => ProductFormatter::formatRepoProduct($product),
            "category"      => (new Category($db))->getInfoByID($product["Category_Id"]),
            "subcategory"   => (new Subcategory($db))->getInfoByID($product["Category_Type"]),
        ])::json();
    } else {
        // Check if UPC exists in `products_temp` table.
        $upcFoundInProductsTemp = (new TemporaryProduct($db))->checkIfUPCExists(
            $payload["upc"],
            $payload["storeadmin_id"]
        );

        if (!$upcFoundInProductsTemp) {
            // Create product in `products_temp` table.
            $insertID = (new TemporaryProduct($db))->setInfo($payload)->create();

            if (!$insertID) {
                Logger::errorMsg("Failed to create product in products_temp");
                throw new Exception("Failed to create product", 500);
            }
        }

        $product = (new TemporaryProduct($db))->getInfo(
            $payload["upc"],
            $payload["storeadmin_id"]
        );
        $category = (new Category($db))->getInfoByID($product["category_id"]);
        $subcategory = (new Subcategory($db))->getInfoByID($product["category_type"]);
        Response::statusCode(200)::body([
            "product"       => ProductFormatter::formatTempProduct($product),
            "category"      => empty($category) ? NULL : $category,
            "subcategory"   => empty($subcategory) ? NULL : $subcategory,
        ])::json();
    }
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}

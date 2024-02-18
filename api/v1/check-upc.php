<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\Product;
use Plat4m\Core\API\TemporaryProduct2;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;

/**
 * Trims value and returns default if empty.
 * @param mixed $val Value.
 * @param mixed $defaultValue Default value.
 * @return mixed Trimmed value or default value.
 */
function trimVal($val, $defaultValue = NULL)
{
    $val = trim($val);
    return empty($val) ? $defaultValue : $val;
}

/**
 * Formats product info.
 * @param array $info Product info.
 * @return array Formatted product info.
 */
function formatProduct($info)
{
    return [
        "upc"           => trimVal($info["UPC"]),
        "product_id"    => trimVal($info["product_id"]),
        "product_name"  => trimVal($info["product_name"]),
        "category_id"   => trimVal($info["Category_Id"]),
        "category_type" => trimVal($info["Category_Type"]),
        "image"         => trimVal($info["Image"]),
        "manufacturer"  => trimVal($info["Manufacturer"]),
        "brand"         => trimVal($info["Brand"]),
        "vendor"        => trimVal($info["Vendor"]),
        "created_on"    => trimVal($info["created_time"]),
        "price"         => trimVal($info["price"]),
        "description"   => trimVal($info["description"]),
        "phone"         => trimVal(isset($info["phone"]) ? $info["phone"] : NULL),
        "email"         => trimVal(isset($info["email"]) ? $info["email"] : NULL),
        "checkbit"      => trimVal(isset($info["checkbit"]) ? $info["checkbit"] : NULL),
    ];
}

/**
 * Formats temp product info.
 * @param array $info Product info.
 * @return array Formatted product info.
 */
function formatProductTemp($info)
{
    return [
        "upc"           => trimVal($info["upc"]),
        "product_id"    => trimVal($info["product_id"]),
        "product_name"  => trimVal($info["product_name"]),
        "category_id"   => trimVal($info["category_id"]),
        "category_type" => trimVal($info["category_type"]),
        "image"         => trimVal($info["image"]),
        "manufacturer"  => trimVal($info["manufacturer"]),
        "brand"         => trimVal($info["brand"]),
        "vendor"        => trimVal($info["vendor"]),
        "created_on"    => trimVal($info["created_on"]),
        "price"         => trimVal($info["price"]),
        "description"   => trimVal($info["description"]),
        "phone"         => trimVal(isset($info["phone"]) ? $info["phone"] : NULL),
        "email"         => trimVal(isset($info["email"]) ? $info["email"] : NULL),
        "checkbit"      => trimVal(isset($info["checkbit"]) ? $info["checkbit"] : NULL),
    ];
}

try {
    Middleware::verifyAuth();

    // Get input params.
    $payload = Request::payload();
    Logger::httpMsg($payload);

    if (empty($payload["UPC"])) {
        throw new Exception("UPC is required", 400);
    }

    $db = (new DB)->getConn();

    // Fetch from products table.
    $product = (new Product($db))->getInfoByUPC($payload["UPC"]);

    if ($product) {
        Logger::infoMsg(sprintf("UPC: %s found in products.", $payload["UPC"]));
        Response::statusCode(200)::body(formatProduct($product))::json();
    }

    // Fetch from products_temp2 table.
    $tempProduct = (new TemporaryProduct2($db))->getInfoByUPC($payload["UPC"]);

    if ($tempProduct) {
        Logger::infoMsg(sprintf("UPC: %s found in products_temp2.", $payload["UPC"]));
        Response::statusCode(200)::body(formatProductTemp($tempProduct))::json();
    }

    Logger::infoMsg(sprintf(
        "UPC: %s not found in either products or products_temp2.",
        $payload["UPC"]
    ));
    Response::statusCode(404)::body([
        "UPC" => $payload["UPC"]
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body([
        "error" => $ex->getMessage()
    ])::json();
}

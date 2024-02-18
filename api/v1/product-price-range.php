<?php

require_once("../../init/init.php");

use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\Product;
use Plat4m\Core\API\ProductImage;
use Plat4m\Core\API\TemporaryProduct;
use Plat4m\Core\API\TemporaryProduct2;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;

try {
    Middleware::verifyAuth();

    // Get input params.
    $payload = Request::payload();
    Logger::httpMsg($payload);

    if (empty($payload["upc"])) {
        throw new Exception("UPC is required", 400);
    }

    if (empty($payload["product_id"])) {
        throw new Exception("Product ID is required", 400);
    }

    $db = (new DB)->getConn();
    $prices = (new Product($db))->getSellingPriceRangeAcrossAll($payload["upc"]);
    $images = (new ProductImage($db))->getListByProductID($payload["product_id"]);
    $formattedImageURLs = [];

    foreach ($images as $image) {
        $formattedImageURLs[] = URL_HOST . "/" . $image["image"];
    }

    // Get images from products_temp.
    $tempImages = (new TemporaryProduct($db))->getImagesByUPC($payload["upc"]);
    if (count($tempImages)) {
        foreach ($tempImages as $image) {
            if (empty($image["image"])) continue;
            $formattedImageURLs[] = $image["image"];
        }
    }

    // Get images from products_temp2.
    $temp2Images = (new TemporaryProduct2($db))->getImagesByUPC($payload["upc"]);
    if (count($temp2Images)) {
        foreach ($temp2Images as $image) {
            if (empty($image["image"])) continue;
            $formattedImageURLs[] = $image["image"];
        }
    }

    Response::statusCode(200)::body([
        "min_selling_price" => $prices["min_price"],
        "max_selling_price" => $prices["max_price"],
        "product_images"    => $formattedImageURLs
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body(["error" => $ex->getMessage()])::json();
}

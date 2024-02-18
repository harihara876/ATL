<?php

require_once("../../init/init.php");

use Plat4m\App\Auth;
use Plat4m\App\DB;
use Plat4m\App\Middleware;
use Plat4m\Core\API\Product;
use Plat4m\Core\API\TemporaryProduct;
use Plat4m\Core\API\Order;
use Plat4m\Utilities\Logger;
use Plat4m\Utilities\Request;
use Plat4m\Utilities\Response;
use Plat4m\Utilities\Weather;

try {
    Middleware::verifyAuth();

    $error = TRUE;
    $bulkOrders = Request::payload();
    Logger::httpMsg($bulkOrders);

    foreach ($bulkOrders as $orderInfo) {
        $paymentMode = "";

        if (empty($orderInfo["order_id"])) {
            throw new Exception("Order ID must not be empty");
        }

        if (empty($orderInfo["total_amount"])) {
            throw new Exception("Total amount must not be empty");
        }

        if (!empty($orderInfo["payment_mode"])) {
            $paymentMode = strtolower(trim($orderInfo["payment_mode"]));
        }

        if (empty($orderInfo["uid"])) {
            throw new Exception("UID must not be empty");
        }

        if (empty($orderInfo["products"])) {
            throw new Exception("Products must not be empty");
        }

        $inProductIDs = [];

        foreach ($orderInfo["products"] as $product) {
            if (!in_array($product["product_id"], $inProductIDs)) {
                $inProductIDs[] = $product["product_id"];
            }
        }

        $db = (new DB)->getConn();

        // Fetch products info from `products` table.
        $productHandler = new Product($db);
        $productsFromDB = $productHandler->getMultipleProductsByIDs($inProductIDs);

        // If count did not match, fetch products from `products_temp` table.
        if (count($inProductIDs) !== count($productsFromDB)) {
            Logger::infoMsg(sprintf(
                "Count of input products (%d) did not match with count of products from products table (%d)",
                count($inProductIDs),
                count($productsFromDB)
            ));

            $foundProductIDs = [];

            foreach ($productsFromDB  as $product) {
                $foundProductIDs[] = $product["product_id"];
            }

            // Fetch products info from `products_temp` table.
            $tmpProductIDs = array_diff($inProductIDs, $foundProductIDs);
            $tmpProductHandler = new TemporaryProduct($db);
            $tmpProductsFromDB = $tmpProductHandler->getMultipleProductsByIDs($inProductIDs);

            if (count($tmpProductsFromDB)) {
                $productsFromDB = array_merge($productsFromDB, $tmpProductsFromDB);
                Logger::infoMsg(sprintf(
                    "Fetched %d product(s) %s from products_temp table.",
                    count($tmpProductsFromDB),
                    implode(",", $tmpProductIDs)
                ));
            }
        }

        $latitude = isset($orderInfo["latitude"]) ? $orderInfo["latitude"] : NULL;
        $longitude = isset($orderInfo["longitude"]) ? $orderInfo["longitude"] : NULL;
        $localtime = isset($orderInfo["localtime"]) ? $orderInfo["localtime"] : NULL;
        $weather = NULL;

        if ($latitude && $longitude) {
            $weather = Weather::getUpdate($latitude, $longitude);
        }

        $created = (new Order($db))
            ->setInfo([
                "orderID"           => $orderInfo["order_id"],
                "totalAmount"       => $orderInfo["total_amount"],
                "paymentMode"       => $paymentMode,
                "uid"               => $orderInfo["uid"],
                "latitude"          => $latitude,
                "longitude"         => $longitude,
                "localtime"         => $localtime,
                "weather"           => $weather,
                "productsFromReq"   => $orderInfo["products"],
                "productsFromDB"    => $productsFromDB
            ])
            ->create();

        if ($created) {
            $error = FALSE;
        }
    }

    Response::statusCode(200)::body([
        "error" => $error
    ])::json();
} catch (Exception $ex) {
    Logger::errExcept($ex);
    Response::statusCode($ex->getCode())::body([
        "error" => TRUE,
        "message" => $ex->getMessage()
    ])::json();
}

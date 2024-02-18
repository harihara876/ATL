<?php

use Plat4mAPI\Model\Transaction;
use Plat4mAPI\Model\StoreProduct;
use Plat4mAPI\Model\StoreTempProduct;
use Plat4mAPI\Util\Logger;
use Plat4mAPI\Util\Validator;
use Plat4mAPI\Util\Weather;

/**
 * Validate order info.
 * @param object $v Validator.
 * @param array $order Order info.
 */
function validateOrder(&$v, &$order)
{
    $v->name("Order UUID")->str($order["order_uuid"])->reqStr();
    $v->name("Total price")->nFloat($order["total_amount"]);
    $v->name("Order date")->str($order["order_date"])->formatDT();
    $v->name("Total Tax")->nFloat($order["total_tax"]);
    $v->name("Total Special value")->nFloat($order["total_special_fee"]);

    if ($order["order_status"]) {
        $v->name("Order status")->str($order["order_status"]);
    }

    if (!validPaymentMode($order["payment_mode"])) {
        $v->appendErr("Invalid payment mode");
    }

    if ($order["payment_mode"] !== "") {
        $v->name("Payment mode")->str($order["payment_mode"]);
    }

    if ($order["payment_status"]) {
        $v->name("Payment status")->str($order["payment_status"]);
    }

    if ($order["latitude"]) {
        $v->name("Latitude")->str($order["latitude"]);
    }

    if ($order["longitude"]) {
        $v->name("Longitude")->str($order["longitude"]);
    }

    if ($order["localtime"]) {
        $v->name("Local time")->str($order["localtime"]);
    }

    if ($order["mobile_number"] !== "") {
        $v->name("Mobile number")->str($order["mobile_number"]);
    }

    if ($order["address"] !== "") {
        $v->name("Address")->str($order["address"]);
    }

    if (count($order["products"]) < 1) {
        $v->appendErr("Ordered products required");
    }
}

/**
 * Validate ordered products.
 * @param object $v Validator.
 * @param array $product Product info.
 */
function validateOrderedProduct(&$v, &$product)
{
    $v->name("Product ID")->nInt($product["product_id"]);
    $v->name("Product name")->str($product["name"])->reqStr();
    $v->name("Quantity")->nInt($product["quantity"]);
    $v->name("Selling price")->nFloat($product["selling_price"]);
    $v->name("Tax")->nFloat($product["tax"]);
    $v->name("Special value")->nFloat($product["special_value"]);
}

/**
 * Create order handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function createOrderV1($ctx, $args)
{
    $v = new Validator;
    $bulkOrders = payload();

    $v->name("payload")->checkRequest($bulkOrders);
    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    Logger::httpMsg($bulkOrders);

    $ctx->db->beginTransaction();
    $orderIDs = [];

    foreach ($bulkOrders as $order) {
        $inOrder = [
            "order_uuid"        => arrVal($order, "order_uuid"),
            "total_amount"      => arrVal($order, "total_amount"),
            "total_tax"         => arrVal($order, "total_tax"),
            "total_special_fee" => arrVal($order, "total_special_fee"),
            "order_date"        => arrVal($order, "order_date", $ctx->now),
            "order_status"      => arrVal($order, "order_status", ORDER_COMPLETED),
            "payment_mode"      => arrVal($order, "payment_mode", ""),
            "payment_status"    => arrVal($order, "payment_status", ORDER_PAYMENT_COMPLETED),
            "payment_reference" => arrVal($order, "payment_reference", ""),
            "products"          => arrVal($order, "products", []),
            "latitude"          => arrVal($order, "latitude"),
            "longitude"         => arrVal($order, "longitude"),
            "localtime"         => arrVal($order, "localtime"),
            "mobile_number"     => arrVal($order, "mobile_number", ""),
            "address"           => arrVal($order, "address", ""),
        ];
        $input["payment_mode"] = strtolower(trim($inOrder["payment_mode"]));

        $v = new Validator;
        validateOrder($v, $inOrder);

        if ($v->anyErrors()) {
            $ctx->db->rollBack();
            sendErrJSON(400, ERR_VALIDATION, $v->errStr());
        }

        $weather = NULL;

        if ($inOrder["latitude"] == 0.0 && $inOrder["longitude"] == 0.0) {
            $inOrder["latitude"] = NULL;
            $inOrder["longitude"] = NULL;
        }

        if ($inOrder["latitude"] && $inOrder["longitude"]) {
            $weather = Weather::getUpdate($inOrder["latitude"], $inOrder["longitude"]);
        }

        $inOrder["admin_id"] = $ctx->tokenData->store_admin_id;
        $inOrder["cashier_id"] = ($ctx->tokenData->type == USER_CASHIER) ? $ctx->tokenData->id : NULL;
        $inOrder["user_id"] = $ctx->tokenData->id;
        $inOrder["weather"] = $weather;
        $inOrder["created_on"] = $ctx->now;
        $inOrder["updated_on"] = $ctx->now;

        // Insert order record.
        $transactionModel = new Transaction;

        $orderUUIDExists= $transactionModel->orderUUIDExists($ctx,$order["order_uuid"]);
        if ($orderUUIDExists) {
            sendJSON(201,["order_ids" => "Order Unique id already exists"]);
        }
        $orderID = $transactionModel->createOrder($ctx, $inOrder);

        if (!$orderID) {
            $ctx->db->rollBack();
            Logger::errorMsg("Failed to insert order");
            sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "Failed to create order");
        }

        $orderID = (int) $orderID;
        $orderIDs[] = $orderID;

        foreach ($inOrder["products"] as $product) {
            $inProduct = [
                "product_id"        => arrVal($product, "product_id"),
                "name"              => arrVal($product, "name"),
                "quantity"          => arrVal($product, "quantity"),
                "selling_price"     => arrVal($product, "selling_price"),
                "tax"               => arrVal($product, "tax"),
                "special_value"     => arrVal($product, "special_value"),
                "image_url"         => arrVal($product, "image_url", ""),
                "size"              => arrVal($product, "size", ""),
                "status"            => arrVal($product, "status", ""),
                "color"             => arrVal($product, "color", ""),
            ];

            $v = new Validator;
            validateOrderedProduct($v, $inProduct);

            if ($v->anyErrors()) {
                $ctx->db->rollBack();
                sendErrJSON(400, ERR_VALIDATION, $v->errStr());
            }
            //Check if product exists or not by using product id.
           
            $productExistsStore= $transactionModel->productExists($ctx, $inProduct["product_id"]);
            $productExistsTemp= $transactionModel->tempProductExists($ctx, $inProduct["product_id"]);

            // Insert ordered product.
            $inProduct["order_id"] = $orderID;
            if ($productExistsStore) {

                $productID = $transactionModel->createOrderedProduct($ctx, $inProduct);
                //Deduct stock quantity from product details table.
                $data['product_id'] = $inProduct["product_id"];
                $data['stock_quantity'] = $productExistsStore[0]['Stock_Quantity'] - $inProduct["quantity"];
                $deductStock = $transactionModel->deductQuantity($ctx,$data);

                if (!$productID) {
                    $ctx->db->rollBack();
                    Logger::errorMsg("Failed to insert order product");
                    sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "Failed to create order");
                }
            } else if ($productExistsTemp) {
                $productID = $transactionModel->createOrderedProduct($ctx, $inProduct);

                //Deduct stock quantity from product details table.
                $data['product_id'] = $inProduct["product_id"];
                $data['stock_quantity'] = $productExistsTemp[0]['stock_quantity'] - $inProduct["quantity"];
                $deductStock = $transactionModel->deductTempQuantity($ctx,$data);

                if (!$productID) {
                    $ctx->db->rollBack();
                    Logger::errorMsg("Failed to insert order product");
                    sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "Failed to create order");
                }
            } else {
                sendErrJSON(404, ERR_PRODUCT_NOT_FOUND);
            }
            
        }
    }

    $ctx->db->commit();

    $orderProducts = $transactionModel->getOrderProductsByOrderRowID(
        $ctx,
        $inProduct["order_id"]
    );

    foreach ($orderProducts as &$product) {
        $productExistsInStore= $transactionModel->productExists($ctx, $product["product_id"]);
        $productExistsInTempStore= $transactionModel->tempProductExists($ctx, $product["product_id"]);

        if ($productExistsInStore) {
            // $soldout_quantity = $transactionModel->getSoldOutQty($ctx, $product["product_id"]);
            $product["stock_quantity"] = $productExistsInStore[0]['Stock_Quantity'];
            $product["product_type"]   = "STORE-PRODUCT";
        } else if ($productExistsInTempStore) {
            // $soldout_quantity = $transactionModel->getSoldOutQty($ctx, $product["product_id"]);
            $product["stock_quantity"] = $productExistsInTempStore[0]['stock_quantity'];
            $product["product_type"]   = "STORE-TEMP-PRODUCT";
        } else {
            $product["stock_quantity"] = 0;
        }

    }

    $orderInfo["products"] = $orderProducts;

    sendJSON(201, $orderInfo);
}

/**
 * View order handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function viewOrderV1($ctx, $args)
{
    $orderID = (int) $args["order_id"];

    $v = new Validator;
    $v->name("Order ID")->nInt($orderID);

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $transactionModel = new Transaction;
    $orderInfo = $transactionModel->getInfoByOrderID($ctx, $orderID);

    if (!$orderInfo) {
        sendErrJSON(400, ERR_ORDER_NOT_FOUND);
    }

    $orderProducts = $transactionModel->getOrderProductsByOrderRowID(
        $ctx,
        $orderInfo["id"]
    );

    foreach ($orderProducts as &$product) {
        $productExistsInStore= $transactionModel->productExists($ctx, $product["product_id"]);
        $productExistsInTempStore= $transactionModel->tempProductExists($ctx, $product["product_id"]);

        if ($productExistsInStore) {
            // $soldout_quantity = $transactionModel->getSoldOutQty($ctx, $product["product_id"]);
            $product["stock_quantity"] = $productExistsInStore[0]['Stock_Quantity'];
        } else if ($productExistsInTempStore) {
            // $soldout_quantity = $transactionModel->getSoldOutQty($ctx, $product["product_id"]);
            $product["stock_quantity"] = $productExistsInTempStore[0]['stock_quantity'];
        } else {
            $product["stock_quantity"] = 0;
        }

    }

    $orderInfo["products"] = $orderProducts;
    sendJSON(200, $orderInfo);
}

/**
 * Update order status handler.
 * @param object $ctx Context.
 * @param array $args Arguments.
 */
function updateOrderStatusV1($ctx, $args)
{
    $orderID = (int) $args["order_id"];
    $payload = payload();
    $input = [
        "status" => arrVal($payload, "status"),
    ];

    $v = new Validator;
    $v->name("Order ID")->nInt($orderID);

    if (!validOrderStatus($input["status"])) {
        $v->appendErr("Invalid order status");
    }

    if ($v->anyErrors()) {
        sendErrJSON(400, ERR_VALIDATION, $v->errStr());
    }

    $transactionModel = new Transaction;
    $orderInfo = $transactionModel->getInfoByOrderID($ctx, $orderID);

    if (!$orderInfo) {
        sendErrJSON(400, ERR_ORDER_NOT_FOUND);
    }

    $updated = $transactionModel->updateStatus($ctx, $orderID, $input["status"]);

    if (!$updated) {
        sendErrJSON(500, ERR_INTERNAL_SERVER_ERROR, "Failed to update order status");
    }

    $orderInfo["order_status"] = $input["status"];
    $orderInfo["updated_on"] = $ctx->now;
    $orderProducts = $transactionModel->getOrderProductsByOrderRowID(
        $ctx,
        $orderInfo["id"]
    );
    $orderInfo["products"] = $orderProducts;

    sendJSON(200, ["orderInfo" => $orderInfo]);
}

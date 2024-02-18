<?php

namespace Plat4mAPI\Model;

use PDO;

class Transaction
{
    /**
     * Format order info.
     * @param array $order Order info.
     * @return array Order info.
     */
    public function format(&$order)
    {
        if (!$order) {
            return NULL;
        }

        return [
            "id"                    => (int) $order["id"],
            "order_uuid"            => (string) $order["order_uuid"],
            "order_date"            => (string) $order["order_date"],
            "order_status"          => (string) $order["order_status"],
            "payment_status"        => (string) $order["payment_status"],
            "payment_mode"          => (string) $order["payment_mode"],
            "payment_reference"     => (string) $order["payment_reference"],
            "total_amount"          => (float) $order["total_amount"],
            "total_tax"             => (float) $order["total_tax"],
            "total_special_fee"     => (float) $order["total_special_fee"],
            "mobile_number"         => (string) $order["mobile_number"],
            "address"               => (string) $order["address"],
            "admin_id"              => (int) $order["admin_id"],
            "cashier_id"            => $order["cashier_id"] ? (int) $order["cashier_id"] : NULL,
            "created_on"            => (string) $order["created_on"],
            "updated_on"            => (string) $order["updated_on"],
        ];
    }

    /**
     * Format order products.
     * @param array $products Products info.
     * @return array Formatted info.
     */
    public function formatOrderProducts(&$products)
    {
        $formattedProducts = [];

        foreach ($products as $product) {
            $formattedProducts[] = [
                "id"                => (int) $product["id"],
                "order_id"          => (int) $product["order_id"],
                "product_id"        => (int) $product["product_id"],
                "name"              => (string) $product["name"],
                "image_url"         => (string) $product["image_url"],
                "quantity"          => (int) $product["quantity"],
                // "stock_quantity"          => (int) $product["stock_quantity"],
                // "soldout_quantity"          => (int) $product["soldout_quantity"],
                "selling_price"     => (float) $product["selling_price"],
                "size"              => (string) $product["size"],
                "special_value"     => (float) $product["special_value"],
                "status"            => (string) $product["status"],
                "tax"               => (float) $product["tax"],
                "color"             => (string) $product["color"]
            ];
        }

        return $formattedProducts;
    }

    /**
     * Create order.
     * @param object $ctx Context.
     * @param array $order Order info.
     * @return int Last insert ID.
     */
    public function createOrder($ctx, $order)
    {
        $insertSQL = "INSERT INTO `users_orders` (
                `order_id`,
                `paymentref`,
                `paymentmode`,
                `payment_status`,
                `address`,
                `order_date`,
                `order_status`,
                `phone`,
                `total`,
                `total_tax`,
                `total_special_fee`,
                `uid`,
                `latitude`,
                `longitude`,
                `localtime`,
                `weather`,
                `admin_id`,
                `cashier_id`,
                `created_on`,
                `updated_on`
            ) VALUES (
                :order_uuid,
                :payment_reference,
                :payment_mode,
                :payment_status,
                :address,
                :order_date,
                :order_status,
                :mobile_number,
                :total_amount,
                :total_tax,
                :total_special_fee,
                :user_id,
                :latitude,
                :longitude,
                :localtime,
                :weather,
                :admin_id,
                :cashier_id,
                :created_on,
                :updated_on
            )";
        $stmt = $ctx->db->prepare($insertSQL);
        $stmt->bindValue(":order_uuid", $order["order_uuid"]);
        $stmt->bindValue(":payment_reference", $order["payment_reference"]);
        $stmt->bindValue(":payment_mode", $order["payment_mode"]);
        $stmt->bindValue(":payment_status", $order["payment_status"]);
        $stmt->bindValue(":address", $order["address"]);
        $stmt->bindValue(":order_date", $order["order_date"]);
        $stmt->bindValue(":order_status", $order["order_status"]);
        $stmt->bindValue(":mobile_number", $order["mobile_number"]);
        $stmt->bindValue(":total_amount", $order["total_amount"]);
        $stmt->bindValue(":total_tax", $order["total_tax"]);
        $stmt->bindValue(":total_special_fee", $order["total_special_fee"]);
        $stmt->bindValue(":user_id", $order["user_id"]);
        $stmt->bindValue(":latitude", $order["latitude"]);
        $stmt->bindValue(":longitude", $order["longitude"]);
        $stmt->bindValue(":localtime", $order["localtime"]);
        $stmt->bindValue(":weather", $order["weather"]);
        $stmt->bindValue(":admin_id", $order["admin_id"]);
        $stmt->bindValue(":cashier_id", $order["cashier_id"]);
        $stmt->bindValue(":created_on", $order["created_on"]);
        $stmt->bindValue(":updated_on", $order["updated_on"]);
        $stmt->execute();

        return $ctx->db->lastInsertId();
    }

    /**
     * Create ordered product.
     * @param object $ctx Context.
     * @param array $product Product info.
     * @return int Last insert ID.
     */
    public function createOrderedProduct($ctx, $product)
    {
        $insertSQL = "INSERT INTO `ordered_product` (
                `order_id`,
                `product_name`,
                `product_id`,
                `quantity`,
                `sellprice`,
                `tax`,
                `Special_Value`,
                `product_image`,
                `size`,
                `status`,
                `color`
            ) VALUES (
                :order_id,
                :name,
                :product_id,
                :quantity,
                :selling_price,
                :tax,
                :special_value,
                :image_url,
                :size,
                :status,
                :color
            )";
        $stmt = $ctx->db->prepare($insertSQL);
        $stmt->bindValue(":order_id", $product["order_id"]);
        $stmt->bindValue(":name", $product["name"]);
        $stmt->bindValue(":product_id", $product["product_id"]);
        $stmt->bindValue(":quantity", $product["quantity"]);
        $stmt->bindValue(":selling_price", $product["selling_price"]);
        $stmt->bindValue(":tax", $product["tax"]);
        $stmt->bindValue(":special_value", $product["special_value"]);
        $stmt->bindValue(":image_url", $product["image_url"]);
        $stmt->bindValue(":size", $product["size"]);
        $stmt->bindValue(":status", $product["status"]);
        $stmt->bindValue(":color", $product["color"]);
        $stmt->execute();

        return $ctx->db->lastInsertId();
    }

    /**
     * Fetch order info by order ID.
     * @param object $ctx Context.
     * @param string $orderRowID Order row ID.
     * @return array Order info.
     */
    public function getInfoByOrderID($ctx, $orderRowID)
    {
        $selectSQL = "SELECT
                `id`,
                `order_id` AS `order_uuid`,
                `order_date`,
                `order_status`,
                `payment_status`,
                `paymentmode` AS `payment_mode`,
                `paymentref` AS `payment_reference`,
                `total` AS `total_amount`,
                `total_tax`,
                `total_special_fee`,
                `phone` AS `mobile_number`,
                `address`,
                `admin_id`,
                `cashier_id`,
                `created_on`,
                `updated_on`
            FROM `users_orders` WHERE `id` = :id";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":id", $orderRowID, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->format($row);
    }

    /**
     * Fetch order products by order row ID.
     * @param object $ctx Context.
     * @param int $orderRowID Order row ID.
     * @return array Order products.
     */
    public function getOrderProductsByOrderRowID($ctx, $orderRowID)
    {
        $selectSQL = "SELECT
                `op`.`id`,
                `op`.`order_id`,
                `op`.`product_id`,
                `op`.`product_name` AS `name`,
                `op`.`product_image` AS `image_url`,
                `op`.`quantity`,
                -- `pd`.`Stock_Quantity` AS `stock_quantity`,
                `op`.`sellprice` AS `selling_price`,
                `op`.`size`,
                `op`.`Special_Value` AS `special_value`,
                `op`.`status`,
                `op`.`tax`,
                `op`.`color`
            FROM `ordered_product` `op`
            -- LEFT JOIN `product_details` `pd`
            --     ON `pd`.`product_id` = `op`.`product_id`
            WHERE `op`.`order_id` = :orderRowID";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":orderRowID", $orderRowID, PDO::PARAM_INT);
        $stmt->execute();
        $rows =  $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->formatOrderProducts($rows);
    }

    /**
     * Updates order status.
     * @param string $orderID Order ID.
     * @param string $orderStatus Order status.
     * @return int Affected rows.
     */
    public function updateStatus($ctx, $orderRowID, $orderStatus)
    {
        $updateSQL = "UPDATE `users_orders`
            SET
                `order_status` = :order_status,
                `updated_on` = :updated_on
            WHERE `id` = :id LIMIT 1";
        $stmt = $ctx->db->prepare($updateSQL);
        $stmt->bindValue(":order_status", $orderStatus);
        $stmt->bindValue(":id", $orderRowID);
        $stmt->bindValue(":updated_on", $ctx->now);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Check order_uuid as order_id exist in users_orders table.
     * @param object $ctx Context.
     * @param string $order_uuid order unique id.
     * @return bool Status.
    */
    public function orderUUIDExists($ctx, $order_uuid): bool
    {
        $selectSQL = "SELECT EXISTS(
                SELECT * FROM `users_orders` 
                WHERE order_id = :order_uuid
            ) AS `order_uuid_found`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":order_uuid", $order_uuid, PDO:: PARAM_STR);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Check order_id as order_id exist in users_orders table.
     * @param object $ctx Context.
     * @param int $order_id order.
     * @return bool Status.
    */
    public function orderIDExists($ctx, $order_id)
    {
        $selectSQL = "SELECT `id` FROM `users_orders` 
                WHERE order_id = :order_id";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":order_id", $order_id, PDO:: PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetch order products Sold out Quantity by product ID.
     * @param object $ctx Context.
     * @param int $productId.
     * @return soldout quantity of product.
     */
    public function getSoldOutQty($ctx, $productId)
    {
        $selectSQL = "SELECT
                 SUM(`op`.quantity) AS `soldout_quantity`
            FROM `ordered_product` `op`
            
            WHERE `op`.`product_id` = :product_id GROUP BY `op`.`product_id`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":product_id", $productId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Checks if Product exists in product_details table.
     * @param object $ctx Context.
     * @param string $product_id.
     * @return bool Exists or not.
     */
    public function productExists($ctx, $productId)
    {
        $selectSQL = "SELECT `id`,`Stock_Quantity` FROM `product_details`
                WHERE product_id = :product_id AND `storeadmin_id` = :store_admin_id
            ";
        $stmt= $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":product_id", $productId, PDO:: PARAM_INT);
        $stmt->bindValue(":store_admin_id", $ctx->tokenData->store_admin_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

     /**
     * Checks if Product exists in products_temp table.
     * @param object $ctx Context.
     * @param string $product_id.
     * @return bool Exists or not.
     */
    public function tempProductExists($ctx, $productId)
    {
        $selectSQL = "SELECT `id`,`stock_quantity` FROM `products_temp`
                WHERE product_id = :product_id AND `storeadmin_id` = :store_admin_id";
        $stmt= $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":product_id", $productId, PDO:: PARAM_INT);
        $stmt->bindValue(":store_admin_id", $ctx->tokenData->store_admin_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Deduct stock_quantity at transaction/order create time from product details.
     * @param object $ctx Context.
     * @param int $productID Product ID. e.g 100034556
     * @param int $qty Quantity.
     * @return int Number of updated rows.
     */
    public function deductQuantity($ctx, $data)
    {
        $updateSQL = "UPDATE `product_details`
            SET `Stock_Quantity` = :stock_quantity,
                `updated_on` = :updatedOn
            WHERE `product_id` = :productID
            AND `storeadmin_id` = :adminID";

        $stmt = $ctx->db->prepare($updateSQL);
        $stmt->bindValue(":stock_quantity", $data["stock_quantity"]);
        $stmt->bindValue(":updatedOn", $ctx->now);
        $stmt->bindValue(":adminID", $ctx->tokenData->store_admin_id);
        $stmt->bindValue(":productID", $data["product_id"]);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Deduct stock_quantity at transaction/order create time from temp_product.
     * @param object $ctx Context.
     * @param int $productID Product ID. e.g 100034556
     * @param int $qty Quantity.
     * @return int Number of updated rows.
     */
    public function deductTempQuantity($ctx, $data)
    {
        $updateSQL = "UPDATE `products_temp`
            SET `stock_quantity` = :stock_quantity,
                `modified_on` = :updatedOn
            WHERE `product_id` = :productID
            AND `storeadmin_id` = :adminID";

        $stmt = $ctx->db->prepare($updateSQL);
        $stmt->bindValue(":stock_quantity", $data["stock_quantity"]);
        $stmt->bindValue(":updatedOn", $ctx->now);
        $stmt->bindValue(":adminID", $ctx->tokenData->store_admin_id);
        $stmt->bindValue(":productID", $data["product_id"]);
        $stmt->execute();

        return $stmt->rowCount();
    }

}
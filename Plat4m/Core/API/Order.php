<?php

namespace Plat4m\Core\API;

use PDOException;
use Exception;
use Plat4m\Utilities\Logger;

class Order
{
    // DB connection object.
    private $db;

    /**
     * Connects to DB on invoke.
     * @param object $db PDO.
     */
    private $orderInfo = [
        "orderID"           => NULL,
        "uid"               => NULL,
        "productQuantites"  => [],
        "products"          => [],
        "latitude"          => NULL,
        "longitude"         => NULL,
        "localtime"         => NULL,
    ];

    /**
     * Connects to DB on invoke.
     */
    public function __construct($db)
    {
        if (!$db) {
            $error = "Requires DB connection";
            Logger::errorMsg($error);
            throw new Exception($error, 500);
        }

        $this->db = $db;
    }

    /**
     * Sets order info.
     * @param array $info Order info.
     * @return object Current object.
     */
    public function setInfo($info)
    {
        $this->orderInfo = $info;
        return $this;
    }

    /**
     * Creates product.
     * @return int Last insert ID.
     */
    public function create()
    {
        try {
            $this->db->beginTransaction(); // Begin transaction.

            // Insert order.
            $orderInsertID = $this->insertOrderRecord(
                $this->orderInfo["orderID"],
                $this->orderInfo["uid"],
                (float) $this->orderInfo["totalAmount"],
                $this->orderInfo["paymentMode"],
                $this->orderInfo["latitude"],
                $this->orderInfo["longitude"],
                $this->orderInfo["localtime"],
                $this->orderInfo["weather"]
            );

            if (!$orderInsertID) {
                throw new Exception("Failed to create order", 500);
            }

            // Insert all ordered products.
            foreach ($this->orderInfo["productsFromReq"] as $productFromReq) {
                if ($productFromReq["product_id"] != 1) {
                    foreach ($this->orderInfo["productsFromDB"] as $productFromDB) {
                        if ((int)$productFromReq["product_id"] == (int)$productFromDB["product_id"]) {
                            $inserted = $this->insertOrderedProduct(
                                $orderInsertID,
                                [
                                    "product_name"  => $productFromDB["product_name"],
                                    "product_id"    => $productFromDB["product_id"],
                                    "quantity"      => $productFromReq["quantity"],
                                    "price"         => $productFromReq["price"],
                                    "tax"           => $productFromReq["tax"],
                                    "Special_Value" => $productFromReq["Special_Value"]
                                ]
                            );

                            if (!$inserted) {
                                throw new Exception("Failed to create order", 500);
                            }
                        }
                    }
                } else {
                    $inserted = $this->insertOrderedProduct(
                        $orderInsertID,
                        [
                            "product_name"  => 'Unknown_Product',
                            "product_id"    => 0,
                            "quantity"      => $productFromReq["quantity"],
                            "price"         => $productFromReq["price"],
                            "tax"           => $productFromReq["tax"],
                            "Special_Value" => $productFromReq["Special_Value"]
                        ]
                    );

                    if (!$inserted) {
                        throw new Exception("Failed to create order", 500);
                    }
                }
            }

            return $this->db->commit(); // Commit transaction.
        } catch (Exception $ex) {
            $this->db->rollBack(); // Rollback transaction.
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Inserts order record.
     * @param string $orderID Order ID.
     * @param float $totalPrice Total price.
     * @return int Last insert ID.
     */
    private function insertOrderRecord(
        $orderID,
        $uid,
        $totalPrice,
        $paymentMode,
        $latitude,
        $longitude,
        $localtime,
        $weather
    ) {
        try {
            $paymentStatus = "Completed";
            $paymentRef = "";
            $address = "";
            $phone = "";
            $order_date = date("Y-m-d H:i:s");
            $insertSQL = "INSERT INTO `users_orders` (
                    `order_id`, `total`, `order_status`, `paymentref`,
                    `paymentmode`, `payment_status`, `address`, `phone`,
                    `uid`, `order_date`, `latitude`, `longitude`, `localtime`,
                    `weather`
                ) VALUES (
                    :orderID, :totalPrice, :orderStatus, :paymentRef,
                    :paymentMode, :paymentStatus, :address, :phone,
                    :uid, :order_date, :latitude, :longitude, :localtime,
                    :weather
                )";
            $stmt = $this->db->prepare($insertSQL);
            $stmt->bindValue(":orderID", $orderID);
            $stmt->bindValue(":totalPrice", $totalPrice);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED);
            $stmt->bindValue(":paymentRef", $paymentRef);
            $stmt->bindValue(":paymentMode", $paymentMode);
            $stmt->bindValue(":paymentStatus", $paymentStatus);
            $stmt->bindValue(":address", $address);
            $stmt->bindValue(":phone", $phone);
            $stmt->bindValue(":uid", $uid);
            $stmt->bindValue(":order_date", $order_date);
            $stmt->bindValue(":latitude", $latitude);
            $stmt->bindValue(":longitude", $longitude);
            $stmt->bindValue(":localtime", $localtime);
            $stmt->bindValue(":weather", $weather);
            $stmt->execute();

            return $this->db->lastInsertId();
        } catch (PDOException $ex) {
            Logger::errExcept($ex);
            throw new Exception("Failed to create order", 500);
        }
    }

    /**
     * Inserts ordered product.
     * @param string $orderID Order ID.
     * @param array $productInfo Product info.
     * @return int Last insert ID.
     */
    private function insertOrderedProduct($orderID, $productInfo)
    {
        try {
            $tax = (float) $productInfo["tax"];
            $productImage = "";
            $size = "";
            $status = "";
            $color = "";
            $insertSQL = "INSERT INTO `ordered_product`
                (`order_id`, `product_name`, `product_id`, `quantity`, `sellprice`, `tax`, `Special_Value`, `product_image`, `size`, `status`, `color`)
                VALUES
                (:orderID, :productName, :productID, :quantity, :sellprice, :tax, :specialValue, :productImage, :size, :status, :color)";
            $stmt = $this->db->prepare($insertSQL);
            $stmt->bindValue(":orderID", $orderID);
            $stmt->bindValue(":productName", $productInfo["product_name"]);
            $stmt->bindValue(":productID", $productInfo["product_id"]);
            $stmt->bindValue(":quantity", $productInfo["quantity"]);
            $stmt->bindValue(":sellprice", $productInfo["price"]);
            $stmt->bindValue(":tax", $tax);
            $stmt->bindValue(":specialValue", $productInfo["Special_Value"]);
            $stmt->bindValue(":productImage", $productImage);
            $stmt->bindValue(":size", $size);
            $stmt->bindValue(":status", $status);
            $stmt->bindValue(":color", $color);
            $stmt->execute();

            return $this->db->lastInsertId();
        } catch (PDOException $ex) {
            Logger::errExcept($ex);
            throw new Exception("Failed to create order", 500);
        }
    }

    /**
     * Fetches order status.
     * @param string $orderID Order ID.
     * @return string Order status.
     */
    public function getStatus($orderID)
    {
        try {
            $selectSQL = "SELECT order_status FROM users_orders
                WHERE order_id = :orderID LIMIT 1";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":orderID", $orderID);
            $stmt->execute();

            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            Logger::errExcept($ex);
            throw new Exception("Failed to create order", 500);
        }
    }

    /**
     * Updates order status.
     * @param string $orderID Order ID.
     * @param string $orderStatus Order status.
     * @return int Affected rows.
     */
    public function updateStatus($orderID, $orderStatus)
    {
        try {
            $updateSQL = "UPDATE `users_orders`
                SET `order_status` = :orderStatus
                WHERE `order_id` = :orderID LIMIT 1";
            $stmt = $this->db->prepare($updateSQL);
            $stmt->bindValue(":orderStatus", $orderStatus);
            $stmt->bindValue(":orderID", $orderID);
            $stmt->execute();

            return $stmt->rowCount();
        } catch (PDOException $ex) {
            Logger::errExcept($ex);
            throw new Exception("Failed to create order", 500);
        }
    }
}

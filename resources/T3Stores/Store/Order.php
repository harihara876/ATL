<?php

namespace T3Stores\Store;

use \PDO;
use \PDOException;
use \Exception;

class Order
{
    // DB connection object.
    private $db;

    // Order info.
    private $orderInfo = [
        "orderID"           => NULL,
        "uid"               => NULL,
        "productQuantites"  => [],
        "products"          => []
    ];



    /**
     * Connects to DB on invoke.
     */
    public function __construct($db)
    {
        if (!$db) {
            throw new Exception("Requires DB connection");
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
        // TODO: Validate
        $this->orderInfo = $info;
        return $this;
    }



    /**
     * Creates product.
     * @return int Last insert ID.
     */
    public function create()
    {
        // $totalPrice = 0;

        // // Calculate total amount.
        // foreach ($this->orderInfo["productsFromReq"] as $product) {
        //     $taxAmount = $product["price"] * (float) $product["tax"];
        //     $totalPrice += $product["quantity"] * ($product["price"] + $taxAmount + (float) $product["Special_Value"]);
        // }

        // $totalPrice = round($totalPrice, 2);

        try {
            // Create a transaction.
            $this->db->beginTransaction();

            // Insert order.
            $orderInsertID = $this->insertOrderRecord(
                $this->orderInfo["orderID"],
                $this->orderInfo["uid"],
                (float) $this->orderInfo["totalAmount"],
                $this->orderInfo["paymentMode"]
            );
            if (!$orderInsertID) {
                throw new Exception("Failed to create order");
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
                                throw new Exception("Failed to create order");
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
                        throw new Exception("Failed to create order");
                    }
                }
            }

            // Commit transaction.
            return $this->db->commit();
        } catch (Exception $ex) {
            // Rollback transaction.
            $this->db->rollBack();
            throw new Exception($ex->getMessage());
        }
    }



    /**
     * Inserts order record.
     * @param string $orderID Order ID.
     * @param float $totalPrice Total price.
     * @return int Last insert ID.
     */
    private function insertOrderRecord($orderID, $uid, $totalPrice, $paymentMode)
    {
        try {
            $orderStatus = "Complete";
            $paymentStatus = "Completed";
            $order_date = date("Y-m-d H:i:s");
            $insertSQL = "INSERT INTO `users_orders`
                    (`order_id`, `total`, `order_status`, `paymentmode`, `payment_status`, `uid`, `order_date`)
                VALUES (:orderID, :totalPrice, :orderStatus, :paymentMode, :paymentStatus, :uid, :order_date)";
            $stmt = $this->db->prepare($insertSQL);
            $stmt->bindParam(":orderID", $orderID);
            $stmt->bindParam(":totalPrice", $totalPrice);
            $stmt->bindParam(":orderStatus", $orderStatus);
            $stmt->bindParam(":paymentMode", $paymentMode);
            $stmt->bindParam(":paymentStatus", $paymentStatus);
            $stmt->bindParam(":uid", $uid);
            $stmt->bindParam(":order_date", $order_date);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $ex) {
            echo "Hello";
            throw new Exception("Failed to create order");
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
            $insertSQL = "INSERT INTO `ordered_product`
                (`order_id`, `product_name`, `product_id`, `quantity`, `sellprice`, `tax`,`Special_Value`)
                VALUES
                (:orderID, :productName, :productID, :quantity, :sellprice, :tax, :specialValue)";
            $stmt = $this->db->prepare($insertSQL);
            $stmt->bindParam(":orderID", $orderID);
            $stmt->bindParam(":productName", $productInfo["product_name"]);
            $stmt->bindParam(":productID", $productInfo["product_id"]);
            $stmt->bindParam(":quantity", $productInfo["quantity"]);
            $stmt->bindParam(":sellprice", $productInfo["price"]);
            $stmt->bindParam(":tax", $tax);
            $stmt->bindParam(":specialValue", $productInfo["Special_Value"]);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $ex) {
            throw new Exception("Failed to create order");
        }
    }
}

<?php

namespace Plat4m\Core\API;

use Exception;
use PDO;
use PDOException;

class Transaction
{
    // DB connection object.
    private $db;

    /**
     * Connects to DB on invoke.
     * @param object $db PDO.
     */
    public function __construct($db)
    {
        if (!$db) {
            throw new Exception("Requires DB connection");
        }

        $this->db = $db;
    }

    /**
     * Fetch order info by order ID.
     * @param string $orderID Order ID.
     * @return array Order info.
     * @throws Exception
     */
    public function getInfoByOrderID($orderID)
    {
        try {
            $selectSQL = "SELECT
                    `id`,
                    `order_id`,
                    `order_date`,
                    `order_status`,
                    `payment_status`,
                    `paymentmode` AS `payment_mode`,
                    `paymentref` AS `payment_ref`,
                    `total`,
                    `phone`,
                    `address`,
                    `uid`
                FROM users_orders WHERE order_id = :orderID";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":orderID", $orderID, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetch order products by order row ID.
     * @param int $orderRowID Order row ID.
     * @return array Order products.
     * @throws Exception
     */
    public function getOrderProductsByOrderRowID($orderRowID)
    {
        try {
            $selectSQL = "SELECT
                    `id`,
                    `order_id` AS `order_row_id`,
                    `product_id`,
                    `product_name`,
                    `product_image`,
                    `quantity`,
                    `sellprice` AS `sell_price`,
                    `size`,
                    `Special_Value` AS `special_value`,
                    `status`,
                    `tax`,
                    `color`,
                    `transaction_time`
                FROM ordered_product WHERE order_id = :orderRowID";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":orderRowID", $orderRowID, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }
}
<?php

namespace Plat4m\Core\API;

use Exception;
use PDO;
use PDOException;

class Reports
{
    // DB connection object.
    private $db;

    // Store admin ID.
    private $storeAdminID = NULL;

    // From date.
    private $fromDate = NULL;

    // To date.
    private $toDate = NULL;

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
     * Set store admin ID.
     * @param int $storeAdminID Store admin ID.
     * @return object Current object.
     */
    public function setStoreAdminID($storeAdminID)
    {
        if ($storeAdminID === NULL) {
            throw new Exception("Store admin ID is required", 400);
        } elseif (!is_int($storeAdminID)) {
            throw new Exception("Store admin ID must be an integer", 400);
        }

        $this->storeAdminID = $storeAdminID;
        return $this;
    }

    /**
     * Set from date.
     * @param string $fromDate From date.
     * @return object Current object.
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;
        return $this;
    }

    /**
     * Set to date.
     * @param string $toDate To date.
     * @return object Current object.
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;
        return $this;
    }

    /**
     * Fetch count of orders.
     * @param string $orderStatus Order status.
     * @return int Orders count.
     */
    public function ordersCount($orderStatus)
    {
        try {
            $selectSQL = "SELECT COUNT(`users_orders`.`id`)
                FROM `users_orders`
                WHERE `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `users_orders`.`order_status`= :orderStatus";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":orderStatus", $orderStatus, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetch count of orders by payment mode.
     * @param string $paymentMode Order payment mode.
     * @return int Orders count.
     */
    public function ordersCountByPaymentMode($paymentMode)
    {
        try {
            $selectSQL = "SELECT COUNT(`users_orders`.`id`)
                FROM `users_orders`
                WHERE `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `users_orders`.`paymentmode`= :paymentMode";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":paymentMode", $paymentMode, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetch count of orders in a period.
     * @param string $orderStatus Order status.
     * @return int Orders count.
     */
    public function ordersCountInPeriod($orderStatus)
    {
        try {
            $selectSQL = "SELECT COUNT(`users_orders`.`id`)
                FROM `users_orders`
                WHERE `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `users_orders`.`order_status`= :orderStatus
                AND `users_orders`.`order_date` BETWEEN :fromDate AND :toDate";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":orderStatus", $orderStatus, PDO::PARAM_STR);
            $stmt->bindValue(":fromDate", $this->fromDate, PDO::PARAM_STR);
            $stmt->bindValue(":toDate", $this->toDate, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetch count of orders by payment mode in a period.
     * @param string $paymentMode Order payment mode.
     * @return int Orders count.
     */
    public function ordersCountInPeriodByPaymentMode($paymentMode)
    {
        try {
            $selectSQL = "SELECT COUNT(`users_orders`.`id`)
                FROM `users_orders`
                WHERE `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `users_orders`.`paymentmode`= :paymentMode
                AND `users_orders`.`order_date` BETWEEN :fromDate AND :toDate";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":paymentMode", $paymentMode, PDO::PARAM_STR);
            $stmt->bindValue(":fromDate", $this->fromDate, PDO::PARAM_STR);
            $stmt->bindValue(":toDate", $this->toDate, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetch orders in a period.
     * @return array Orders.
     */
    public function ordersInPeriod()
    {
        try {
            $selectSQL = "SELECT
                    `order_id` AS id,
                    `total` AS amount,
                    `order_status` AS status,
                    `order_date` AS tms,
                    `uid`,
                    `paymentmode` AS `payment_mode`
                FROM `users_orders`
                WHERE `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `order_date` BETWEEN :fromDate AND :toDate
                ORDER BY `order_date`";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":fromDate", $this->fromDate, PDO::PARAM_STR);
            $stmt->bindValue(":toDate", $this->toDate, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches total revenue.
     * Total revenue = sum of the amount of completed orders.
     * @return float Total revenue.
     */
    public function totalRevenue()
    {
        try {
            $selectSQL = "SELECT SUM(`users_orders`.`total`)
                FROM `users_orders`
                WHERE `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `users_orders`.`order_status` = :orderStatus";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches total revenue by payment mode.
     * Total revenue = sum of the amount of completed orders.
     * @param string $paymentMode Payment mode.
     * @return float Total revenue.
     */
    public function totalRevenueByPaymentMode($paymentMode)
    {
        try {
            $selectSQL = "SELECT SUM(`users_orders`.`total`)
                FROM `users_orders`
                WHERE `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `users_orders`.`order_status` = :orderStatus
                AND `users_orders`.`paymentmode`= :paymentMode";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->bindValue(":paymentMode", $paymentMode, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches total revenue in a period.
     * Total revenue = sum of the amount of completed orders.
     * @return float Total revenue.
     */
    public function totalRevenueInPeriod()
    {
        try {
            $selectSQL = "SELECT SUM(`users_orders`.`total`)
                FROM `users_orders`
                WHERE `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `users_orders`.`order_status` = :orderStatus
                AND `users_orders`.`order_date` BETWEEN :fromDate AND :toDate";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->bindValue(":fromDate", $this->fromDate, PDO::PARAM_STR);
            $stmt->bindValue(":toDate", $this->toDate, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches total revenuevby payment mode in a period.
     * Total revenue = sum of the amount of completed orders.
     * @param string $paymentMode Payment mode.
     * @return float Total revenue.
     */
    public function totalRevenueByPaymentModeInPeriod($paymentMode)
    {
        try {
            $selectSQL = "SELECT SUM(`users_orders`.`total`)
                FROM `users_orders`
                WHERE `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `users_orders`.`order_status` = :orderStatus
                AND `users_orders`.`order_date` BETWEEN :fromDate AND :toDate
                AND `users_orders`.`paymentmode`= :paymentMode";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->bindValue(":fromDate", $this->fromDate, PDO::PARAM_STR);
            $stmt->bindValue(":toDate", $this->toDate, PDO::PARAM_STR);
            $stmt->bindValue(":paymentMode", $paymentMode, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetch total selling price and tax.
     * @return object Object Total selling price and tax.
     */
    public function totalSellingPriceAndTax()
    {
        try {
            $selectSQL = "SELECT
                    SUM(`sellprice` * `quantity`) as `sellingPrice`,
                    SUM((`sellprice` * `tax`) * `quantity`) as `tax`
                FROM `ordered_product`
                WHERE `order_id` IN (
                    SELECT `id` FROM `users_orders` WHERE `uid` IN (
                        SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                        UNION
                        SELECT :storeAdminID2
                    ) AND `order_status` = :orderStatus
                )";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetch total selling price and tax in a period.
     * @return object Object Total selling price and tax.
     */
    public function totalSellingPriceAndTaxInPeriod()
    {
        try {
            $selectSQL = "SELECT
                    SUM(`sellprice` * `quantity`) as `sellingPrice`,
                    SUM((`sellprice` * `tax`) * `quantity`) as `tax`
                FROM `ordered_product`
                WHERE `order_id` IN (
                    SELECT `id` FROM `users_orders` WHERE `uid` IN (
                        SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                        UNION
                        SELECT :storeAdminID2
                    ) AND `order_status` = :orderStatus
                    AND `order_date` BETWEEN :fromDate AND :toDate
                )";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->bindValue(":fromDate", $this->fromDate, PDO::PARAM_STR);
            $stmt->bindValue(":toDate", $this->toDate, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches total revenue by category name.
     * @param string $categoryName Category name.
     * @return float Total revenue.
     */
    public function totalRevenueByCategoryName($categoryName)
    {
        try {
            $selectSQL = "SELECT SUM(`total`)
                FROM `users_orders`
                WHERE `id` IN (
                    SELECT `order_id`
                    FROM `ordered_product`
                    WHERE `product_id` IN (
                        SELECT `products`.`product_id`
                        FROM `category`
                        JOIN `products` ON `category`.`cat_id` = `products`.`cat_id`
                        WHERE `category`.`category_name` = :categoryName
                    )
                ) AND `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `order_status` = :orderStatus";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":categoryName", $categoryName, PDO::PARAM_STR);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches total revenue by category name in a period.
     * @param string $categoryName Category name.
     * @return float Total revenue.
     */
    public function totalRevenueByCategoryNameInPeriod($categoryName)
    {
        try {
            $selectSQL = "SELECT SUM(`total`)
                FROM `users_orders`
                WHERE `id` IN (
                    SELECT `order_id`
                    FROM `ordered_product`
                    WHERE `product_id` IN (
                        SELECT `products`.`product_id`
                        FROM `category`
                        JOIN `products` ON `category`.`cat_id` = `products`.`cat_id`
                        WHERE `category`.`category_name` = :categoryName
                    )
                ) AND `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `order_status` = :orderStatus
                AND `order_date` BETWEEN :fromDate AND :toDate";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":categoryName", $categoryName, PDO::PARAM_STR);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->bindValue(":fromDate", $this->fromDate, PDO::PARAM_STR);
            $stmt->bindValue(":toDate", $this->toDate, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches total revenue by subcategory name.
     * @param string $subcategoryName Subcategory name.
     * @return float Total revenue.
     */
    public function totalRevenueBySubcategoryName($subcategoryName)
    {
        try {
            $selectSQL = "SELECT SUM(`total`)
                FROM `users_orders`
                WHERE `id` IN (
                    SELECT `order_id`
                    FROM `ordered_product`
                    WHERE `product_id` IN (
                        SELECT `products`.`product_id`
                        FROM `subcategories`
                        JOIN `products` ON `subcategories`.`Sub_Category_Id` = `products`.`Category_Type`
                        WHERE `subcategories`.`Sub_Category_Name` = :subcategoryName
                    )
                ) AND `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `order_status` = :orderStatus";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":subcategoryName", $subcategoryName, PDO::PARAM_STR);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches total revenue by subcategory name in a period.
     * @param string $subcategoryName Subategory name.
     * @return float Total revenue.
     */
    public function totalRevenueBySubcategoryNameInPeriod($subcategoryName)
    {
        try {
            $selectSQL = "SELECT SUM(`total`)
                FROM `users_orders`
                WHERE `id` IN (
                    SELECT `order_id`
                    FROM `ordered_product`
                    WHERE `product_id` IN (
                        SELECT `products`.`product_id`
                        FROM `subcategories`
                        JOIN `products` ON `subcategories`.`Sub_Category_Id` = `products`.`Category_Type`
                        WHERE `subcategories`.`Sub_Category_Name` = :subcategoryName
                    )
                ) AND `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `order_status` = :orderStatus
                AND `order_date` BETWEEN :fromDate AND :toDate";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":subcategoryName", $subcategoryName, PDO::PARAM_STR);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->bindValue(":fromDate", $this->fromDate, PDO::PARAM_STR);
            $stmt->bindValue(":toDate", $this->toDate, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches total revenue of Scratchers (Sub cat) and Games (Product).
     * @return float Total revenue.
     */
    public function totalRevenueOfScratchersGame()
    {
        try {
            $selectSQL = "SELECT
                    SUM(`sellprice` * `quantity`) as `total`
                FROM `ordered_product`
                JOIN `users_orders` ON `users_orders`.`id` = `ordered_product`.`order_id`
                WHERE `ordered_product`.`product_name` REGEXP 'Game*'
                AND `users_orders`.`uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `users_orders`.`order_status` = :orderStatus";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches total revenue of Scratchers (Sub cat) and Games (Product) in a period.
     * @return float Total revenue.
     */
    public function totalRevenueOfScratchersGameInPeriod()
    {
        try {
            $selectSQL = "SELECT
                    SUM(`sellprice` * `quantity`) as `total`
                FROM `ordered_product`
                JOIN `users_orders` ON `users_orders`.`id` = `ordered_product`.`order_id`
                WHERE `ordered_product`.`product_name` REGEXP 'Game*'
                AND `users_orders`.`uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `users_orders`.`order_status` = :orderStatus
                AND `users_orders`.`order_date` BETWEEN :fromDate AND :toDate";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->bindValue(":fromDate", $this->fromDate, PDO::PARAM_STR);
            $stmt->bindValue(":toDate", $this->toDate, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches total revenue by a product name.
     * @param string $productName Product name.
     * @return float Total revenue.
     */
    public function totalRevenueByProduct($productName)
    {
        try {
            $selectSQL = "SELECT SUM(`total`)
                FROM `users_orders`
                WHERE `id` IN (
                    SELECT `order_id`
                    FROM `ordered_product`
                    WHERE `product_id` IN (
                        SELECT `product_id`
                        FROM `products`
                        WHERE `products`.`product_name` = :productName
                    )
                ) AND `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `order_status` = :orderStatus";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":productName", $productName, PDO::PARAM_STR);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches total revenue by a product name in a period.
     * @param string $productName Product name.
     * @return float Total revenue.
     */
    public function totalRevenueByProductInPeriod($productName)
    {
        try {
            $selectSQL = "SELECT SUM(`total`)
                FROM `users_orders`
                WHERE `id` IN (
                    SELECT `order_id`
                    FROM `ordered_product`
                    WHERE `product_id` IN (
                        SELECT `product_id`
                        FROM `products`
                        WHERE `products`.`product_name` = :productName
                    )
                ) AND `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `order_status` = :orderStatus
                AND `order_date` BETWEEN :fromDate AND :toDate";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":productName", $productName, PDO::PARAM_STR);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->bindValue(":fromDate", $this->fromDate, PDO::PARAM_STR);
            $stmt->bindValue(":toDate", $this->toDate, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches total revenue by a product name "Lotto".
     * @return float Total revenue.
     */
    public function totalRevenueByProductLotto()
    {
        try {
            $selectSQL = "SELECT
                    SUM(`sellprice` * `quantity`) as `total`
                FROM `ordered_product`
                JOIN `users_orders` ON `users_orders`.`id` = `ordered_product`.`order_id`
                WHERE `ordered_product`.`product_name` = 'Lotto'
                AND `users_orders`.`uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `users_orders`.`order_status` = :orderStatus";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches total revenue by a product name "Lotto" in a period.
     * @return float Total revenue.
     */
    public function totalRevenueByProductLottoInPeriod()
    {
        try {
            $selectSQL = "SELECT
                    SUM(`sellprice` * `quantity`) as `total`
                FROM `ordered_product`
                JOIN `users_orders` ON `users_orders`.`id` = `ordered_product`.`order_id`
                WHERE `ordered_product`.`product_name` = 'Lotto'
                AND `users_orders`.`uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `users_orders`.`order_status` = :orderStatus
                AND `users_orders`.`order_date` BETWEEN :fromDate AND :toDate";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->bindValue(":fromDate", $this->fromDate, PDO::PARAM_STR);
            $stmt->bindValue(":toDate", $this->toDate, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches product sales.
     * @return array Product sales info.
     * @throws Exception
     */
    public function productSales()
    {
        try {
            $selectSQL = "SELECT
                    `product_name`,
                    SUM(`quantity`) AS `quantity`,
                    FORMAT(SUM(`sellprice`), 2) AS `selling_price`
                FROM ordered_product
                WHERE `order_id` IN (
                    SELECT `id` FROM `users_orders` WHERE `uid` IN (
                        SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                        UNION
                        SELECT :storeAdminID2
                    ) AND `order_status` = :orderStatus
                )
                GROUP BY `product_name`
                ORDER BY `quantity` DESC, `product_name` ASC";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches product sales in a period.
     * @return array Product sales info.
     * @throws Exception
     */
    public function productSalesInPeriod()
    {
        try {
            $selectSQL = "SELECT
                    `product_name`,
                    SUM(`quantity`) AS `quantity`,
                    FORMAT(SUM(`sellprice`), 2) AS `selling_price`
                FROM ordered_product
                WHERE `order_id` IN (
                    SELECT `id` FROM `users_orders` WHERE `uid` IN (
                        SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                        UNION
                        SELECT :storeAdminID2
                    ) AND `order_status` = :orderStatus
                    AND `order_date` BETWEEN :fromDate AND :toDate
                )
                GROUP BY `product_name`
                ORDER BY `quantity` DESC, `product_name` ASC";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID1", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":storeAdminID2", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
            $stmt->bindValue(":fromDate", $this->fromDate, PDO::PARAM_STR);
            $stmt->bindValue(":toDate", $this->toDate, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }
}

<?php

namespace Plat4m\Core\API;

use Exception;
use PDO;
use PDOException;

class Product
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
            throw new Exception("Requires DB connection", 500);
        }

        $this->db = $db;
    }

    /**
     * Fetch product info by UPC.
     * @param string $upc UPC.
     * @return array Product info.
     * @throws Exception
     */
    public function getInfoByUPC($upc)
    {
        try {
            $selectSQL = "SELECT `products`.*, `price`, `description`
                FROM `products`
                JOIN `product_details` ON `product_details`.`product_id` = `products`.`product_id`
                WHERE `products`.`UPC` = :UPC
                ORDER BY `product_details`.`id` DESC
                LIMIT 1";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":UPC", $upc, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetch product info by UPC.
     * @param string $upc UPC.
     * @return array Product info.
     * @throws Exception
     */
    public function getFullInfoByUPC($upc)
    {
        try {
            $selectSQL = "SELECT
                    `products`.`id`,
                    `products`.`product_id`,
                    `products`.`product_name`,
                    `products`.`cat_id`,
                    `products`.`POS_description`,
                    `products`.`UPC`,
                    `products`.`Category_Id`,
                    `products`.`Category_Type`,
                    `products`.`Date_Created`,
                    `products`.`Image`,
                    `products`.`Manufacturer`,
                    `products`.`Brand`,
                    `products`.`Vendor`,
                    `products`.`status`,
                    `products`.`created_time`,
                    `product_details`.`id`,
                    `product_details`.`product_id` AS `store_product_id`,
                    `product_details`.`description`,
                    `product_details`.`POS_description` AS `store_pos_description`,
                    `product_details`.`price`,
                    `product_details`.`sellprice`,
                    `product_details`.`color`,
                    `product_details`.`size`,
                    `product_details`.`product_status`,
                    `product_details`.`quantity`,
                    `product_details`.`plimit`,
                    `product_details`.`Regular_Price`,
                    `product_details`.`Buying_Price`,
                    `product_details`.`Tax_Status`,
                    `product_details`.`Tax_Value`,
                    `product_details`.`Special_Value`,
                    `product_details`.`Date_Created` AS `store_date_created`,
                    `product_details`.`SKU`,
                    `product_details`.`Stock_Quantity`,
                    `product_details`.`ProductMode`,
                    `product_details`.`Age_Restriction`,
                    `product_details`.`sale_type`,
                    `product_details`.`status` AS `store_status`,
                    `product_details`.`storeadmin_id`,
                    `product_details`.`created_date_on`
                FROM `products`
                JOIN `product_details` ON `product_details`.`product_id` = `products`.`product_id`
                WHERE `products`.`UPC` = :UPC
                ORDER BY `product_details`.`id` DESC
                LIMIT 1";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":UPC", $upc, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches all products by store admin ID.
     * @param int $storeAdminID Store admin ID.
     * @return array Products.
     * @throws Exception
     */
    public function getAllByStoreAdminID($storeAdminID)
    {
        try {
            $selectSQL = "SELECT *
                FROM `products`
                LEFT JOIN `product_details` ON `product_details`.`product_id` = `products`.`product_id`
                WHERE `product_details`.`storeadmin_id` = :storeAdminID";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID", $storeAdminID, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches multiple products at once by IDs.
     * @param array $ids Product IDs.
     * @return array Products.
     */
    public function getMultipleProductsByIDs($ids)
    {
        try {
            $productIDs = implode(",", $ids);
            $selectSQL = "SELECT `product_id`, `product_name`
                FROM `products`
                WHERE `product_id` IN ({$productIDs})";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Checks if UPC exists in products table.
     * @param string $upc UPC.
     * @return bool Exists or not.
     * @throws Exception
     */
    public function checkIfUPCExists($upc)
    {
        try {
            $selectSQL = "SELECT EXISTS(
                SELECT * FROM `products` WHERE `UPC` = :UPC
            ) AS `upc_found`";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":UPC", $upc, PDO::PARAM_STR);
            $stmt->execute();

            return (bool) $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Fetches selling price range of a product.
     * @param int $productID Product ID.
     * @return array Min & max selling prices.
     * @throws Exception
     */
    public function getSellingPriceRange($productID)
    {
        try {
            $selectSQL = "SELECT
                    MIN(`sellprice`) AS `min_price`,
                    MAX(`sellprice`) AS `max_price`
                FROM `product_details`
                WHERE `product_id` = :productID";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":productID", $productID, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Fetches selling price range of a product across products, products_temp & products_temp2.
     * @param string $upc UPC.
     * @return array Min & max selling prices.
     * @throws Exception
     */
    public function getSellingPriceRangeAcrossAll($upc)
    {
        try {
            $selectSQL = "SELECT
                    MIN(`price`) AS `min_price`,
                    MAX(`price`) AS `max_price`
                FROM
                    (
                        (
                            SELECT `sellprice` AS `price`
                            FROM `products` `p`
                            JOIN `product_details` `pd` ON `pd`.`product_id` = `p`.`product_id`
                            WHERE `p`.`UPC` = :upc1
                        )
                        UNION ALL
                        (
                            SELECT `price`
                            FROM `products_temp`
                            WHERE `upc` = :upc2
                        )
                        UNION ALL
                        (
                            SELECT `price`
                            FROM `products_temp2`
                            WHERE `upc` = :upc3
                        )
                    ) AS `t1`";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":upc1", $upc, PDO::PARAM_STR);
            $stmt->bindValue(":upc2", $upc, PDO::PARAM_STR);
            $stmt->bindValue(":upc3", $upc, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }
}

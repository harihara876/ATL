<?php
namespace Plat4m\Core\API;

use Exception;
use PDO;
use PDOException;

class ProductRepository
{
    // DB connection object.
    private ?PDO $db;

    // Super admin ID.
    private int $superAdminID = 1;

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
     * Checks if UPC exists in products repository.
     * @param string $upc UPC.
     * @return bool Status.
     * @throws Exception
     */
    public function upcExists(string $upc): bool
    {
        try {
            $selectSQL = "SELECT EXISTS(
                SELECT * FROM `products`
                JOIN `product_details`
                    ON `product_details`.`product_id` = `products`.`product_id`
                WHERE `product_details`.`storeadmin_id` = :superAdminID
                AND `products`.`UPC` = :upc
            ) AS `upc_found`";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":superAdminID", $this->superAdminID, PDO::PARAM_INT);
            $stmt->bindValue(":upc", $upc, PDO::PARAM_STR);
            $stmt->execute();

            return (bool) $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Fetches product info by UPC from products repository.
     * @param string $upc UPC.
     * @return array Product info if exists. Else NULL.
     * @throws Exception
     */
    public function getInfoByUPC(string $upc)
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
                    `product_details`.`id` AS `store_product_row_id`,
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
                    `product_details`.`created_date_on`,
                    `product_details`.`discount_percent`,
                    `product_details`.`discount_pretax`,
                    `product_details`.`discount_posttax`
                FROM `products`
                JOIN `product_details`
                    ON `product_details`.`product_id` = `products`.`product_id`
                WHERE `products`.`UPC` = :upc
                AND `product_details`.`storeadmin_id` = :superAdminID
                LIMIT 1";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":upc", $upc, PDO::PARAM_STR);
            $stmt->bindValue(":superAdminID", $this->superAdminID, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function create($info)
    {
        try {
            $insertSQL = "INSERT INTO `products` (
                `product_id`,
                `product_name`,
                `cat_id`,
                `UPC`,
                `Category_Id`,
                `Category_Type`,
                `Date_Created`,
                `Image`,
                `Manufacturer`,
                `Brand`,
                `Vendor`,
                `status`
            ) VALUES (
                :productID,
                :productName,
                :category,
                :upc,
                :categoryID,
                :subcategoryID,
                :createdAt,
                :image,
                :manufacturer,
                :brand,
                :vendor,
                :status
            )";
            $stmt = $this->db->prepare($insertSQL);
            $stmt->bindValue(":productID", $info["productID"], PDO::PARAM_STR);
            $stmt->bindValue(":productName", $info["productName"], PDO::PARAM_STR);
            $stmt->bindValue(":category", $info["category"], PDO::PARAM_STR);
            $stmt->bindValue(":upc", $info["upc"], PDO::PARAM_STR);
            $stmt->bindValue(":categoryID", $info["categoryID"], PDO::PARAM_STR);
            $stmt->bindValue(":subcategoryID", $info["subcategoryID"], PDO::PARAM_STR);
            $stmt->bindValue(":createdAt", $info["createdAt"], PDO::PARAM_STR);
            $stmt->bindValue(":image", $info["image"], PDO::PARAM_STR);
            $stmt->bindValue(":manufacturer", $info["manufacturer"], PDO::PARAM_STR);
            $stmt->bindValue(":brand", $info["brand"], PDO::PARAM_STR);
            $stmt->bindValue(":vendor", $info["vendor"], PDO::PARAM_STR);
            $stmt->bindValue(":status", $info["status"], PDO::PARAM_STR);
            $stmt->execute();

            $this->db->lastInsertId();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }
}

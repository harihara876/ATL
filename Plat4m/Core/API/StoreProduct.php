<?php

namespace Plat4m\Core\API;

use Exception;
use PDO;
use PDOException;

use Plat4m\Utilities\Logger;

class StoreProduct
{
    // DB connection object.
    private $db;

    // Store admin ID.
    private $storeAdminID = NULL;

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
     * Fetch product info.
     * @param int $productID Product ID.
     * @return array Product info.
     * @throws Exception
     */
    public function getInfo($productID)
    {
        try {
            $selectSQL = "SELECT * FROM `product_details`
                WHERE `product_id` = :productID
                AND `storeadmin_id` = :storeAdminID LIMIT 1";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":productID", $productID);
            $stmt->bindValue(":storeAdminID", $this->storeAdminID);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 400);
        }
    }

    /**
     * Update regular price of a product.
     * @param int $productID Product ID. e.g 100034556
     * @param string $regularPrice Regular Price. e.g $35.50
     * @return int Number of updated rows.
     * @throws Exception
     */
    public function updateRegularPrice(
        $productID,
        $regularPrice,
        $multiItemQuantity,
        $multiItemPrice,
        $discountPercent,
        $discountPretax,
        $discountPosttax
    )
    {
        try {
            $updateSQL = "UPDATE `product_details`
                SET `Regular_Price` = :regularPrice,
                    `multi_item_quantity` = :multiItemQuantity,
                    `multi_item_price` = :multiItemPrice,
                    `discount_percent` = :discount_percent,
                    `discount_pretax` = :discount_pretax,
                    `discount_posttax` = :discount_posttax
                WHERE `product_id` = :productID AND `storeadmin_id` = :storeAdminID";
            $stmt = $this->db->prepare($updateSQL);
            $stmt->bindValue(":regularPrice", $regularPrice);
            $stmt->bindValue(":multiItemQuantity", $multiItemQuantity);
            $stmt->bindValue(":multiItemPrice", $multiItemPrice);
            $stmt->bindValue(":discount_percent", $discountPercent);
            $stmt->bindValue(":discount_pretax", $discountPretax);
            $stmt->bindValue(":discount_posttax", $discountPosttax);
            $stmt->bindValue(":productID", $productID);
            $stmt->bindValue(":storeAdminID", $this->storeAdminID);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $ex) {
            throw new Exception("Failed to update price", 500);
        }
    }

    /**
     * Update description of a product.
     * @param int $productID Product ID. e.g 100034556
     * @param string $description Description.
     * @return int Number of updated rows.
     * @throws Exception
     */
    public function updateDescription($productID, $description)
    {
        try {
            $updateSQL = "UPDATE `product_details`
                SET `description` = :description
                WHERE `product_id` = :productID AND `storeadmin_id` = :storeAdminID";
            $stmt = $this->db->prepare($updateSQL);
            $stmt->bindValue(":description", $description);
            $stmt->bindValue(":productID", $productID);
            $stmt->bindValue(":storeAdminID", $this->storeAdminID);
            $stmt->execute();

            return $stmt->rowCount();
        } catch (PDOException $ex) {
            throw new Exception("Failed to update price", HTTP_STATUS_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Checks if UPC exists in products table by store ID.
     * @param string $upc UPC.
     * @param int $storeAdminID Store admin ID.
     * @return bool Exists or not.
     * @throws Exception
     */
    public function checkIfUPCExists($upc, $storeAdminID)
    {
        try {
            $selectSQL = "SELECT EXISTS(
                SELECT *
                FROM `products` `p`
                LEFT JOIN `product_details` `pd` ON `pd`.`product_id` = `p`.`product_id`
                WHERE `UPC` = :UPC
                AND `storeadmin_id` = :storeAdminID
            ) AS `upc_found`";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":UPC", $upc, PDO::PARAM_STR);
            $stmt->bindValue(":storeAdminID", $storeAdminID, PDO::PARAM_STR);
            $stmt->execute();

            return (bool) $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Fetches product info by UPC from products repository.
     * @param string $upc UPC.
     * @return mixed Product info if exists. Else FALSE.
     * @throws Exception
     */
    public function getInfoByUPC(string $upc): mixed
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
                JOIN `product_details`
                    ON `product_details`.`product_id` = `products`.`product_id`
                WHERE `products`.`UPC` = :upc
                AND `product_details`.`storeadmin_id` = :storeAdminID
                LIMIT 1";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":upc", $upc, PDO::PARAM_STR);
            $stmt->bindValue(":storeAdminID", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Creates product in store products.
     * @param array $info Product info.
     * @return int Row ID.
     * @throws Exception
     */
    public function create(array $info): int
    {
        try {
            $createdOn = date("Y-m-d H:i:s");
            $insertSQL = "INSERT INTO `product_details` (
                `product_id`,
                `description`,
                `POS_description`,
                `price`,
                `sellprice`,
                `color`,
                `size`,
                `product_status`,
                `quantity`,
                `plimit`,
                `Regular_Price`,
                `Buying_Price`,
                `Tax_Status`,
                `Tax_Value`,
                `Special_Value`,
                `Date_Created`,
                `SKU`,
                `Stock_Quantity`,
                `ProductMode`,
                `Age_Restriction`,
                `sale_type`,
                `status`,
                `storeadmin_id`,
                `created_date_on`
                -- `discount_percent`,
                -- `discount_pretax`,
                -- `discount_posttax`
            ) VALUES (
                :productID,
                :description,
                :posDescription,
                :price,
                :sellPrice,
                :color,
                :size,
                :productStatus,
                :quantity,
                :plimit,
                :regularPrice,
                :buyingPrice,
                :taxStatus,
                :taxValue,
                :specialValue,
                :dateCreated,
                :sku,
                :stockQuantity,
                :productMode,
                :ageRestriction,
                :saleType,
                :status,
                :storeAdminID,
                :createdDate
                -- :discount_percent,
                -- :discount_pretax,
                -- :discount_posttax
            )";
            $stmt = $this->db->prepare($insertSQL);
            $stmt->bindValue(":productID", $info["product_id"]);
            $stmt->bindValue(":description", $info["description"]);
            $stmt->bindValue(":posDescription", $info["POS_description"]);
            $stmt->bindValue(":price", $info["price"]);
            $stmt->bindValue(":sellPrice", $info["sellprice"]);
            $stmt->bindValue(":color", $info["color"]);
            $stmt->bindValue(":size", $info["size"]);
            $stmt->bindValue(":productStatus", $info["product_status"]);
            $stmt->bindValue(":quantity", $info["quantity"]);
            $stmt->bindValue(":plimit", $info["plimit"]);
            $stmt->bindValue(":regularPrice", $info["Regular_Price"]);
            $stmt->bindValue(":buyingPrice", $info["Buying_Price"]);
            $stmt->bindValue(":taxStatus", $info["Tax_Status"]);
            $stmt->bindValue(":taxValue", $info["Tax_Value"]);
            $stmt->bindValue(":specialValue", $info["Special_Value"]);
            $stmt->bindValue(":dateCreated", $info["Date_Created"]);
            $stmt->bindValue(":sku", $info["SKU"]);
            $stmt->bindValue(":stockQuantity", $info["Stock_Quantity"]);
            $stmt->bindValue(":productMode", $info["ProductMode"]);
            $stmt->bindValue(":ageRestriction", $info["Age_Restriction"]);
            $stmt->bindValue(":saleType", $info["sale_type"]);
            $stmt->bindValue(":status", $info["status"]);
            $stmt->bindValue(":storeAdminID", $this->storeAdminID);
            $stmt->bindValue(":createdDate", $createdOn);
            // $stmt->bindValue(":discount_percent", $info["discount_percent"]);
            // $stmt->bindValue(":discount_pretax", $info["discount_pretax"]);
            // $stmt->bindValue(":discount_posttax", $info["discount_posttax"]);
            $stmt->execute();

            return $this->db->lastInsertId();
        } catch (PDOException $ex) {
            Logger::errExcept($ex);
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Fetches product full info.
     * @param string $upc UPC.
     * @return array Product info.
     * @throws Exception
     */
    public function getFullInfo($upc)
    {
        try {
            $selectSQL = "SELECT
                    *,
                    (SELECT
                            CONCAT('http://mystore.plat4minc.com/', image)
                        FROM  product_images pi
                        WHERE pi.product_id = p.product_id
                    ) AS Image
                FROM `products` p
                LEFT JOIN product_details pd
                    ON pd.product_id = p.product_id
                WHERE `UPC` = :upc
                AND pd.storeadmin_id = :storeAdminID
                LIMIT 1";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":upc", $upc, PDO::PARAM_STR);
            $stmt->bindValue(":storeAdminID", $this->storeAdminID, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            Logger::errExcept($ex);
            throw new Exception($ex->getMessage());
        }
    }
}

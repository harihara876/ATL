<?php

namespace Plat4m\Core\API;

use Exception;
use PDO;
use PDOException;

class Subcategory
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
     * Fetches all categories.
     * @return array Categories.
     * @throws Exception
     */
    public function getAll()
    {
        try {
            $selectSQL = "SELECT
                    `sc`.`Sub_Category_Id`,
                    `sc`.`Sub_Category_Name`,
                    `sc`.`Image`,
                    `sc`.`Description`,
                    `c`.`cat_id`
                FROM `subcategories` `sc`
                JOIN `category` `c` ON `sc`.`cat_id` = `c`.`cat_id`";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches all subcategories by store admin ID.
     * @param int $storeAdminID Store admin ID.
     * @return array Subcategories.
     * @throws Exception
     */
    public function getAllByStoreAdminID($storeAdminID)
    {
        try {
            $selectSQL = "SELECT
                    `sc`.`Sub_Category_Id`,
                    `sc`.`Sub_Category_Name`,
                    `sc`.`Image`,
                    `sc`.`Description`,
                    `c`.`cat_id`
                FROM `subcategories` `sc`
                JOIN `category` `c` ON `sc`.`cat_id` = `c`.`cat_id`
                WHERE `sc`.`Sub_Category_Id` IN (
                    SELECT DISTINCT(`p`.`Category_Type`)
                    FROM `products` `p` JOIN `product_details` `pd`
                    ON `p`.`product_id` = `pd`.`product_id`
                    WHERE `pd`.`storeadmin_id` = :storeAdminID
                )";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID", $storeAdminID, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches subcategory info by ID.
     * @param int $subcatID Subcategory ID.
     * @return array Subcategory info.
     * @throws Exception
     */
    public function getInfoByID($subcatID)
    {
        try {
            $selectSQL = "SELECT * FROM `subcategories`
                WHERE `Sub_Category_Id` = :subcatID
                LIMIT 1";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":subcatID", $subcatID, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }
}

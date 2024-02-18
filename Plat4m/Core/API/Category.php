<?php

namespace Plat4m\Core\API;

use Exception;
use PDO;
use PDOException;

class Category
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
            $selectSQL = "SELECT * FROM `category`";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches all categories by store admin ID.
     * @param int $storeAdminID Store admin ID.
     * @return array Categories.
     * @throws Exception
     */
    public function getAllByStoreAdminID($storeAdminID)
    {
        try {
            $selectSQL = "SELECT
                    `category`.`cat_id`,
                    `category`.`category_name`,
                    `category`.`category_image`,
                    `category`.`Description`
                FROM `category`
                INNER JOIN `products` ON `products`.`Category_Id` = `category`.`cat_id`
                INNER JOIN `product_details` ON `product_details`.`product_id` = `products`.`product_id`
                WHERE `product_details`.`storeadmin_id` = :storeAdminID
                GROUP BY `category`.`cat_id`
                ORDER BY `category`.`cat_id`";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID", $storeAdminID, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches category info by ID.
     * @param int $catID Category ID.
     * @return array Category info.
     * @throws Exception
     */
    public function getInfoByID($catID)
    {
        try {
            $selectSQL = "SELECT * FROM `category`
                WHERE `cat_id` = :catID
                LIMIT 1";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":catID", $catID, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }
}

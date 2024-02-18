<?php
namespace T3Stores\Store;

use \PDO;
use \PDOException;
use \Exception;

class Product
{
    // DB connection object.
    private $db;



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
     * Fetches product info by ID.
     * @param int $id Product ID.
     * @return mixed Product info on success, FALSE on failure.
     */
    public function getInfoByID($id)
    {
        try {
            $selectSQL = "SELECT * FROM `products` WHERE `id` = :id";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
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
            // '1','2','3'
            $ids = "'" . implode("','", $ids) . "'";
            /*$selectSQL = "SELECT product_id,product_name FROM `products`  WHERE `product_id` IN ({$ids})
			              UNION 
                          SELECT product_id,product_name FROM `products_temp` WHERE `product_id` IN ({$ids})";*/
                          
            $selectSQL = "SELECT DISTINCT *
						  FROM
						  (SELECT product_id,product_name FROM `products`  WHERE `product_id` IN ({$ids})
			              UNION 
                          SELECT product_id,product_name FROM `products_temp` WHERE `product_id` IN ({$ids})
						  )a GROUP BY product_id";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }
}
<?php

namespace Plat4m\Core\API;

use Exception;
use PDO;
use PDOException;

class ProductImage
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
     * Fetch images list of a product.
     * @param int $productID Product ID.
     * @return array Images list.
     * @throws Exception
     */
    public function getListByProductID($productID)
    {
        try {
            $selectSQL = "SELECT * FROM `product_images` WHERE `product_id` = :productID";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":productID", $productID, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }
}

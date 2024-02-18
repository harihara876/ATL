<?php
namespace T3Stores\Store;

use \PDO;

class OrderPrice
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
     * Fetches all device users.
     * @return array Device users.
     */
    public function getAll()
    {
        $selectSQL = "SELECT * FROM `app_product_price_change` ORDER BY `id` ASC";
        $stmt = $this->db->prepare($selectSQL);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



}
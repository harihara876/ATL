<?php

namespace Plat4m\Core\API;

use PDO;
use PDOException;
use Exception;
use Plat4m\Utilities\Logger;

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
            $error = "Requires DB connection";
            Logger::errorMsg($error);
            throw new Exception($error, 500);
        }

        $this->db = $db;
    }

    /**
     * Fetches all device users.
     * @return array Device users.
     */
    public function getAll()
    {
        try {
            $selectSQL = "SELECT * FROM `app_product_price_change` ORDER BY `id` ASC";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            Logger::errExcept($ex);
            throw new Exception($ex->getMessage(), 500);
        }
    }
}

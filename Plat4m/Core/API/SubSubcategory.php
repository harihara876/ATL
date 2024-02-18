<?php

namespace Plat4m\Core\API;

use Exception;
use PDO;
use PDOException;

class SubSubcategory
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
            $selectSQL = "SELECT * FROM `subsubcategories`";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches all sub-subcategories by store admin ID.
     * @param int $storeAdminID Store admin ID.
     * @return array Subcategories.
     * @throws Exception
     */
    public function getAllByStoreAdminID($storeAdminID)
    {
        try {
            // ERROR: There is no `storeadmin_id` column.
            // $selectSQL = "SELECT * FROM `subsubcategories`
            //     WHERE `storeadmin_id` = :storeAdminID";
            $selectSQL = "SELECT * FROM `subsubcategories`";
            $stmt = $this->db->prepare($selectSQL);
            // $stmt->bindValue(":storeAdminID", $storeAdminID, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }
}

<?php

namespace Plat4m\Core\Web;

use Exception;
use PDO;
use PDOException;

class Product
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
        }

        $this->storeAdminID = $storeAdminID;

        return $this;
    }

    /**
     * Fetches total count of products.
     * @return int Total count.
     * @throws Exception
     */
    public function totalCount()
    {
        try {
            $selectSQL = "SELECT COUNT(*) FROM `product_details`
                WHERE `storeadmin_id` = :storeAdminID";

            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID", $this->storeAdminID);
            $stmt->execute();

            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches total count of products.
     * @param array $filters Filters.
     * @return int Total count.
     * @throws Exception
     */
    public function totalCountWithFilter($filters)
    {
        try {
            $filterCond = "";
            $productName = "%{$filters["productName"]}%";
            $upc = "%{$filters["upc"]}%";

            if (!empty($filters["productName"]) || !empty($filters["upc"])) {
                $filterCond = "AND `p`.`product_name` LIKE :productName OR `UPC` LIKE :upc";
            }

            $selectSQL = "SELECT COUNT(*) FROM `products` `p`
                LEFT JOIN `product_details` `pd` ON `pd`.`product_id` = `p`.`product_id`
                WHERE `storeadmin_id` = :storeAdminID
                {$filterCond}";

            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID", $this->storeAdminID);

            if ($filterCond) {
                $stmt->bindValue(":productName", $productName);
                $stmt->bindValue(":upc", $upc);
            }

            $stmt->execute();

            return $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches products based on filters.
     * @param array $filters Filters.
     * @return array Products.
     * @throws Exception
     */
    public function productsWithFilter($filters)
    {
        try {
            $filterCond = "";
            $productName = "%{$filters["productName"]}%";
            $upc = "%{$filters["upc"]}%";

            if (!empty($filters["productName"]) || !empty($filters["upc"])) {
                $filterCond = "AND `p`.`product_name` LIKE :productName OR `p`.`UPC` LIKE :upc";
            }

            $selectSQL = "SELECT
                    p.product_id,
                    p.id,
                    p.product_name,
                    p.UPC,
                    pd.quantity,
                    pd.Regular_Price,
                    pd.sellprice,
                    img.image
                FROM `products` `p`
                LEFT JOIN `product_details` `pd` ON `pd`.`product_id` = `p`.`product_id`
                LEFT JOIN (
                            SELECT `image`,`product_id` 
                            FROM `product_images` 
                            GROUP BY `product_id`
                        ) `img` ON `img`.`product_id` = `p`.`product_id`
                WHERE `pd`.`storeadmin_id` = :storeAdminID
                {$filterCond}
                ORDER BY :columnName :columnSortOrder
                LIMIT :offset, :rowCount ";

            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID", $this->storeAdminID);
            $stmt->bindValue(":columnName", $filters["columnName"]);
            $stmt->bindValue(":columnSortOrder", $filters["columnSortOrder"]);
            $stmt->bindValue(":offset", $filters["offset"], PDO::PARAM_INT);
            $stmt->bindValue(":rowCount", $filters["rowCount"], PDO::PARAM_INT);

            if ($filterCond) {
                $stmt->bindValue(":productName", $productName);
                $stmt->bindValue(":upc", $upc);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }
}

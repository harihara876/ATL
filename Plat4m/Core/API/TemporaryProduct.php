<?php

namespace Plat4m\Core\API;

use Exception;
use PDO;
use PDOException;

class TemporaryProduct
{
    // DB connection object.
    private $db;

    // Product info.
    private $productInfo = [
        "product_name"          => NULL,
        "product_id"            => NULL,
        "cat_id"                => NULL,
        "description"           => NULL,
        "price"                 => NULL,
        "selling_price"         => NULL,
        "color"                 => NULL,
        "size"                  => NULL,
        "product_status"        => NULL,
        "quantity"              => NULL,
        "date"                  => NULL,
        "p_limit"               => NULL,
        "upc"                   => NULL,
        "regular_price"         => NULL,
        "buying_price"          => NULL,
        "tax_status"            => NULL,
        "tax_value"             => NULL,
        "special_value"         => NULL,
        "category_id"           => NULL,
        "category_type"         => NULL,
        "date_created"          => NULL,
        "sku"                   => NULL,
        "image"                 => NULL,
        "stock_quantity"        => NULL,
        "manufacturer"          => NULL,
        "brand"                 => NULL,
        "vendor"                => NULL,
        "product_mode"          => NULL,
        "age_restriction"       => NULL,
        "sale_type"             => NULL,
        "upc_status_request"    => NULL,
        "storeadmin_id"         => NULL,
        "multi_item_quantity"   => NULL,
        "multi_item_price"      => NULL,
        "discount_percent"      => 0,
        "discount_pretax"       => 0,
        "discount_posttax"      => 0,
    ];

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
     * Sets product info.
     * @param array $info Product info.
     * @return object Current object.
     */
    public function setInfo($info)
    {
        // TODO
        // Validate

        foreach ($this->productInfo as $key => $value) {
            $this->productInfo[$key] = isset($info[$key])
                ? $info[$key]
                : $this->productInfo[$key];
        }

        return $this;
    }

    /**
     * Creates temp product.
     * @return int Last insert ID.
     */
    public function create()
    {
        $info = $this->productInfo;
        $insertSQL = "INSERT INTO `products_temp` (
            `product_name`,
            `product_id`,
            `cat_id`,
            `description`,
            `price`,
            `selling_price`,
            `color`,
            `size`,
            `product_status`,
            `quantity`,
            `date`,
            `p_limit`,
            `upc`,
            `regular_price`,
            `buying_price`,
            `tax_status`,
            `tax_value`,
            `special_value`,
            `category_id`,
            `category_type`,
            `date_created`,
            `sku`,
            `image`,
            `stock_quantity`,
            `manufacturer`,
            `brand`,
            `vendor`,
            `product_mode`,
            `age_restriction`,
            `sale_type`,
            `upc_status_request`,
            `storeadmin_id`,
            `multi_item_quantity`,
            `multi_item_price`,
            `discount_percent`,
            `discount_pretax`,
            `discount_posttax`
        ) VALUES (
            :product_name,
            :product_id,
            :cat_id,
            :description,
            :price,
            :selling_price,
            :color,
            :size,
            :product_status,
            :quantity,
            :date,
            :p_limit,
            :upc,
            :regular_price,
            :buying_price,
            :tax_status,
            :tax_value,
            :special_value,
            :category_id,
            :category_type,
            :date_created,
            :sku,
            :image,
            :stock_quantity,
            :manufacturer,
            :brand,
            :vendor,
            :product_mode,
            :age_restriction,
            :sale_type,
            :upc_status_request,
            :storeadmin_id,
            :multi_item_quantity,
            :multi_item_price,
            :discount_percent,
            :discount_pretax,
            :discount_posttax
        )";

        try {
            $productID = $this->generateProductID();
            $info["upc_status_request"] = 1;

            $stmt = $this->db->prepare($insertSQL);
            $stmt->bindValue(":product_name", $info["product_name"]);
            $stmt->bindValue(":product_id", $productID);
            $stmt->bindValue(":cat_id", $info["cat_id"]);
            $stmt->bindValue(":description", $info["description"]);
            $stmt->bindValue(":price", $info["price"]);
            $stmt->bindValue(":selling_price", $info["selling_price"]);
            $stmt->bindValue(":color", $info["color"]);
            $stmt->bindValue(":size", $info["size"]);
            $stmt->bindValue(":product_status", $info["product_status"]);
            $stmt->bindValue(":quantity", $info["quantity"]);
            $stmt->bindValue(":date", $info["date"]);
            $stmt->bindValue(":p_limit", $info["p_limit"]);
            $stmt->bindValue(":upc", $info["upc"]);
            $stmt->bindValue(":regular_price", $info["regular_price"]);
            $stmt->bindValue(":buying_price", $info["buying_price"]);
            $stmt->bindValue(":tax_status", $info["tax_status"]);
            $stmt->bindValue(":tax_value", $info["tax_value"]);
            $stmt->bindValue(":special_value", $info["special_value"]);
            $stmt->bindValue(":category_id", $info["category_id"]);
            $stmt->bindValue(":category_type", $info["category_type"]);
            $stmt->bindValue(":date_created", $info["date_created"]);
            $stmt->bindValue(":sku", $info["sku"]);
            $stmt->bindValue(":image", $info["image"]);
            $stmt->bindValue(":stock_quantity", $info["stock_quantity"]);
            $stmt->bindValue(":manufacturer", $info["manufacturer"]);
            $stmt->bindValue(":brand", $info["brand"]);
            $stmt->bindValue(":vendor", $info["vendor"]);
            $stmt->bindValue(":product_mode", $info["product_mode"]);
            $stmt->bindValue(":age_restriction", $info["age_restriction"]);
            $stmt->bindValue(":sale_type", $info["sale_type"]);
            $stmt->bindValue(":upc_status_request", $info["upc_status_request"]);
            $stmt->bindValue(":storeadmin_id", $info["storeadmin_id"]);
            $stmt->bindValue(":multi_item_quantity", $info["multi_item_quantity"]);
            $stmt->bindValue(":multi_item_price", $info["multi_item_price"]);
            $stmt->bindValue(":discount_percent", $info["discount_percent"]);
            $stmt->bindValue(":discount_pretax", $info["discount_pretax"]);
            $stmt->bindValue(":discount_posttax", $info["discount_posttax"]);
            $stmt->execute();

            return $this->db->lastInsertId();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Generates product ID.
     * @return int Product ID.
     * @throws Exception
     */
    private function generateProductID()
    {
        try {
            $selectSQL = "SELECT IFNULL(MAX(`product_id`), 0) `product_id`
                FROM (
                    SELECT `product_id` FROM `products`
                    UNION ALL
                    SELECT `product_id` FROM `products_temp`
                ) `a`";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->execute();
            $productID = $stmt->fetchColumn();

            return $productID + 1;
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Returns all temporary products list.
     * @return array Temporary products.
     * @throws Exception
     */
    public function getAll()
    {
        try {
            $selectSQL = "SELECT * FROM `products_temp` ORDER BY `id` ASC";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Returns all temporary products list by store ID.
     * @param int $storeAdminID Store admin ID.
     * @return array Products.
     */
    public function getAllByStoreID($storeAdminID)
    {
        try {
            $selectSQL = "SELECT *
                FROM `products_temp`
                WHERE `storeadmin_id` = :storeAdminID
                ORDER BY `id` ASC";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":storeAdminID", $storeAdminID);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            $productIDs = implode(",", $ids);
            $selectSQL = "SELECT `product_id`, `product_name`
                FROM `products_temp`
                WHERE `product_id` IN ({$productIDs})";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Checks if UPC exists in products_temp table.
     * @param string $upc UPC.
     * @param int $storeAdminID Store admin ID.
     * @return bool Exists or not.
     * @throws Exception
     */
    public function checkIfUPCExists($upc, $storeAdminID)
    {
        try {
            $selectSQL = "SELECT EXISTS(
                SELECT * FROM `products_temp`
                WHERE `upc` = :UPC AND storeadmin_id = :storeAdminID
            ) AS `upc_found`";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":UPC", $upc, PDO::PARAM_STR);
            $stmt->bindValue(":storeAdminID", $storeAdminID, PDO::PARAM_INT);
            $stmt->execute();

            return (bool) $stmt->fetchColumn();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    /**
     * Fetches temp product info by upc and store admin.
     * @param string $upc UPC.
     * @param int $storeAdminID Store admin ID.
     * @return array Temp product info.
     * @throws Exception
     */
    public function getInfo($upc, $storeAdminID)
    {
        try {
            $selectSQL = "SELECT * FROM `products_temp`
                WHERE `upc` = :UPC AND storeadmin_id = :storeAdminID
                LIMIT 1";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":UPC", $upc, PDO::PARAM_STR);
            $stmt->bindValue(":storeAdminID", $storeAdminID, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public function getStoreProduct($upc, $storeid)
    {
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
            WHERE `UPC` = {$upc}
            AND pd.storeadmin_id = " . $storeid;
        return $this->db->query($selectSQL)->fetch(PDO::FETCH_OBJ);
    }

    public function getCategory($cid)
    {
        $selectSQL = "SELECT * FROM `category` WHERE `cat_id`=" . $cid;
        return $this->db->query($selectSQL)->fetch(PDO::FETCH_OBJ);
    }

    public function getSubCategory($subcid)
    {
        $selectSQL = "SELECT * FROM `subcategories` WHERE `Sub_Category_Id`=" . $subcid;
        return $this->db->query($selectSQL)->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Fetch images of product by UPC.
     * @param string $upc UPC.
     * @return array Images.
     */
    public function getImagesByUPC($upc)
    {
        try {
            $selectSQL = "SELECT `image`
                FROM `products_temp`
                WHERE `upc` = :upc
                ORDER BY `id` DESC";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":upc", $upc, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }
}

<?php

namespace Plat4m\Core\API;

use Exception;
use PDO;
use PDOException;

class TemporaryProduct2
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
        "phone"                 => NULL,
        "email"                 => NULL,
        "checkbit"              => NULL,
        "latitude"              => NULL,
        "longitude"             => NULL,
        "localtime"             => NULL,
        "weather"               => NULL,
        // "discount_percent"      => NULL,
        // "discount_pretax"       => NULL,
        // "discount_posttax"      => NULL,
    ];

    /**
     * Connects to DB on invoke.
     * @param object $db PDO.
     */
    public function __construct($db)
    {
        if (!$db) {
            throw new Exception("Requires DB connection");
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
        // print_r($info);
        if (empty($info["upc"])) {
            throw new Exception("UPC is required", 400);
        }

        if (empty($info["price"])) {
            throw new Exception("Price is required", 400);
        }

        foreach ($this->productInfo as $key => $value) {
            $this->productInfo[$key] = !empty($info[$key]) ? $info[$key] : NULL;
        }

        return $this;
    }

    /**
     * Creates product.
     * @return int Last insert ID.
     */
    public function create()
    {

        //$selectSQL = "SELECT max(product_id) as product_id FROM `products`";
        $selectSQL = "SELECT IFNULL(MAX(product_id), 0) product_id FROM(SELECT product_id FROM products UNION ALL SELECT product_id FROM products_temp) a";
        $product_id = $this->db->query($selectSQL)->fetch(PDO::FETCH_OBJ);
        $product_id = ($product_id->product_id) + 1;


        $info = $this->productInfo;
        $insertSQL = "INSERT INTO `products_temp2` (
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
            `phone`,
            `email`,
            `checkbit`,
            `latitude`,
            `longitude`,
            `localtime`,
            `weather`
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
            :phone,
            :email,
            :checkbit,
            :latitude,
            :longitude,
            :localtime,
            :weather
        )";

        try {
            $stmt = $this->db->prepare($insertSQL);
            $info["upc_status_request"] = 1;
            $stmt->bindParam(":product_name", $info["product_name"]);
            $stmt->bindParam(":product_id", $product_id);
            $stmt->bindParam(":cat_id", $info["cat_id"]);
            $stmt->bindParam(":description", $info["description"]);
            $stmt->bindParam(":price", $info["price"]);
            $stmt->bindParam(":selling_price", $info["selling_price"]);
            $stmt->bindParam(":color", $info["color"]);
            $stmt->bindParam(":size", $info["size"]);
            $stmt->bindParam(":product_status", $info["product_status"]);
            $stmt->bindParam(":quantity", $info["quantity"]);
            $stmt->bindParam(":date", $info["date"]);
            $stmt->bindParam(":p_limit", $info["p_limit"]);
            $stmt->bindParam(":upc", $info["upc"]);
            $stmt->bindParam(":regular_price", $info["regular_price"]);
            $stmt->bindParam(":buying_price", $info["buying_price"]);
            $stmt->bindParam(":tax_status", $info["tax_status"]);
            $stmt->bindParam(":tax_value", $info["tax_value"]);
            $stmt->bindParam(":special_value", $info["special_value"]);
            $stmt->bindParam(":category_id", $info["category_id"]);
            $stmt->bindParam(":category_type", $info["category_type"]);
            $stmt->bindParam(":date_created", $info["date_created"]);
            $stmt->bindParam(":sku", $info["sku"]);
            $stmt->bindParam(":image", $info["image"]);
            $stmt->bindParam(":stock_quantity", $info["stock_quantity"]);
            $stmt->bindParam(":manufacturer", $info["manufacturer"]);
            $stmt->bindParam(":brand", $info["brand"]);
            $stmt->bindParam(":vendor", $info["vendor"]);
            $stmt->bindParam(":product_mode", $info["product_mode"]);
            $stmt->bindParam(":age_restriction", $info["age_restriction"]);
            $stmt->bindParam(":sale_type", $info["sale_type"]);
            $stmt->bindParam(":upc_status_request", $info["upc_status_request"]);
            $stmt->bindParam(":storeadmin_id", $info["storeadmin_id"]);
            $stmt->bindParam(":phone", $info["phone"]);
            $stmt->bindParam(":email", $info["email"]);
            $stmt->bindParam(":checkbit", $info["checkbit"]);
            $stmt->bindParam(":latitude", $info["latitude"]);
            $stmt->bindParam(":longitude", $info["longitude"]);
            $stmt->bindParam(":localtime", $info["localtime"]);
            $stmt->bindParam(":weather", $info["weather"]);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches product info by ID.
     * @param int $rowID Row ID.
     * @return array Product info.
     * @throws Exception
     */
    public function getInfoByID($rowID)
    {
        try {
            $selectSQL = "SELECT *
                FROM `products_temp2`
                WHERE `id` = :rowID";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":rowID", $rowID, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
    }

    /**
     * Fetches product info based on UPC and store admin ID.
     * @param string $upc UPC.
     * @return array Product info.
     * @throws Exception
     */
    public function getInfoByUPC($upc)
    {
        try {
            $selectSQL = "SELECT *
                FROM `products_temp2`
                WHERE `upc` = :upc
                ORDER BY `id` DESC
                LIMIT 1";
            $stmt = $this->db->prepare($selectSQL);
            $stmt->bindValue(":upc", $upc, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            throw new Exception($ex->getMessage(), 500);
        }
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
                FROM `products_temp2`
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

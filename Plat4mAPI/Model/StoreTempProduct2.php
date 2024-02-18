<?php

namespace Plat4mAPI\Model;

use PDO;

class StoreTempProduct2
{
    // Admin ID.
    private $adminID = NULL;

    /**
     * Set values.
     */
    public function __construct($adminID)
    {
        $this->adminID = $adminID;
    }

     /**
     * Format temporary product info.
     * @param array $product Product info.
     * @return array Formatted info.
     */
    public function format($product)
    {
        if (!$product) {
            return NULL;
        }

        return [
            "age_restriction"       => (int) $product["age_restriction"],
            "brand"                 => (string) $product["brand"],
            "buying_price"          => (float) $product["buying_price"],
            "category_id"           => (int) $product["category_id"],
            "subcategory_id"        => (int) $product["subcategory_id"],
            "color"                 => (string) $product["color"],
            "created_on"            => (string) $product["created_on"],
            "description"           => (string) $product["description"],
            "discount_percent"      => (int) $product["discount_percent"],
            "discount_posttax"      => (int) $product["discount_posttax"],
            "discount_pretax"       => (int) $product["discount_pretax"],
            "id"                    => (int) $product["id"],
            "image_url"             => (string) $product["image_url"],
            "manufacturer"          => (string) $product["manufacturer"],
            "per_order_limit"       => (int) $product["per_order_limit"],
            "price"                 => (float) $product["price"],
            "product_id"            => (int) $product["product_id"],
            "product_mode"          => (string) $product["product_mode"],
            "store_product_name"    => (string) $product["store_product_name"],
            "product_status"        => (string) $product["product_status"],
            "quantity"              => (int) $product["quantity"],
            "regular_price"         => (float) $product["regular_price"],
            "sale_type"             => (string) $product["sale_type"],
            "selling_price"         => (float) $product["selling_price"],
            "size"                  => (string) $product["size"],
            "sku"                   => (int) $product["sku"],
            "special_value"         => (float) $product["special_value"],
            "stock_quantity"        => (int) $product["stock_quantity"],
            "admin_id"              => (int) $product["admin_id"],
            "tax_status"            => (string) $product["tax_status"],
            "tax_value"             => (float) $product["tax_value"],
            "upc"                   => (string) $product["upc"],
            "upc_status_request"    => (int) $product["upc_status_request"],
            "vendor"                => (string) $product["vendor"],
        ];
    }

    /**
     * Create product in temporary table.
     * @param object $ctx Context.
     * @param array $product Product info.
     * @return int Last insert ID.
     */
    public function create($ctx, $product)
    {
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
                `manufacturer`,
                `brand`,
                `vendor`,
                `product_mode`,
                `age_restriction`,
                `sale_type`,
                `upc_status_request`,
                `storeadmin_id`,
                `created_on`,
                `phone`,
                `email`,
                `checkbit`,
                `latitude`,
                `longitude`,
                `localtime`,
                `weather`,
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
                :manufacturer,
                :brand,
                :vendor,
                :product_mode,
                :age_restriction,
                :sale_type,
                :upc_status_request,
                :storeadmin_id,
                :created_on,
                :mobile_number,
                :email,
                :checkbit,
                :latitude,
                :longitude,
                :localtime,
                :weather,
                :discount_percent,
                :discount_pretax,
                :discount_posttax
            )";
        $stmt = $ctx->db->prepare($insertSQL);
        $stmt->bindValue(":product_name", $product["product_name"]);
        $stmt->bindValue(":product_id", $product["product_id"]);
        $stmt->bindValue(":cat_id", $product["cat_id"]);
        $stmt->bindValue(":description", $product["description"]);
        $stmt->bindValue(":price", $product["price"]);
        $stmt->bindValue(":selling_price", $product["selling_price"]);
        $stmt->bindValue(":color", $product["color"]);
        $stmt->bindValue(":size", $product["size"]);
        $stmt->bindValue(":product_status", $product["product_status"]);
        $stmt->bindValue(":quantity", $product["quantity"]);
        $stmt->bindValue(":date", $product["date"]);
        $stmt->bindValue(":p_limit", $product["p_limit"]);
        $stmt->bindValue(":upc", $product["upc"]);
        $stmt->bindValue(":regular_price", $product["regular_price"]);
        $stmt->bindValue(":buying_price", $product["buying_price"]);
        $stmt->bindValue(":tax_status", $product["tax_status"]);
        $stmt->bindValue(":tax_value", $product["tax_value"]);
        $stmt->bindValue(":special_value", $product["special_value"]);
        $stmt->bindValue(":category_id", $product["category_id"]);
        $stmt->bindValue(":category_type", $product["subcategory_id"]);
        $stmt->bindValue(":date_created", $product["date_created"]);
        $stmt->bindValue(":sku", $product["sku"]);
        $stmt->bindValue(":image", $product["image"]);
        $stmt->bindValue(":manufacturer", $product["manufacturer"]);
        $stmt->bindValue(":brand", $product["brand"]);
        $stmt->bindValue(":vendor", $product["vendor"]);
        $stmt->bindValue(":product_mode", $product["product_mode"]);
        $stmt->bindValue(":age_restriction", $product["age_restriction"]);
        $stmt->bindValue(":sale_type", $product["sale_type"]);
        $stmt->bindValue(":upc_status_request", $product["upc_status_request"]);
        $stmt->bindValue(":storeadmin_id", $this->adminID);
        $stmt->bindValue(":created_on", $ctx->now);
        $stmt->bindValue(":mobile_number", $product["phone"]);
        $stmt->bindValue(":email", $product["email"]);
        $stmt->bindValue(":checkbit", $product["checkbit"]);
        $stmt->bindValue(":latitude", $product["latitude"]);
        $stmt->bindValue(":longitude", $product["longitude"]);
        $stmt->bindValue(":localtime", $product["localtime"]);
        $stmt->bindValue(":weather", $product["weather"]);
        $stmt->bindValue(":discount_percent", $product["discount_percent"]);
        $stmt->bindValue(":discount_pretax", $product["discount_pretax"]);
        $stmt->bindValue(":discount_posttax", $product["discount_posttax"]);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Update Temp2 product.
     * @param object $ctx Context.
     * @param object $product Product info.
     */
    // public function updateProduct($ctx, $productInfo)
    // {
    //     $updateSQL = "UPDATE `products_temp2`
    //         SET `product_name` = :product_name,
    //             `product_id` = :product_id,
    //             `cat_id` = :cat_id,
    //             `description` = :description,
    //             `price` = :price,
    //             `selling_price` = :selling_price,
    //             `color` = :color,
    //             `size` = :size,
    //             `product_status` = :product_status,
    //             `quantity` = :quantity,
    //             `date` = :date,
    //             `p_limit` = :p_limit,
    //             `upc` = :upc,
    //             `regular_price` = :regular_price,
    //             `buying_price` = :buying_price,
    //             `tax_status` = :tax_status,
    //             `tax_value` = :tax_value,
    //             `special_value` = :special_value,
    //             `category_id` = :category_id,
    //             `category_type` = :category_type,
    //             `date_created` = :date_created,
    //             `sku` = :sku,
    //             `image` = :image,
    //             `manufacturer` = :manufacturer,
    //             `brand` = :brand,
    //             `vendor` = :vendor,
    //             `product_mode` = :product_mode,
    //             `age_restriction` = :age_restriction,
    //             `sale_type` = :sale_type,
    //             `upc_status_request` = :upc_status_request,
    //             `storeadmin_id` = :storeadmin_id,
    //             `created_on` = :created_on,
    //             `phone` = :mobile_number,
    //             `email` = :email,
    //             `checkbit` = :checkbit,
    //             `latitude` = :latitude,
    //             `longitude` = :longitude,
    //             `localtime` = :localtime,
    //             `weather` = :weather,
    //             `discount_percent` = :discount_percent,
    //             `discount_pretax` = :discount_pretax,
    //             `discount_posttax` = :discount_posttax
    //         WHERE `product_id` = :product_id
    //         AND `storeadmin_id` = :storeadmin_id";
    //     $stmt = $ctx->db->prepare($updateSQL);
    //     $stmt->bindValue(":product_name", $productInfo["product_name"]);
    //     $stmt->bindValue(":product_id", $productInfo["product_id"]);
    //     $stmt->bindValue(":cat_id", $productInfo["cat_id"]);
    //     $stmt->bindValue(":description", $productInfo["description"]);
    //     $stmt->bindValue(":price", $productInfo["price"]);
    //     $stmt->bindValue(":selling_price", $productInfo["selling_price"]);
    //     $stmt->bindValue(":color", $productInfo["color"]);
    //     $stmt->bindValue(":size", $productInfo["size"]);
    //     $stmt->bindValue(":product_status", $productInfo["product_status"]);
    //     $stmt->bindValue(":quantity", $productInfo["quantity"]);
    //     $stmt->bindValue(":date", $productInfo["date"]);
    //     $stmt->bindValue(":p_limit", $productInfo["p_limit"]);
    //     $stmt->bindValue(":upc", $productInfo["upc"]);
    //     $stmt->bindValue(":regular_price", $productInfo["regular_price"]);
    //     $stmt->bindValue(":buying_price", $productInfo["buying_price"]);
    //     $stmt->bindValue(":tax_status", $productInfo["tax_status"]);
    //     $stmt->bindValue(":tax_value", $productInfo["tax_value"]);
    //     $stmt->bindValue(":special_value", $productInfo["special_value"]);
    //     $stmt->bindValue(":category_id", $productInfo["category_id"]);
    //     $stmt->bindValue(":category_type", $productInfo["subcategory_id"]);
    //     $stmt->bindValue(":date_created", $productInfo["date_created"]);
    //     $stmt->bindValue(":sku", $productInfo["sku"]);
    //     $stmt->bindValue(":image", $productInfo["image"]);
    //     $stmt->bindValue(":manufacturer", $productInfo["manufacturer"]);
    //     $stmt->bindValue(":brand", $productInfo["brand"]);
    //     $stmt->bindValue(":vendor", $productInfo["vendor"]);
    //     $stmt->bindValue(":product_mode", $productInfo["product_mode"]);
    //     $stmt->bindValue(":age_restriction", $productInfo["age_restriction"]);
    //     $stmt->bindValue(":sale_type", $productInfo["sale_type"]);
    //     $stmt->bindValue(":upc_status_request", $productInfo["upc_status_request"]);
    //     $stmt->bindValue(":storeadmin_id", $ctx->tokenData->store_admin_id);
    //     $stmt->bindValue(":created_on", $ctx->now);
    //     $stmt->bindValue(":mobile_number", $productInfo["phone"]);
    //     $stmt->bindValue(":email", $productInfo["email"]);
    //     $stmt->bindValue(":checkbit", $productInfo["checkbit"]);
    //     $stmt->bindValue(":latitude", $productInfo["latitude"]);
    //     $stmt->bindValue(":longitude", $productInfo["longitude"]);
    //     $stmt->bindValue(":localtime", $productInfo["localtime"]);
    //     $stmt->bindValue(":weather", $productInfo["weather"]);
    //     $stmt->bindValue(":discount_percent", $productInfo["discount_percent"]);
    //     $stmt->bindValue(":discount_pretax", $productInfo["discount_pretax"]);
    //     $stmt->bindValue(":discount_posttax", $productInfo["discount_posttax"]);
    //     $stmt->execute();
    //     // return $ctx->db->lastInsertId();

    //     return $stmt->rowCount();
    // }

    /**
     * Fetch all temporary products by admin ID.
     * @param object $ctx Context.
     * @return array Products list.
     */
    public function getAll($ctx)
    {
        $selectSQL = "SELECT
                `age_restriction`,
                `brand`,
                `buying_price`,
                `category_id`,
                `category_type` AS `subcategory_id`,
                `checkbit`,
                `color`,
                `created_on`,
                `description`,
                `discount_percent`,
                `discount_posttax`,
                `discount_pretax`,
                `email`,
                `id`,
                `image` AS `image_url`,
                `latitude`,
                `longitude`,
                `manufacturer`,
                `multi_item_price`,
                `multi_item_quantity`,
                `p_limit` AS `per_order_limit`,
                `phone`,
                `price`,
                `product_id`,
                `product_mode`,
                `product_name` AS `name`,
                `product_status`,
                `quantity`,
                `regular_price`,
                `sale_type`,
                `selling_price`,
                `size`,
                `sku`,
                `special_value`,
                `stock_quantity`,
                `storeadmin_id` AS `admin_id`,
                `tax_status`,
                `tax_value`,
                `upc`,
                `upc_status_request`,
                `vendor`,
                `weather`
            FROM `products_temp2`
            WHERE `storeadmin_id` = :adminID
            ORDER BY `id` ASC";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":adminID", $this->adminID, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch images of product by UPC.
     * @param object $ctx Context.
     * @param string $upc UPC.
     * @return array Images.
     */
    public function getImagesByUPC($ctx, $upc)
    {
        $selectSQL = "SELECT `image`
            FROM `products_temp2`
            WHERE `upc` = :upc
            ORDER BY `id` DESC";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":upc", $upc, PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $images = [];

        foreach ($rows as $row) {
            $images[] = $row["image"];
        }

        return $images;
    }

     /**
     * Fetch temporary 2 product by UPC.
     * @param object $ctx Context.
     * @param string $upc UPC.
     * @return array Product info.
     */
    public function getInfoByUPC($ctx, $upc)
    {
        $selectSQL = "SELECT
                `pt`.`age_restriction`,
                `pt`.`brand`,
                `pt`.`buying_price`,
                `pt`.`category_id`,
                `pt`.`category_type` AS `subcategory_id`,
                `pt`.`color`,
                `pt`.`created_on`,
                `pt`.`description`,
                `pt`.`discount_percent`,
                `pt`.`discount_posttax`,
                `pt`.`discount_pretax`,
                `pt`.`id`,
                `pt`.`image` AS `image_url`,
                `pt`.`manufacturer`,
                `pt`.`p_limit` AS `per_order_limit`,
                `pt`.`price`,
                `pt`.`product_id`,
                `pt`.`product_mode`,
                `pt`.`product_name` AS `store_product_name`,
                `pt`.`product_status`,
                `pt`.`quantity`,
                `pt`.`regular_price`,
                `pt`.`sale_type`,
                `pt`.`selling_price`,
                `pt`.`size`,
                `pt`.`sku`,
                `pt`.`special_value`,
                `pt`.`stock_quantity` AS `stock_quantity`,
                `pt`.`storeadmin_id` AS `admin_id`,
                `pt`.`tax_status`,
                `pt`.`tax_value`,
                `pt`.`upc`,
                `pt`.`upc_status_request`,
                `pt`.`vendor`
            FROM `products_temp2` `pt`
            WHERE `pt`.`upc` = :upc
                AND `pt`.`storeadmin_id` = :adminID LIMIT 1";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":upc", $upc, PDO::PARAM_STR);
        $stmt->bindValue(":adminID", $this->adminID, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->format($row);
        // return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     /**
     * Checks if UPC exists in products_temp2 table.
     * @param object $ctx Context.
     * @param string $upc UPC.
     * @return bool Exists or not.
     */
    public function upcExists($ctx, $upc)
    {
        $selectSQL = "SELECT EXISTS(
            SELECT * FROM `products_temp2`
            WHERE `upc` = :UPC AND storeadmin_id = :adminID
        ) AS `upc_found`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":UPC", $upc, PDO::PARAM_STR);
        $stmt->bindValue(":adminID", $this->adminID, PDO::PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

}

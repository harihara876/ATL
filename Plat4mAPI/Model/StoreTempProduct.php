<?php

namespace Plat4mAPI\Model;

use PDO;

class StoreTempProduct
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
    public function format(&$product)
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
            "updated_on"            => (string) $product["updated_on"],
            "multi_item_price_one"  => (float) $product["multi_item_price_one"],
            "multi_item_qty_one"    => (int) $product["multi_item_qty_one"],
            "multi_item_price_two"  => (float) $product["multi_item_price_two"],
            "multi_item_qty_two"    => (int) $product["multi_item_qty_two"],
            "multi_item_price_three"=> (float) $product["multi_item_price_three"],
            "multi_item_qty_three"  => (int) $product["multi_item_qty_three"],
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
     * Format multiple product info.
     * @param array $products Products info.
     * @return array Formatted products.
     */
    public function formatMultiple(&$products)
    {
        $formattedProducts = [];

        foreach ($products as $product) {
            $formattedProducts[] = $this->format($product);
        }

        return $formattedProducts;
    }

    /**
     * Create product in temporary table.
     * @param object $ctx Context.
     * @param array $product Product info.
     * @return int Last insert ID.
     */
    public function create($ctx, $product)
    {
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
                `created_on`,
                `modified_on`,
                -- `upc_status_request`,
                `storeadmin_id`,
                `multi_item_qty_one`,
                `multi_item_price_one`,
                `multi_item_qty_two`,
                `multi_item_price_two`,
                `multi_item_qty_three`,
                `multi_item_price_three`,
                `discount_percent`,
                `discount_pretax`,
                `discount_posttax`
            ) VALUES (
                :name,
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
                :created_on,
                :modified_on,
                -- :upc_status_request,
                :storeadmin_id,
                :multi_item_qty_one,
                :multi_item_price_one,
                :multi_item_qty_two,
                :multi_item_price_two,
                :multi_item_qty_three,
                :multi_item_price_three,
                :discount_percent,
                :discount_pretax,
                :discount_posttax
            )";
        $stmt = $ctx->db->prepare($insertSQL);
        $stmt->bindValue(":name", $product["name"]);
        $stmt->bindValue(":product_id", $product["product_id"]);
        $stmt->bindValue(":cat_id", $product["category_id"]);
        $stmt->bindValue(":description", $product["description"]);
        $stmt->bindValue(":price", $product["price"]);
        $stmt->bindValue(":selling_price", $product["selling_price"]);
        $stmt->bindValue(":color", $product["color"]);
        $stmt->bindValue(":size", $product["size"]);
        $stmt->bindValue(":product_status", $product["product_status"]);
        $stmt->bindValue(":quantity", $product["quantity"]);
        $stmt->bindValue(":date", $product["created_on"]);
        $stmt->bindValue(":p_limit", $product["per_order_limit"]);
        $stmt->bindValue(":upc", $product["upc"]);
        $stmt->bindValue(":regular_price", $product["regular_price"]);
        $stmt->bindValue(":buying_price", $product["buying_price"]);
        $stmt->bindValue(":tax_status", $product["tax_status"]);
        $stmt->bindValue(":tax_value", $product["tax_value"]);
        $stmt->bindValue(":special_value", $product["special_value"]);
        $stmt->bindValue(":category_id", $product["category_id"]);
        $stmt->bindValue(":category_type", $product["subcategory_id"]);
        $stmt->bindValue(":date_created", $product["created_on"]);
        $stmt->bindValue(":sku", $product["sku"]);
        $stmt->bindValue(":image", $product["image_url"]);
        $stmt->bindValue(":stock_quantity", $product["stock_quantity"]);
        $stmt->bindValue(":manufacturer", $product["manufacturer"]);
        $stmt->bindValue(":brand", $product["brand"]);
        $stmt->bindValue(":vendor", $product["vendor"]);
        $stmt->bindValue(":product_mode", $product["product_mode"]);
        $stmt->bindValue(":age_restriction", $product["age_restriction"]);
        $stmt->bindValue(":sale_type", $product["sale_type"]);
        $stmt->bindValue(":created_on", $product["created_on"]);
        $stmt->bindValue(":modified_on", $product["updated_on"]);
        // $stmt->bindValue(":upc_status_request", $product["upc_status_request"]);
        $stmt->bindValue(":storeadmin_id", $product["admin_id"]);
        $stmt->bindValue(":multi_item_qty_one", $product["multi_item_qty_one"]);
        $stmt->bindValue(":multi_item_price_one", $product["multi_item_price_one"]);
        $stmt->bindValue(":multi_item_qty_two", $product["multi_item_qty_two"]);
        $stmt->bindValue(":multi_item_price_two", $product["multi_item_price_two"]);
        $stmt->bindValue(":multi_item_qty_three", $product["multi_item_qty_three"]);
        $stmt->bindValue(":multi_item_price_three", $product["multi_item_price_three"]);
        $stmt->bindValue(":discount_percent", $product["discount_percent"]);
        $stmt->bindValue(":discount_pretax", $product["discount_pretax"]);
        $stmt->bindValue(":discount_posttax", $product["discount_posttax"]);
        $stmt->execute();

        return $ctx->db->lastInsertId();
    }

    /**
     * Fetch all temporary products by admin ID.
     * @param object $ctx Context.
     * @return array Products list.
     */
    public function getAll($ctx)
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
                `pt`.`modified_on` AS `updated_on`,
                `pt`.`multi_item_price_one`,
                `pt`.`multi_item_qty_one`,
                `pt`.`multi_item_price_two`,
                `pt`.`multi_item_qty_two`,
                `pt`.`multi_item_price_three`,
                `pt`.`multi_item_qty_three`,
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
            FROM `products_temp` `pt`
            LEFT JOIN `ordered_product` `op`
                ON `pt`.`product_id` = `op`.`product_id`
            WHERE `pt`.`storeadmin_id` = :adminID AND `pt`.`product_status` != :deleted GROUP BY `pt`.`product_id`
            ORDER BY `pt`.`id` ASC";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":adminID", $this->adminID, PDO::PARAM_INT);
        $stmt->bindValue(":deleted", "InActive");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->formatMultiple($rows);
    }

    /**
     * Fetch temporary product by UPC.
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
                `pt`.`modified_on` AS `updated_on`,
                `pt`.`multi_item_qty_one`,
                `pt`.`multi_item_price_one`,
                `pt`.`multi_item_qty_two`,
                `pt`.`multi_item_price_two`,
                `pt`.`multi_item_qty_three`,
                `pt`.`multi_item_price_three`,
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
            FROM `products_temp` `pt`
            WHERE `pt`.`upc` = :upc
                AND `pt`.`storeadmin_id` = :adminID LIMIT 1";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":upc", $upc, PDO::PARAM_STR);
        $stmt->bindValue(":adminID", $this->adminID, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->format($row);
    }

    /**
     * Checks if UPC exists in products_temp table.
     * @param object $ctx Context.
     * @param string $upc UPC.
     * @return bool Exists or not.
     */
    public function upcExists($ctx, $upc)
    {
        $selectSQL = "SELECT EXISTS(
            SELECT * FROM `products_temp`
            WHERE `upc` = :UPC AND storeadmin_id = :adminID
        ) AS `upc_found`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":UPC", $upc, PDO::PARAM_STR);
        $stmt->bindValue(":adminID", $this->adminID, PDO::PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
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
            FROM `products_temp`
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
     * Checks if Product exists in product_temp table.
     * @param object $ctx Context.
     * @param string $product_id.
     * @return bool Exists or not.
     */
    public function productExists($ctx, $productId): bool
    {
        $selectSQL = "SELECT EXISTS(
                SELECT * FROM `products_temp`
                WHERE product_id = :product_id
            ) AS `product_not_found`";
        $stmt= $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":product_id", $productId, PDO:: PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

     /**
     * Fetch product info by ProductId from Temp products repository.
     * @param object $ctx Context.
     * @param int $productID Product ID.
     * @return mixed Product info if exists. Else FALSE.
     */
    public function getInfoByProductID($ctx, $productID)
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
                `pt`.`modified_on` AS `updated_on`,
                `pt`.`multi_item_qty_one`,
                `pt`.`multi_item_price_one`,
                `pt`.`multi_item_qty_two`,
                `pt`.`multi_item_price_two`,
                `pt`.`multi_item_qty_three`,
                `pt`.`multi_item_price_three`,
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
            FROM `products_temp` `pt`
            WHERE `pt`.`product_id` = :productID
            AND `pt`.`storeadmin_id` = :adminID AND `pt`.`product_status` != :deleted
            LIMIT 1";

        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":productID", $productID, PDO::PARAM_STR);
        $stmt->bindValue(":adminID", $this->adminID, PDO::PARAM_INT);
        $stmt->bindValue(":deleted", "InActive");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->format($row);
    }

    /**
     * Update Name of a Temp product.
     * @param object $ctx Context.
     * @param int $productID Product ID. e.g 100034556
     * @param string $data.
     * @return int Number of updated rows.
     */
    public function updateDescription($ctx, $data)
    {
        $updateSQL = "UPDATE `products_temp`
            SET `Stock_Quantity` = :stock_quantity,
                `product_name` = :name,
                `regular_price` = :regular_price,
                `modified_on` = :updatedOn
            WHERE `product_id` = :productID
            AND `storeadmin_id` = :adminID";
        $stmt = $ctx->db->prepare($updateSQL);
        $stmt->bindValue(":productID", $data["product_id"]);
        $stmt->bindValue(":updatedOn", $ctx->now);
        $stmt->bindValue(":adminID", $this->adminID);
        $stmt->bindValue(":stock_quantity", $data["stockQty"]);
        $stmt->bindValue(":regular_price", $data["regularPrice"]);
        $stmt->bindValue(":name", $data["name"]);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Update price of a product.
     * @param object $ctx Context.
     * @param int $productID Product ID. e.g 100034556
     * @param object $priceInfo Price info.
     * @return int Number of updated rows.
     */
    public function updatePrice($ctx, $productID, $priceInfo)
    {
        $updateSQL = "UPDATE `products_temp`
            SET `regular_Price` = :regular_price,
                `multi_item_qty_one` = :multi_item_qty_one,
                `multi_item_price_one` = :multi_item_price_one,
                `multi_item_qty_two` = :multi_item_qty_two,
                `multi_item_price_two` = :multi_item_price_two,
                `multi_item_qty_three` = :multi_item_qty_three,
                `multi_item_price_three` = :multi_item_price_three,
                `discount_percent` = :discount_percent,
                `discount_pretax` = :discount_pretax,
                `discount_posttax` = :discount_posttax,
                `stock_quantity` = :stock_quantity,
                `buying_price` = :buying_price,
                `tax_value` = :tax_value,
                `tax_status` = :tax_status,
                `modified_on` = :updatedOn
            WHERE `product_id` = :productID
            AND `storeadmin_id` = :adminID";
        $stmt = $ctx->db->prepare($updateSQL);
        $stmt->bindValue(":regular_price", $priceInfo->regular_price);
        $stmt->bindValue(":multi_item_qty_one", $priceInfo->multi_item_qty_one);
        $stmt->bindValue(":multi_item_price_one", $priceInfo->multi_item_price_one);
        $stmt->bindValue(":multi_item_qty_two", $priceInfo->multi_item_qty_two);
        $stmt->bindValue(":multi_item_price_two", $priceInfo->multi_item_price_two);
        $stmt->bindValue(":multi_item_qty_three", $priceInfo->multi_item_qty_three);
        $stmt->bindValue(":multi_item_price_three", $priceInfo->multi_item_price_three);
        $stmt->bindValue(":discount_percent", $priceInfo->discount_percent);
        $stmt->bindValue(":discount_pretax", $priceInfo->discount_pretax);
        $stmt->bindValue(":discount_posttax", $priceInfo->discount_posttax);
        $stmt->bindValue(":stock_quantity", $priceInfo->stock_quantity);
        $stmt->bindValue(":buying_price", $priceInfo->buying_price);
        $stmt->bindValue(":updatedOn", $ctx->now);
        $stmt->bindValue(":productID", $productID);
        $stmt->bindValue(":adminID", $this->adminID);
        $stmt->bindValue(":tax_value", $priceInfo->tax_value);
        $stmt->bindValue(":tax_status", $priceInfo->tax_status);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Delete product.
     * @param object $ctx Context.
     * @param int $productID Product ID. e.g 100034556
     * @param string $description Description.
     * @return int Number of updated rows.
     */
    public function deleteProduct($ctx, $data)
    {
        $updateSQL = "UPDATE `products_temp`
            SET `product_status` = :product_status,
                `modified_on` = :updatedOn
            WHERE `product_id` = :productID
            AND `storeadmin_id` = :adminID AND `product_status` != :deleted";
        $stmt = $ctx->db->prepare($updateSQL);
        $stmt->bindValue(":product_status", $data["product_status"]);
        $stmt->bindValue(":updatedOn", $ctx->now);
        $stmt->bindValue(":adminID", $this->adminID);
        $stmt->bindValue(":productID", $data["product_id"]);
        $stmt->bindValue(":deleted", "InActive");
        $stmt->execute();

        return $stmt->rowCount();
    }

}

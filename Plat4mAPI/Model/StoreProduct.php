<?php

namespace Plat4mAPI\Model;

use PDO;

class StoreProduct
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
     * Format product info.
     * @param array $product Product info.
     * @return array Formatted product.
     */
    public function format(&$product)
    {
        if (!$product) {
            return NULL;
        }
        
        return [
            "id"                    => (int) $product["id"],
            "product_id"            => (int) $product["product_id"],
            //"name"                  => (string) $product["name"],
            "global_product_name"   => (string) $product["global_product_name"],
            "store_product_name"    => (string) $product["store_product_name"],
            "upc"                   => (string) $product["upc"],
            "category_id"           => (int) $product["category_id"],
            "subcategory_id"        => (int) $product["subcategory_id"],
            "image_url"             => (string) $product["image_url"],
            "brand"                 => (string) $product["brand"],
            "manufacturer"          => (string) $product["manufacturer"],
            "vendor"                => (string) $product["vendor"],
            "description"           => (string) $product["description"],
            "pos_description"       => (string) $product["pos_description"],
            "color"                 => (string) $product["color"],
            "size"                  => (string) $product["size"],
            "sku"                   => (int) $product["sku"],
            "quantity"              => (int) $product["quantity"],
            "stock_quantity"        => (int) $product["stock_quantity"],
            "age_restriction"       => (int) $product["age_restriction"],
            "sale_type"             => (string) $product["sale_type"],
            "price"                 => (float) $product["price"],
            "regular_price"         => (float) $product["regular_price"],
            "buying_price"          => (float) $product["buying_price"],
            "selling_price"         => (float) $product["selling_price"],
            "special_value"         => (float) $product["special_value"],
            "tax_status"            => (string) $product["tax_status"],
            "tax_value"             => (float) $product["tax_value"],
            // "admin_tax_value"       => (float) $product["admin_tax_value"],
            "multi_item_qty_one"    => (int) $product["multi_item_qty_one"],
            "multi_item_price_one"  => (float) $product["multi_item_price_one"],
            "multi_item_qty_two"    => (int) $product["multi_item_qty_two"],
            "multi_item_price_two"  => (float) $product["multi_item_price_two"],
            "multi_item_qty_three"  => (int) $product["multi_item_qty_three"],
            "multi_item_price_three"=> (float) $product["multi_item_price_three"],
            "discount_percent"      => (int) $product["discount_percent"],
            "discount_pretax"       => (int) $product["discount_pretax"],
            "discount_posttax"      => (int) $product["discount_posttax"],
            "per_order_limit"       => (int) $product["per_order_limit"],
            "product_mode"          => (int) $product["product_mode"],
            "admin_id"              => (int) $product["admin_id"],
            "status"                => (int) $product["status"],
            "product_status"        => (string) $product["product_status"],
            "created_on"            => (string) $product["created_on"],
            "updated_on"            => (string) $product["updated_on"],
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
     * Fetch all products by admin ID.
     * @param object $ctx Context.
     * @return array Products list.
     */
    public function getAll($ctx)
    {
        $selectSQL = "SELECT
                `pd`.`id`,
                `pd`.`product_id`,
                `p`.`product_name` AS `global_product_name`,
                `p`.`UPC` AS `upc`,
                `p`.`Category_Id` AS `category_id`,
                `p`.`Category_Type` AS `subcategory_id`,
                `pi`.`Image` AS `image_url`,
                `p`.`Brand` AS `brand`,
                `p`.`Manufacturer` AS `manufacturer`,
                `p`.`Vendor` AS `vendor`,
                `pd`.`description`,
                `pd`.`product_name` AS `store_product_name`,
                `pd`.`POS_description` AS `pos_description`,
                `pd`.`color`,
                `pd`.`size`,
                `pd`.`SKU` AS `sku`,
                `pd`.`quantity`,
                `pd`.`Stock_Quantity` AS `stock_quantity`,
                `pd`.`Age_Restriction` AS `age_restriction`,
                `pd`.`sale_type`,
                `pd`.`price`,
                `pd`.`Regular_Price` AS `regular_price`,
                `pd`.`Buying_Price` AS `buying_price`,
                `pd`.`sellprice` AS `selling_price`,
                `pd`.`Special_Value` AS `special_value`,
                `pd`.`Tax_Status` AS `tax_status`,
                `pd`.`Tax_Value` AS `tax_value`,
                -- `a`.`tax` AS `admin_tax_value`,
                `pd`.`multi_item_qty_one`,
                `pd`.`multi_item_price_one`,
                `pd`.`multi_item_qty_two`,
                `pd`.`multi_item_price_two`,
                `pd`.`multi_item_qty_three`,
                `pd`.`multi_item_price_three`,
                `pd`.`discount_percent`,
                `pd`.`discount_pretax`,
                `pd`.`discount_posttax`,
                `pd`.`plimit` AS `per_order_limit`,
                `pd`.`ProductMode` AS `product_mode`,
                `pd`.`storeadmin_id` AS `admin_id`,
                `pd`.`status`,
                `pd`.`product_status`,
                `pd`.`created_on`,
                `pd`.`updated_on`
            FROM `products` `p`
            LEFT JOIN `product_details` `pd`
                ON `pd`.`product_id` = `p`.`product_id`
            LEFT JOIN `admin` `a`
                ON `a`.`admin_id` = `pd`.`storeadmin_id`
            LEFT JOIN `product_images` `pi`
                ON `pd`.`product_id` = `pi`.`product_id`
            WHERE `pd`.`storeadmin_id` = :adminID AND `pd`.`product_status` != :deleted GROUP BY `p`.`product_id`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":adminID", $this->adminID, PDO::PARAM_INT);
        $stmt->bindValue(":deleted", "InActive");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->formatMultiple($rows);
    }

    /**
     * Fetch product info by UPC from products repository.
     * @param object $ctx Context.
     * @param string $upc UPC.
     * @return mixed Product info if exists. Else FALSE.
     */
    public function getInfoByUPC($ctx, $upc)
    {
        $selectSQL = "SELECT
                `pd`.`id`,
                `pd`.`product_id`,
                `p`.`product_name` AS `global_product_name`,
                `p`.`UPC` AS `upc`,
                `p`.`Category_Id` AS `category_id`,
                `p`.`Category_Type` AS `subcategory_id`,
                `p`.`Image` AS `image_url`,
                `p`.`Brand` AS `brand`,
                `p`.`Manufacturer` AS `manufacturer`,
                `p`.`Vendor` AS `vendor`,
                `pd`.`description`,
                `pd`.`product_name` AS `store_product_name`,
                `pd`.`POS_description` AS `pos_description`,
                `pd`.`color`,
                `pd`.`size`,
                `pd`.`SKU` AS `sku`,
                `pd`.`quantity`,
                `pd`.`Stock_Quantity` AS `stock_quantity`,
                `pd`.`Age_Restriction` AS `age_restriction`,
                `pd`.`sale_type`,
                `pd`.`price`,
                `pd`.`Regular_Price` AS `regular_price`,
                `pd`.`Buying_Price` AS `buying_price`,
                `pd`.`sellprice` AS `selling_price`,
                `pd`.`Special_Value` AS `special_value`,
                `pd`.`Tax_Status` AS `tax_status`,
                `pd`.`Tax_Value` AS `tax_value`,
                -- `a`.`tax` AS `admin_tax_value`,
                `pd`.`multi_item_qty_one`,
                `pd`.`multi_item_price_one`,
                `pd`.`multi_item_qty_two`,
                `pd`.`multi_item_price_two`,
                `pd`.`multi_item_qty_three`,
                `pd`.`multi_item_price_three`,
                `pd`.`discount_percent`,
                `pd`.`discount_pretax`,
                `pd`.`discount_posttax`,
                `pd`.`plimit` AS `per_order_limit`,
                `pd`.`ProductMode` AS `product_mode`,
                `pd`.`storeadmin_id` AS `admin_id`,
                `pd`.`status`,
                `pd`.`product_status`,
                `pd`.`created_on`,
                `pd`.`updated_on`
            FROM `products` `p`
            JOIN `product_details` `pd`
                ON `pd`.`product_id` = `p`.`product_id`
            LEFT JOIN `admin` `a`
                ON `a`.`admin_id` = `pd`.`storeadmin_id`
            WHERE `p`.`UPC` = :upc
                AND `pd`.`storeadmin_id` = :adminID
            LIMIT 1";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":upc", $upc, PDO::PARAM_STR);
        $stmt->bindValue(":adminID", $this->adminID, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->format($row);
    }

    /**
     * Fetch product info by ProductId from products repository.
     * @param object $ctx Context.
     * @param int $productID Product ID.
     * @return mixed Product info if exists. Else FALSE.
     */
    public function getInfoByProductID($ctx, $productID)
    {
        $selectSQL = "SELECT
                `pd`.`id`,
                `pd`.`product_id`,
                `p`.`product_name` AS `global_product_name`,
                `p`.`UPC` AS `upc`,
                `p`.`Category_Id` AS `category_id`,
                `p`.`Category_Type` AS `subcategory_id`,
                `p`.`Image` AS `image_url`,
                `p`.`Brand` AS `brand`,
                `p`.`Manufacturer` AS `manufacturer`,
                `p`.`Vendor` AS `vendor`,
                `pd`.`description`,
                `pd`.`product_name` AS `store_product_name`,
                `pd`.`POS_description` AS `pos_description`,
                `pd`.`color`,
                `pd`.`size`,
                `pd`.`SKU` AS `sku`,
                `pd`.`quantity`,
                `pd`.`Stock_Quantity` AS `stock_quantity`,
                `pd`.`Age_Restriction` AS `age_restriction`,
                `pd`.`sale_type`,
                `pd`.`price`,
                `pd`.`Regular_Price` AS `regular_price`,
                `pd`.`Buying_Price` AS `buying_price`,
                `pd`.`sellprice` AS `selling_price`,
                `pd`.`Special_Value` AS `special_value`,
                `pd`.`Tax_Status` AS `tax_status`,
                `pd`.`Tax_Value` AS `tax_value`,
                -- `a`.`tax` AS `admin_tax_value`,
                `pd`.`multi_item_qty_one`,
                `pd`.`multi_item_price_one`,
                `pd`.`multi_item_qty_two`,
                `pd`.`multi_item_price_two`,
                `pd`.`multi_item_qty_three`,
                `pd`.`multi_item_price_three`,
                `pd`.`discount_percent`,
                `pd`.`discount_pretax`,
                `pd`.`discount_posttax`,
                `pd`.`plimit` AS `per_order_limit`,
                `pd`.`ProductMode` AS `product_mode`,
                `pd`.`storeadmin_id` AS `admin_id`,
                `pd`.`status`,
                `pd`.`product_status`,
                `pd`.`created_on`,
                `pd`.`updated_on`
            FROM `products` `p`
            JOIN `product_details` `pd`
                ON `pd`.`product_id` = `p`.`product_id`
            LEFT JOIN `admin` `a`
                ON `a`.`admin_id` = `pd`.`storeadmin_id`
            WHERE `pd`.`product_id` = :productID
                AND `pd`.`storeadmin_id` = :adminID AND `pd`.`product_status` != :deleted
            LIMIT 1";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":productID", $productID, PDO::PARAM_INT);
        $stmt->bindValue(":adminID", $this->adminID, PDO::PARAM_INT);
        $stmt->bindValue(":deleted", "InActive");
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Checks if UPC exists in products table by store ID.
     * @param object $ctx Context.
     * @param string $upc UPC.
     * @return bool Exists or not.
     * @throws Exception
     */
    public function upcExists($ctx, $upc)
    {
        $selectSQL = "SELECT EXISTS(
            SELECT *
            FROM `products` `p`
            LEFT JOIN `product_details` `pd`
                ON `pd`.`product_id` = `p`.`product_id`
            WHERE `UPC` = :UPC
            AND `storeadmin_id` = :adminID
        ) AS `upc_found`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":UPC", $upc, PDO::PARAM_STR);
        $stmt->bindValue(":adminID", $this->adminID, PDO::PARAM_STR);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Update description of a product.
     * @param object $ctx Context.
     * @param int $productID Product ID. e.g 100034556
     * @param string $description Description.
     * @return int Number of updated rows.
     */
    public function updateDescription($ctx, $data)
    {
        $updateSQL = "UPDATE `product_details`
            SET `product_name` = :store_product_name,
                `Stock_Quantity` = :stock_quantity,
                `Regular_Price` = :regular_price,
                `updated_on` = :updatedOn
            WHERE `product_id` = :productID
            AND `storeadmin_id` = :adminID";
        $stmt = $ctx->db->prepare($updateSQL);
        $stmt->bindValue(":store_product_name", $data["name"]);
        $stmt->bindValue(":productID", $data["product_id"]);
        $stmt->bindValue(":updatedOn", $ctx->now);
        $stmt->bindValue(":adminID", $this->adminID);
        $stmt->bindValue(":stock_quantity", $data["stockQty"]);
        $stmt->bindValue(":regular_price", $data["regularPrice"]);
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
        $updateSQL = "UPDATE `product_details`
            SET `Regular_Price` = :regular_price,
                `multi_item_qty_one` = :multi_item_qty_one,
                `multi_item_price_one` = :multi_item_price_one,
                `multi_item_qty_two` = :multi_item_qty_two,
                `multi_item_price_two` = :multi_item_price_two,
                `multi_item_qty_three` = :multi_item_qty_three,
                `multi_item_price_three` = :multi_item_price_three,
                `discount_percent` = :discount_percent,
                `discount_pretax` = :discount_pretax,
                `discount_posttax` = :discount_posttax,
                `Stock_Quantity` = :stock_quantity,
                `Buying_Price` = :buying_price,
                `tax_value` = :tax_value,
                `tax_status` = :tax_status,
                `updated_on` = :updatedOn
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
     * Checks if Product exists in product_details table.
     * @param object $ctx Context.
     * @param string $product_id.
     * @return bool Exists or not.
     */
    public function productExists($ctx, $productId): bool
    {
        $selectSQL = "SELECT EXISTS(
                SELECT * FROM `product_details`
                WHERE product_id = :product_id
            ) AS `product_not_found`";
        $stmt= $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":product_id", $productId, PDO:: PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Fetch all products by Store admin ID & Exclude MY_STORE Category(id-:40).
     * @param object $ctx Context.
     * @return array Products list.
     */
    public function getStoreProduct($ctx)
    {
        $selectSQL = "SELECT
                `pd`.`id`,
                `pd`.`product_id`,
                `p`.`product_name` AS `global_product_name`,
                `p`.`UPC` AS `upc`,
                `p`.`Category_Id` AS `category_id`,
                `p`.`Category_Type` AS `subcategory_id`,
                `pi`.`Image` AS `image_url`,
                `p`.`Brand` AS `brand`,
                `p`.`Manufacturer` AS `manufacturer`,
                `p`.`Vendor` AS `vendor`,
                `pd`.`description`,
                `pd`.`product_name` AS `store_product_name`,
                `pd`.`POS_description` AS `pos_description`,
                `pd`.`color`,
                `pd`.`size`,
                `pd`.`SKU` AS `sku`,
                `pd`.`quantity`,
                `pd`.`Stock_Quantity` - SUM(`op`.quantity) AS `stock_quantity`,
                `pd`.`Age_Restriction` AS `age_restriction`,
                `pd`.`sale_type`,
                `pd`.`price`,
                `pd`.`Regular_Price` AS `regular_price`,
                `pd`.`Buying_Price` AS `buying_price`,
                `pd`.`sellprice` AS `selling_price`,
                `pd`.`Special_Value` AS `special_value`,
                `pd`.`Tax_Status` AS `tax_status`,
                `pd`.`Tax_Value` AS `tax_value`,
                -- `a`.`tax` AS `admin_tax_value`,
                `pd`.`multi_item_qty_one`,
                `pd`.`multi_item_price_one`,
                `pd`.`multi_item_qty_two`,
                `pd`.`multi_item_price_two`,
                `pd`.`multi_item_qty_three`,
                `pd`.`multi_item_price_three`,
                `pd`.`discount_percent`,
                `pd`.`discount_pretax`,
                `pd`.`discount_posttax`,
                `pd`.`plimit` AS `per_order_limit`,
                `pd`.`ProductMode` AS `product_mode`,
                `pd`.`storeadmin_id` AS `admin_id`,
                `pd`.`status`,
                `pd`.`product_status`,
                `pd`.`created_on`,
                `pd`.`updated_on`
            FROM `products` `p`
            LEFT JOIN `product_details` `pd`
                ON `pd`.`product_id` = `p`.`product_id`
            LEFT JOIN `admin` `a`
                ON `a`.`admin_id` = `pd`.`storeadmin_id`
            LEFT JOIN `product_images` `pi`
                ON `pd`.`product_id` = `pi`.`product_id`
            LEFT JOIN `ordered_product` `op`
                ON `pd`.`product_id` = `op`.`product_id`
            WHERE `pd`.`storeadmin_id` = :adminID AND `p`.`Category_Id` != 40 GROUP BY `p`.`product_id`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":adminID", $this->adminID, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->formatMultiple($rows);
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
        $updateSQL = "UPDATE `product_details`
            SET `product_status` = :product_status,
                `updated_on` = :updatedOn
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

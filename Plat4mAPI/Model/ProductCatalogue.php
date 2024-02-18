<?php

namespace Plat4mAPI\Model;

use PDO;

class ProductCatalogue
{
    // Super admin ID.
    private int $superAdminID = 1;

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
            // "category_name"                  => (string) $product["category_name"],
            "global_product_name"   => (string) $product["global_product_name"],
            "store_product_name"    => (string) $product["store_product_name"],
            "upc"                   => (string) $product["upc"],
            "category_id"           => (int) $product["category_id"],
            "subcategory_id"        => (int) $product["subcategory_id"],
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
            // "product_tax_value"     => (float) $product["product_tax_value"],
            "tax_value"             => (float) $product["tax_value"],
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
     * Create product.
     * @param object $ctx Context.
     * @param array $product Product info.
     * @return int Last insert ID.
     */
    public function create($ctx, $product)
    {
        $insertSQL = "INSERT INTO `products` (
                `product_id`,
                `product_name`,
                `cat_id`,
                `POS_description`,
                `UPC`,
                `Category_Id`,
                `Category_Type`,
                `Date_Created`,
                `Image`,
                `Manufacturer`,
                `Brand`,
                `Vendor`,
                `status`,
                `created_time`
            ) VALUES (
                :product_id,
                :name,
                :category_id,
                :pos_description,
                :upc,
                :category_id_2,
                :subcategory_id,
                :created_date,
                :image,
                :manufacturer,
                :brand,
                :vendor,
                :status,
                :created_time
            )";
        $stmt = $ctx->db->prepare($insertSQL);
        $stmt->bindValue(":product_id", $product["product_id"]);
        $stmt->bindValue(":name", $product["name"]);
        $stmt->bindValue(":category_id", $product["category_id"]);
        $stmt->bindValue(":pos_description", $product["pos_description"]);
        $stmt->bindValue(":upc", $product["upc"]);
        $stmt->bindValue(":category_id_2", $product["category_id"]);
        $stmt->bindValue(":subcategory_id", $product["subcategory_id"]);
        $stmt->bindValue(":created_date", $product["created_date"]);
        $stmt->bindValue(":image", $product["image"]);
        $stmt->bindValue(":manufacturer", $product["manufacturer"]);
        $stmt->bindValue(":brand", $product["brand"]);
        $stmt->bindValue(":vendor", $product["vendor"]);
        $stmt->bindValue(":status", $product["status"]);
        $stmt->bindValue(":created_time", $product["created_time"]);
        $stmt->execute();

        return $ctx->db->lastInsertId();
    }

    /**
     * Create product details.
     * @param object $ctx Context.
     * @param array $details Product details.
     * @return int Last insert ID.
     */
    public function createDetails($ctx, $details)
    {
        $insertSQL = "INSERT INTO `product_details` (
                `product_id`,
                `description`,
                `product_name`,
                `POS_description`,
                `price`,
                `sellprice`,
                `color`,
                `size`,
                `product_status`,
                `quantity`,
                `plimit`,
                `Regular_Price`,
                `Buying_Price`,
                `Tax_Status`,
                `Tax_Value`,
                `Special_Value`,
                `Date_Created`,
                `SKU`,
                `Stock_Quantity`,
                `ProductMode`,
                `Age_Restriction`,
                `sale_type`,
                `status`,
                `storeadmin_id`,
                `created_date_on`,
                `multi_item_qty_one`,
                `multi_item_price_one`,
                `multi_item_qty_two`,
                `multi_item_price_two`,
                `multi_item_qty_three`,
                `multi_item_price_three`,
                `discount_percent`,
                `discount_pretax`,
                `discount_posttax`,
                `created_on`,
                `updated_on`
            ) VALUES (
                :product_id,
                :description,
                :product_name,
                :pos_description,
                :price,
                :selling_price,
                :color,
                :size,
                :product_status,
                :quantity,
                :per_order_limit,
                :regular_price,
                :buying_price,
                :tax_status,
                :tax_value,
                :special_value,
                :Date_Created,
                :sku,
                :stock_quantity,
                :product_mode,
                :age_restriction,
                :sale_type,
                :status,
                :admin_id,
                :created_date_on,
                :multi_item_qty_one,
                :multi_item_price_one,
                :multi_item_qty_two,
                :multi_item_price_two,
                :multi_item_qty_three,
                :multi_item_price_three,
                :discount_percent,
                :discount_pretax,
                :discount_posttax,
                :created_on,
                :updated_on
            )";
        $stmt = $ctx->db->prepare($insertSQL);
        $stmt->bindValue(":product_id", $details["product_id"]);
        $stmt->bindValue(":description", $details["description"]);
        $stmt->bindValue(":product_name", $details["global_product_name"]);
        $stmt->bindValue(":pos_description", $details["pos_description"]);
        $stmt->bindValue(":price", $details["price"]);
        $stmt->bindValue(":selling_price", $details["selling_price"]);
        $stmt->bindValue(":color", $details["color"]);
        $stmt->bindValue(":size", $details["size"]);
        $stmt->bindValue(":product_status", $details["product_status"]);
        $stmt->bindValue(":quantity", $details["quantity"]);
        $stmt->bindValue(":per_order_limit", $details["per_order_limit"]);
        $stmt->bindValue(":regular_price", $details["regular_price"]);
        $stmt->bindValue(":buying_price", $details["buying_price"]);
        $stmt->bindValue(":tax_status", $details["tax_status"]);
        $stmt->bindValue(":tax_value", $details["tax_value"]);
        $stmt->bindValue(":special_value", $details["special_value"]);
        $stmt->bindValue(":Date_Created", $details["created_on"]);
        $stmt->bindValue(":sku", $details["sku"]);
        $stmt->bindValue(":stock_quantity", $details["stock_quantity"]);
        $stmt->bindValue(":product_mode", $details["product_mode"]);
        $stmt->bindValue(":age_restriction", $details["age_restriction"]);
        $stmt->bindValue(":sale_type", $details["sale_type"]);
        $stmt->bindValue(":status", $details["status"]);
        $stmt->bindValue(":admin_id", $details["admin_id"]);
        $stmt->bindValue(":created_date_on", $details["created_on"]);
        $stmt->bindValue(":multi_item_qty_one", $details["multi_item_qty_one"]);
        $stmt->bindValue(":multi_item_price_one", $details["multi_item_price_one"]);
        $stmt->bindValue(":multi_item_qty_two", $details["multi_item_qty_two"]);
        $stmt->bindValue(":multi_item_price_two", $details["multi_item_price_two"]);
        $stmt->bindValue(":multi_item_qty_three", $details["multi_item_qty_three"]);
        $stmt->bindValue(":multi_item_price_three", $details["multi_item_price_three"]);
        $stmt->bindValue(":discount_percent", $details["discount_percent"]);
        $stmt->bindValue(":discount_pretax", $details["discount_pretax"]);
        $stmt->bindValue(":discount_posttax", $details["discount_posttax"]);
        $stmt->bindValue(":created_on", $details["created_on"]);
        $stmt->bindValue(":updated_on", $details["updated_on"]);
        $stmt->execute();

        return $ctx->db->lastInsertId();
    }

    /**
     * Fetch product info by UPC from products repository.
     * @param object $ctx Context.
     * @param string $upc UPC.
     * @return array Product info if exists. Else NULL.
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
                `p`.`Brand` AS `brand`,
                `p`.`Manufacturer` AS `manufacturer`,
                `p`.`Vendor` AS `vendor`,
                `pd`.`description`,
                `pd`.`product_name` AS `store_product_name`,
                `pd`.`POS_description` AS `pos_description`,
                `pd`.`status`,
                `pd`.`product_status`,
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
                -- `a`.`tax` AS `tax_value`,
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
                `pd`.`Date_Created` AS `store_date_created`,
                `pd`.`ProductMode` AS `product_mode`,
                `pd`.`storeadmin_id` AS `admin_id`,
                `pd`.`created_on`,
                `pd`.`updated_on`
            FROM `products` `p`
            JOIN `product_details` `pd`
                ON `pd`.`product_id` = `p`.`product_id`
            LEFT JOIN `admin` `a`
                ON `a`.`admin_id` = `pd`.`storeadmin_id`
            WHERE `p`.`UPC` = :upc
            AND `pd`.`storeadmin_id` = :superAdminID
            LIMIT 1";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":upc", $upc, PDO::PARAM_STR);
        $stmt->bindValue(":superAdminID", $this->superAdminID, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Checks if UPC exists in products repository.
     * @param object $ctx Context.
     * @param string $upc UPC.
     * @return bool Status.
     */
    public function upcExists($ctx, $upc): bool
    {
        $selectSQL = "SELECT EXISTS(
            SELECT * FROM `products`
            JOIN `product_details`
                ON `product_details`.`product_id` = `products`.`product_id`
            WHERE `product_details`.`storeadmin_id` = :superAdminID
            AND `products`.`UPC` = :upc
        ) AS `upc_found`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":superAdminID", $this->superAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":upc", $upc, PDO::PARAM_STR);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Generates product ID.
     * TODO: VERY BAD Code. Replace product_id with UUID.
     * @param object $ctx Context.
     * @param string $upc UPC.
     * @return int Product ID.
     */
    public function generateProductID($ctx)
    {
        $selectSQL = "SELECT IFNULL(MAX(`product_id`), 0) `product_id`
            FROM (
                SELECT `product_id` FROM `products`
                UNION ALL
                SELECT `product_id` FROM `products_temp`
                UNION ALL
                SELECT `product_id` FROM `products_temp2`
            ) `a`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->execute();
        $productID = $stmt->fetchColumn();

        return $productID + 1;
    }

    /**
     * Fetches selling price range of a product across products, products_temp & products_temp2.
     * @param object $ctx Context.
     * @param string $upc UPC.
     * @return array Min & max selling prices.
     * @throws Exception
     */
    public function getSellingPriceRangeAcrossAll($ctx, $upc)
    {
        $selectSQL = "SELECT
                MIN(`price`) AS `min_price`,
                MAX(`price`) AS `max_price`
            FROM
                (
                    (
                        SELECT `sellprice` AS `price`
                        FROM `products` `p`
                        JOIN `product_details` `pd` ON `pd`.`product_id` = `p`.`product_id`
                        WHERE `p`.`UPC` = :upc1
                    )
                    UNION ALL
                    (
                        SELECT `price`
                        FROM `products_temp`
                        WHERE `upc` = :upc2
                    )
                    UNION ALL
                    (
                        SELECT `price`
                        FROM `products_temp2`
                        WHERE `upc` = :upc3
                    )
                ) AS `t1`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":upc1", $upc, PDO::PARAM_STR);
        $stmt->bindValue(":upc2", $upc, PDO::PARAM_STR);
        $stmt->bindValue(":upc3", $upc, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

     /**
     * Checks if Product exists in products table.
     * @param object $ctx Context.
     * @param string $product_id.
     * @return bool Exists or not.
     */
    public function productExists($ctx, $productId): bool
    {
        $selectSQL = "SELECT EXISTS(
                        (
                            SELECT * FROM `products` `p`
                            JOIN `product_details` `pd` ON `pd`.`product_id` = `p`.`product_id`
                            WHERE `p`.`product_id` = :product_id
                        )
                   
                ) AS `product_not_found`";
        $stmt= $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":product_id", $productId);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Checks if Product exists in products_temp table.
     * @param object $ctx Context.
     * @param string $product_id.
     * @return bool Exists or not.
     */
    public function storeProductExists($ctx, $productId): bool
    {
        $selectSQL = "SELECT EXISTS(
                        (
                            SELECT * FROM `products_temp` 
                            WHERE `product_id` = :product_id
                        )
                   
                ) AS `product_found`";
        $stmt= $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":product_id", $productId);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Checks if UPC exists in products repository.
     * @param object $ctx Context.
     * @param string $upc UPC.
     * @return bool Status.
     */
    public function upcExistsInProducts($ctx, $upc): bool
    {
        $selectSQL = "SELECT EXISTS(
            SELECT * FROM `products`
            JOIN `product_details`
                ON `product_details`.`product_id` = `products`.`product_id`
            WHERE `products`.`UPC` = :upc
        ) AS `upc_found`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":upc", $upc, PDO::PARAM_STR);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Fetch all product.
     * @param Int $catId Category_Id.
     * @return array Of MyStore Category Product.
     */
    public function getMyStoreCatProduct($ctx,$cat_id)
    {
        // TODO: Optimize query.
        $selectSQL = "SELECT `upc`
            FROM `products` `p`
            WHERE `p`.`category_id` = :id";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":id", $cat_id, PDO:: PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch product info by Category_id from products repository.
     * @param object $ctx Context.
     * @param string $upc UPC.
     * @return array Product info if exists. Else NULL.
     */
    public function getInfoByCategoryId($ctx)
    {
        $selectSQL = "SELECT
                `pd`.`id`,
                `pd`.`product_id`,
                `p`.`product_name` AS `global_product_name`,
                `p`.`UPC` AS `upc`,
                `p`.`Category_Id` AS `category_id`,
                `c`.`category_name`,
                `p`.`Category_Type` AS `subcategory_id`,
                `p`.`Brand` AS `brand`,
                `p`.`Manufacturer` AS `manufacturer`,
                `p`.`Vendor` AS `vendor`,
                `pd`.`description`,
                `pd`.`product_name` AS `store_product_name`,
                `pd`.`POS_description` AS `pos_description`,
                `pd`.`status`,
                `pd`.`product_status`,
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
                `pd`.`Date_Created` AS `store_date_created`,
                `pd`.`ProductMode` AS `product_mode`,
                `pd`.`storeadmin_id` AS `admin_id`,
                `pd`.`created_on`,
                `pd`.`updated_on`
            FROM `products` `p`
            JOIN `product_details` `pd`
                ON `pd`.`product_id` = `p`.`product_id`
            LEFT JOIN `category` `c`
                ON `c`.`cat_id` = 23
            WHERE `p`.`Category_Id` = 23 AND `pd`.`storeadmin_id` = 1
            ";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->formatMultiple($rows);

    }

    /**
     * Fetch product info by UPC from products repository for OBCR.
     * @param object $ctx Context.
     * @param string $upc UPC.
     * @return array Product info if exists. Else NULL.
     */
    public function getOBCRInfoByUPC($ctx, $upc)
    {
        $selectSQL = "SELECT
                `pd`.`id`,
                `pd`.`product_id`,
                `p`.`product_name`,
                `p`.`UPC` AS `upc`,
                `p`.`Category_Id` AS `category_id`,
                `p`.`Category_Type` AS `subcategory_id`,
                `p`.`Brand` AS `brand`,
                `p`.`Manufacturer` AS `manufacturer`,
                `p`.`Vendor` AS `vendor`,
                `pd`.`description`,
                `pd`.`product_name` AS `store_product_name`,
                `pd`.`POS_description` AS `pos_description`,
                `pd`.`status`,
                `pd`.`product_status`,
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
                `pd`.`Date_Created` AS `store_date_created`,
                `pd`.`ProductMode` AS `product_mode`,
                `pd`.`storeadmin_id` AS `admin_id`,
                `pd`.`created_on`,
                `pd`.`updated_on`
            FROM `products` `p`
            JOIN `product_details` `pd`
                ON `pd`.`product_id` = `p`.`product_id`
            LEFT JOIN `admin` `a`
                ON `a`.`admin_id` = `pd`.`storeadmin_id`
            WHERE `p`.`UPC` = :upc
            AND `pd`.`storeadmin_id` = :superAdminID
            LIMIT 1";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":upc", $upc, PDO::PARAM_STR);
        $stmt->bindValue(":superAdminID", $this->superAdminID, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
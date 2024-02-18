<?php

namespace Plat4mAPI\Model;

use PDO;

class TempProduct2
{
    /**
     * Fetch temporary products by UPC.
     * @param object $ctx Context.
     * @param string $upc UPC.
     * @return array Product.
     */
    public function getInfoByUPC($ctx, $upc)
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
                `product_name` AS `store_product_name`,
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
            WHERE `upc` = :upc AND `storeadmin_id` = :superAdminID
            ORDER BY `id` ASC";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":upc", $upc, PDO::PARAM_STR);
        $stmt->bindValue(":superAdminID", $ctx->tokenData->store_admin_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

<?php

namespace Plat4m\Core\API;

class ProductFormatter
{
    /**
     * Formats product info.
     * @param array $info Product info.
     * @return array Formatted product info.
     */
    public static function formatRepoProduct($info)
    {
        $multiItemQuantity = ($info["multi_item_quantity"] === NULL)
            ? (int) 0
            : (int) ($info["multi_item_quantity"]);
        $multiItemPrice = $info["multi_item_price"] ?? $info["regular_price"];
        return [
            "product_id"            => $info["product_id"],
            "upc"                   => $info["UPC"],
            "product_name"          => $info["product_name"],
            "category_id"           => $info["Category_Id"],
            "subcategory_id"        => $info["Category_Type"],
            "price"                 => $info["price"],
            "regular_price"         => $info["Regular_Price"],
            "selling_price"         => $info["sellprice"],
            "buying_price"          => $info["Buying_Price"],
            "tax_status"            => $info["Tax_Status"],
            "tax_value"             => $info["Tax_Value"],
            "special_value"         => $info["Special_Value"],
            "image"                 => $info["Image"],
            "pos_description"       => $info["POS_description"],
            "description"           => $info["description"],
            "manufacturer"          => $info["Manufacturer"],
            "brand"                 => $info["Brand"],
            "vendor"                => $info["Vendor"],
            "color"                 => $info["color"],
            "size"                  => $info["size"],
            "status"                => $info["status"],
            "product_status"        => $info["product_status"],
            "quantity"              => $info["quantity"],
            "p_limit"               => $info["plimit"],
            "sku"                   => $info["SKU"],
            "stock_quantity"        => $info["Stock_Quantity"],
            "product_mode"          => $info["ProductMode"],
            "age_restriction"       => $info["Age_Restriction"],
            "sale_type"             => $info["sale_type"],
            "created_on"            => $info["created_date_on"],
            "modified_on"           => $info["created_date_on"],
            "upc_status_request"    => NULL,
            "store_admin_id"        => $info["storeadmin_id"],
            "multi_item_quantity"   => $multiItemQuantity,
            "multi_item_price"      => $multiItemPrice,
            "discount_percent"      => $info["discount_percent"],
            "discount_pretax"       => $info["discount_pretax"],
            "discount_posttax"      => $info["discount_posttax"],
        ];
    }

    /**
     * Formats temp product info.
     * @param array $info Product info.
     * @return array Formatted product info.
     */
    public static function formatTempProduct($info)
    {
        return [
            "product_id"            => $info["product_id"],
            "upc"                   => $info["upc"],
            "product_name"          => $info["product_name"],
            "category_id"           => $info["category_id"],
            "subcategory_id"        => $info["category_type"],
            "price"                 => $info["price"],
            "regular_price"         => $info["regular_price"],
            "selling_price"         => $info["selling_price"],
            "buying_price"          => $info["buying_price"],
            "tax_status"            => $info["tax_status"],
            "tax_value"             => $info["tax_value"],
            "special_value"         => $info["special_value"],
            "image"                 => $info["image"],
            "pos_description"       => $info["description"],
            "description"           => $info["description"],
            "manufacturer"          => $info["manufacturer"],
            "brand"                 => $info["brand"],
            "vendor"                => $info["vendor"],
            "color"                 => $info["color"],
            "size"                  => $info["size"],
            "status"                => $info["product_status"],
            "product_status"        => $info["product_status"],
            "quantity"              => $info["quantity"],
            "p_limit"               => $info["p_limit"],
            "sku"                   => $info["sku"],
            "stock_quantity"        => $info["stock_quantity"],
            "product_mode"          => $info["product_mode"],
            "age_restriction"       => $info["age_restriction"],
            "sale_type"             => $info["sale_type"],
            "created_on"            => $info["created_on"],
            "modified_on"           => $info["modified_on"],
            "upc_status_request"    => $info["upc_status_request"],
            "store_admin_id"        => $info["storeadmin_id"]
        ];
    }
}

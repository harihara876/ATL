<?php

namespace Plat4mAPI\Model;

use PDO;

class RevenueReports
{
    /**
     * Fetche total revenue.
     * Total revenue = sum of the amount of completed orders.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return float Total revenue.
     */
    public function totalRevenue($ctx, $filters)
    {
        $selectSQL = "SELECT SUM(`users_orders`.`total`)
            FROM `users_orders`
            WHERE `uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `users_orders`.`order_status` = :orderStatus";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetches total revenue by payment mode.
     * Total revenue = sum of the amount of completed orders.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return float Total revenue.
     */
    public function totalRevenueByPaymentMode($ctx, $filters)
    {
        $selectSQL = "SELECT SUM(`users_orders`.`total`)
            FROM `users_orders`
            WHERE `uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `users_orders`.`order_status` = :orderStatus
            AND `users_orders`.`paymentmode`= :paymentMode";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->bindValue(":paymentMode", $filters->paymentMode, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetches total revenue in a period.
     * Total revenue = sum of the amount of completed orders.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return float Total revenue.
     */
    public function totalRevenueInPeriod($ctx, $filters)
    {
        $selectSQL = "SELECT SUM(`users_orders`.`total`)
            FROM `users_orders`
            WHERE `uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `users_orders`.`order_status` = :orderStatus
            AND `users_orders`.`order_date` BETWEEN :fromDate AND :toDate";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->bindValue(":fromDate", $filters->fromDate, PDO::PARAM_STR);
        $stmt->bindValue(":toDate", $filters->toDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetches total revenuevby payment mode in a period.
     * Total revenue = sum of the amount of completed orders.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return float Total revenue.
     */
    public function totalRevenueByPaymentModeInPeriod($ctx, $filters)
    {
        $selectSQL = "SELECT SUM(`users_orders`.`total`)
            FROM `users_orders`
            WHERE `uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `users_orders`.`order_status` = :orderStatus
            AND `users_orders`.`order_date` BETWEEN :fromDate AND :toDate
            AND `users_orders`.`paymentmode`= :paymentMode";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->bindValue(":fromDate", $filters->fromDate, PDO::PARAM_STR);
        $stmt->bindValue(":toDate", $filters->toDate, PDO::PARAM_STR);
        $stmt->bindValue(":paymentMode", $filters->paymentMode, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetch total selling price and tax.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return object Object Total selling price and tax.
     */
    public function totalSellingPriceAndTax($ctx, $filters)
    {
        $selectSQL = "SELECT
                SUM(`sellprice` * `quantity`) as `sellingPrice`,
                SUM((`sellprice` * `tax`) * `quantity`) as `tax`
            FROM `ordered_product`
            WHERE `order_id` IN (
                SELECT `id` FROM `users_orders` WHERE `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `order_status` = :orderStatus
            )";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Fetch total selling price and tax in a period.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return object Object Total selling price and tax.
     */
    public function totalSellingPriceAndTaxInPeriod($ctx, $filters)
    {
        $selectSQL = "SELECT
                SUM(`sellprice` * `quantity`) as `sellingPrice`,
                SUM((`sellprice` * `tax`) * `quantity`) as `tax`
            FROM `ordered_product`
            WHERE `order_id` IN (
                SELECT `id` FROM `users_orders` WHERE `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `order_status` = :orderStatus
                AND `order_date` BETWEEN :fromDate AND :toDate
            )";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->bindValue(":fromDate", $filters->fromDate, PDO::PARAM_STR);
        $stmt->bindValue(":toDate", $filters->toDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Fetches total revenue by category name.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return float Total revenue.
     */
    public function totalRevenueByCategoryName($ctx, $filters)
    {
        $selectSQL = "SELECT SUM(`total`)
            FROM `users_orders`
            WHERE `id` IN (
                SELECT `order_id`
                FROM `ordered_product`
                WHERE `product_id` IN (
                    SELECT `products`.`product_id`
                    FROM `category`
                    JOIN `products` ON `category`.`cat_id` = `products`.`cat_id`
                    WHERE `category`.`category_name` = :categoryName
                )
            ) AND `uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `order_status` = :orderStatus";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":categoryName", $filters->categoryName, PDO::PARAM_STR);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetches total revenue by category name in a period.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return float Total revenue.
     */
    public function totalRevenueByCategoryNameInPeriod($ctx, $filters)
    {
        $selectSQL = "SELECT SUM(`total`)
            FROM `users_orders`
            WHERE `id` IN (
                SELECT `order_id`
                FROM `ordered_product`
                WHERE `product_id` IN (
                    SELECT `products`.`product_id`
                    FROM `category`
                    JOIN `products` ON `category`.`cat_id` = `products`.`cat_id`
                    WHERE `category`.`category_name` = :categoryName
                )
            ) AND `uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `order_status` = :orderStatus
            AND `order_date` BETWEEN :fromDate AND :toDate";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":categoryName", $filters->categoryName, PDO::PARAM_STR);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->bindValue(":fromDate", $filters->fromDate, PDO::PARAM_STR);
        $stmt->bindValue(":toDate", $filters->toDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetches total revenue by subcategory name.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return float Total revenue.
     */
    public function totalRevenueBySubcategoryName($ctx, $filters)
    {
        $selectSQL = "SELECT SUM(`total`)
            FROM `users_orders`
            WHERE `id` IN (
                SELECT `order_id`
                FROM `ordered_product`
                WHERE `product_id` IN (
                    SELECT `products`.`product_id`
                    FROM `subcategories`
                    JOIN `products` ON `subcategories`.`Sub_Category_Id` = `products`.`Category_Type`
                    WHERE `subcategories`.`Sub_Category_Name` = :subcategoryName
                )
            ) AND `uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `order_status` = :orderStatus";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":subcategoryName", $filters->subcategoryName, PDO::PARAM_STR);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetches total revenue by subcategory name in a period.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return float Total revenue.
     */
    public function totalRevenueBySubcategoryNameInPeriod($ctx, $filters)
    {
        $selectSQL = "SELECT SUM(`total`)
            FROM `users_orders`
            WHERE `id` IN (
                SELECT `order_id`
                FROM `ordered_product`
                WHERE `product_id` IN (
                    SELECT `products`.`product_id`
                    FROM `subcategories`
                    JOIN `products` ON `subcategories`.`Sub_Category_Id` = `products`.`Category_Type`
                    WHERE `subcategories`.`Sub_Category_Name` = :subcategoryName
                )
            ) AND `uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `order_status` = :orderStatus
            AND `order_date` BETWEEN :fromDate AND :toDate";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":subcategoryName", $filters->subcategoryName, PDO::PARAM_STR);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->bindValue(":fromDate", $filters->fromDate, PDO::PARAM_STR);
        $stmt->bindValue(":toDate", $filters->toDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetches total revenue of Scratchers (Sub cat) and Games (Product).
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return float Total revenue.
     */
    public function totalRevenueOfScratchersGame($ctx, $filters)
    {
        $selectSQL = "SELECT
                SUM(`sellprice` * `quantity`) as `total`
            FROM `ordered_product`
            JOIN `users_orders` ON `users_orders`.`id` = `ordered_product`.`order_id`
            WHERE `ordered_product`.`product_name` REGEXP 'Game*'
            AND `users_orders`.`uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `users_orders`.`order_status` = :orderStatus";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetches total revenue of Scratchers (Sub cat) and Games (Product) in a period.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return float Total revenue.
     */
    public function totalRevenueOfScratchersGameInPeriod($ctx, $filters)
    {
        $selectSQL = "SELECT
                SUM(`sellprice` * `quantity`) as `total`
            FROM `ordered_product`
            JOIN `users_orders` ON `users_orders`.`id` = `ordered_product`.`order_id`
            WHERE `ordered_product`.`product_name` REGEXP 'Game*'
            AND `users_orders`.`uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `users_orders`.`order_status` = :orderStatus
            AND `users_orders`.`order_date` BETWEEN :fromDate AND :toDate";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->bindValue(":fromDate", $filters->fromDate, PDO::PARAM_STR);
        $stmt->bindValue(":toDate", $filters->toDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetches All Scratchers (Sub cat) and Games (Product) in a period.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return array of Scratchers.
     */
    public function AllScratchersGameInPeriod($ctx, $filters)
    {
        $selectSQL = "SELECT
                `product_name` as `name`,SUM(`sellprice` * `quantity`) as `total_amount`
            FROM `ordered_product`
            JOIN `users_orders` ON `users_orders`.`id` = `ordered_product`.`order_id`
            WHERE `ordered_product`.`product_name` REGEXP 'Game*' 
            AND `users_orders`.`uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `users_orders`.`order_status` = :orderStatus
            AND `users_orders`.`order_date` BETWEEN :fromDate AND :toDate";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->bindValue(":fromDate", $filters->fromDate, PDO::PARAM_STR);
        $stmt->bindValue(":toDate", $filters->toDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    /**
     * Fetches total revenue by a product name.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return float Total revenue.
     */
    public function totalRevenueByProduct($ctx, $filters)
    {
        $selectSQL = "SELECT SUM(`total`)
            FROM `users_orders`
            WHERE `id` IN (
                SELECT `order_id`
                FROM `ordered_product`
                WHERE `product_id` IN (
                    SELECT `product_id`
                    FROM `products`
                    WHERE `products`.`product_name` = :productName
                )
            ) AND `uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `order_status` = :orderStatus";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":productName", $filters->productName, PDO::PARAM_STR);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetches total revenue by a product name in a period.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return float Total revenue.
     */
    public function totalRevenueByProductInPeriod($ctx, $filters)
    {
        $selectSQL = "SELECT SUM(`total`)
            FROM `users_orders`
            WHERE `id` IN (
                SELECT `order_id`
                FROM `ordered_product`
                WHERE `product_id` IN (
                    SELECT `product_id`
                    FROM `products`
                    WHERE `products`.`product_name` = :productName
                )
            ) AND `uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `order_status` = :orderStatus
            AND `order_date` BETWEEN :fromDate AND :toDate";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":productName", $filters->productName, PDO::PARAM_STR);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->bindValue(":fromDate", $filters->fromDate, PDO::PARAM_STR);
        $stmt->bindValue(":toDate", $filters->toDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetches total revenue by a product name "Lotto".
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return float Total revenue.
     */
    public function totalRevenueByProductLotto($ctx, $filters)
    {
        $selectSQL = "SELECT
                SUM(`sellprice` * `quantity`) as `total`
            FROM `ordered_product`
            JOIN `users_orders` ON `users_orders`.`id` = `ordered_product`.`order_id`
            WHERE `ordered_product`.`product_name` = 'Lotto'
            AND `users_orders`.`uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `users_orders`.`order_status` = :orderStatus";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetches total revenue by a product name "Lotto" in a period.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return float Total revenue.
     */
    public function totalRevenueByProductLottoInPeriod($ctx, $filters)
    {
        $selectSQL = "SELECT
                SUM(`sellprice` * `quantity`) as `total`
            FROM `ordered_product`
            JOIN `users_orders` ON `users_orders`.`id` = `ordered_product`.`order_id`
            WHERE `ordered_product`.`product_name` = 'Lotto'
            AND `users_orders`.`uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `users_orders`.`order_status` = :orderStatus
            AND `users_orders`.`order_date` BETWEEN :fromDate AND :toDate";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->bindValue(":fromDate", $filters->fromDate, PDO::PARAM_STR);
        $stmt->bindValue(":toDate", $filters->toDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetches All product name "Lotto" in a period.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return Array of Lotto List.
     */
    public function AllProductLottoInPeriod($ctx, $filters)
    {
        $selectSQL = "SELECT
                `product_name` as name,SUM(`sellprice` * `quantity`) as `total_amount`
            FROM `ordered_product`
            JOIN `users_orders` ON `users_orders`.`id` = `ordered_product`.`order_id`
            WHERE `ordered_product`.`product_name` = 'Lotto'
            AND `users_orders`.`uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `users_orders`.`order_status` = :orderStatus
            AND `users_orders`.`order_date` BETWEEN :fromDate AND :toDate";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->bindValue(":fromDate", $filters->fromDate, PDO::PARAM_STR);
        $stmt->bindValue(":toDate", $filters->toDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}

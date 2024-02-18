<?php

namespace Plat4mAPI\Model;

use PDO;

class SalesReports
{
    /**
     * Fetch product sales.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return array Product sales info.
     */
    public function productSales($ctx, $filters)
    {
        $selectSQL = "SELECT
                `product_name`,
                SUM(`quantity`) AS `quantity`,
                FORMAT(SUM(`sellprice`), 2) AS `selling_price`
            FROM `ordered_product`
            WHERE `order_id` IN (
                SELECT `id` FROM `users_orders` WHERE `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `order_status` = :orderStatus
            )
            GROUP BY `product_name`
            ORDER BY `quantity` DESC, `product_name` ASC";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches product sales in a period.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return array Product sales info.
     */
    public function productSalesInPeriod($ctx, $filters)
    {
        $selectSQL = "SELECT
                `product_name`,
                SUM(`quantity`) AS `quantity`,
                FORMAT(SUM(`sellprice`), 2) AS `selling_price`
            FROM `ordered_product`
            WHERE `order_id` IN (
                SELECT `id` FROM `users_orders` WHERE `uid` IN (
                    SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                    UNION
                    SELECT :storeAdminID2
                ) AND `order_status` = :orderStatus
                AND `order_date` BETWEEN :fromDate AND :toDate
            )
            GROUP BY `product_name`
            ORDER BY `quantity` DESC, `product_name` ASC";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->bindValue(":fromDate", $filters->fromDate, PDO::PARAM_STR);
        $stmt->bindValue(":toDate", $filters->toDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

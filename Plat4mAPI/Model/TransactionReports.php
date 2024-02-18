<?php

namespace Plat4mAPI\Model;

use PDO;

class TransactionReports
{
    /**
     * Fetch count of orders.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return int Orders count.
     */
    public function ordersCount($ctx, $filters)
    {
        $selectSQL = "SELECT COUNT(`users_orders`.`id`)
            FROM `users_orders`
            WHERE `uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `users_orders`.`order_status`= :orderStatus";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", $filters->orderStatus, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetch count of orders by payment mode.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return int Orders count.
     */
    public function ordersCountByPaymentMode($ctx, $filters)
    {
        $selectSQL = "SELECT COUNT(`users_orders`.`id`)
            FROM `users_orders`
            WHERE `uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `users_orders`.`paymentmode`= :paymentMode";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":paymentMode", $filters->paymentMode, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetch count of orders in a period.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return int Orders count.
     */
    public function ordersCountInPeriod($ctx, $filters)
    {
        $selectSQL = "SELECT COUNT(`users_orders`.`id`)
            FROM `users_orders`
            WHERE `uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `users_orders`.`order_status`= :orderStatus
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
     * Fetch Sum of orders Special fee in a period.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return Sum of Orders special fee .
     */
    public function ordersSpecialFeeInPeriod($ctx, $filters)
    {
        $selectSQL = "SELECT SUM(`users_orders`.`total_special_fee`)
            FROM `users_orders`
            WHERE `uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `users_orders`.`order_status`= :orderStatus
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
     * Fetch count of orders by payment mode in a period.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return int Orders count.
     */
    public function ordersCountInPeriodByPaymentMode($ctx, $filters)
    {
        $selectSQL = "SELECT COUNT(`users_orders`.`id`)
            FROM `users_orders`
            WHERE `uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `users_orders`.`paymentmode`= :paymentMode
            AND `users_orders`.`order_date` BETWEEN :fromDate AND :toDate";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":paymentMode", $filters->paymentMode, PDO::PARAM_STR);
        $stmt->bindValue(":fromDate", $filters->fromDate, PDO::PARAM_STR);
        $stmt->bindValue(":toDate", $filters->toDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetch orders in a period.
     * @param object $ctx Context.
     * @param object $filters Filters.
     * @return array Orders.
     */
    public function ordersInPeriod($ctx, $filters)
    {
        $selectSQL = "SELECT
                `id`,
                `order_id` AS `order_uuid`,
                `total` AS `amount`,
                `order_status` AS `status`,
                `order_date` AS `tms`,
                `uid` AS `user_id`,
                `paymentmode` AS `payment_mode`
            FROM `users_orders`
            WHERE `uid` IN (
                SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
                UNION
                SELECT :storeAdminID2
            ) AND `order_date` BETWEEN :fromDate AND :toDate
            ORDER BY `order_date`";
        $stmt = $ctx->db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $filters->storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":fromDate", $filters->fromDate, PDO::PARAM_STR);
        $stmt->bindValue(":toDate", $filters->toDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}

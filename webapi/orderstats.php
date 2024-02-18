<?php

require_once("../config.php");

/**
 * Fetch orders count by order status.
 * @param object $db DB connection.
 * @param string $orderStatus Order status.
 * @return int Orders count.
 */
function getOrdersCountByStatus($db, $orderStatus)
{
    $selectSQL = "SELECT COUNT(*) FROM `users_orders`
        WHERE `order_status` = :orderStatus";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":orderStatus", $orderStatus);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch total revenue.
 * @param object $db DB connection.
 * @return int Revenue.
 */
function getTotalRevenue($db)
{
    $selectSQL = "SELECT SUM(`total`) FROM `users_orders`
        WHERE `order_status` = :orderStatus";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
    $stmt->execute();
    return round($stmt->fetchColumn(), 2);
}

/**
 * Fetch orders count by store admin and order status.
 * @param object $db DB connection.
 * @param int $storeAdminID Store admin ID.
 * @param string $orderStatus Order status.
 * @return int Orders count.
 */
function getOrdersCountByStoreAdminAndStatus($db, $storeAdminID, $orderStatus)
{
    $selectSQL = "SELECT COUNT(*) FROM `device_users`
        JOIN `users_orders`
        WHERE `device_users`.`id` = `users_orders`.`uid`
        AND `device_users`.`storeadmin_id` = :storeAdminID
        AND `users_orders`.`order_status` = :orderStatus";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":storeAdminID", $storeAdminID, PDO::PARAM_INT);
    $stmt->bindValue(":orderStatus", $orderStatus, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch total revenue.
 * @param object $db DB connection.
 * @param int $storeAdminID Store admin ID.
 * @return int Revenue.
 */
function getTotalRevenueByStoreAdmin($db, $storeAdminID)
{
    $selectSQL = "SELECT SUM(`total`) FROM `device_users`
        JOIN `users_orders`
        WHERE `device_users`.`id` = `users_orders`.`uid`
        AND `device_users`.`storeadmin_id` = :storeAdminID
        AND `users_orders`.`order_status` = :orderStatus";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":storeAdminID", $storeAdminID, PDO::PARAM_INT);
    $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
    $stmt->execute();
    return round($stmt->fetchColumn(), 2);
}

if ($_SESSION["type_app"]  == "ADMIN") {
    $totalRevenue               = getTotalRevenue($db);
    $ordersCompletedCount       = getOrdersCountByStatus($db, ORDER_COMPLETED);
    $ordersInProcessingCount    = getOrdersCountByStatus($db, ORDER_IN_PROCESSING);
    $ordersCancelledCount       = getOrdersCountByStatus($db, ORDER_CANCELLED);
} else {
    $totalRevenue               = getTotalRevenueByStoreAdmin($db, $_SESSION["id"]);
    $ordersCompletedCount       = getOrdersCountByStoreAdminAndStatus($db, $_SESSION["id"], ORDER_COMPLETED);
    $ordersInProcessingCount    = getOrdersCountByStoreAdminAndStatus($db, $_SESSION["id"], ORDER_IN_PROCESSING);
    $ordersCancelledCount       = getOrdersCountByStoreAdminAndStatus($db, $_SESSION["id"], ORDER_CANCELLED);
}

echo json_encode([
    "error" => FALSE,
    "data" => [
        "totalRevenue"              => $totalRevenue,
        "ordersCompletedCount"      => $ordersCompletedCount,
        "ordersInProcessingCount"   => $ordersInProcessingCount,
        "ordersCancelledCount"      => $ordersCancelledCount,
    ]
]);
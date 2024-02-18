<?php

/**
 * Fetch count of orders.
 * @param object $db DB connection.
 * @param string $orderStatus Order status.
 * @return int Orders count.
 */
function ordersCount($db, $orderStatus)
{
    $selectSQL = "SELECT COUNT(`id`)
        FROM `users_orders`
        WHERE `order_status` = :orderStatus";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":orderStatus", $orderStatus, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch count of orders of a store.
 * @param object $db DB connection.
 * @param int $storeAdminID Store admin ID.
 * @param string $orderStatus Order status.
 * @return int Orders count.
 */
function storeOrdersCount($db, $storeAdminID, $orderStatus)
{
    $selectSQL = "SELECT COUNT(`users_orders`.`id`)
        FROM `users_orders`
        WHERE `uid` IN (
            SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
            UNION
            SELECT :storeAdminID2
        ) AND `users_orders`.`order_status`= :orderStatus";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":storeAdminID1", $storeAdminID, PDO::PARAM_INT);
    $stmt->bindValue(":storeAdminID2", $storeAdminID, PDO::PARAM_INT);
    $stmt->bindValue(":orderStatus", $orderStatus, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch count of orders in a period.
 * @param object $db DB connection.
 * @param string $orderStatus Order status.
 * @param string $fromDate From date.
 * @param string $toDate To date.
 * @return int Orders count.
 */
function ordersCountInPeriod($db, $orderStatus, $fromDate, $toDate)
{
    $selectSQL = "SELECT COUNT(`id`)
        FROM `users_orders`
        WHERE `order_status`= :orderStatus
        AND `order_date` BETWEEN :fromDate AND :toDate";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":orderStatus", $orderStatus, PDO::PARAM_STR);
    $stmt->bindValue(":fromDate", $fromDate, PDO::PARAM_STR);
    $stmt->bindValue(":toDate", $toDate, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch count of orders of a store in a period.
 * @param object $db DB connection.
 * @param int $storeAdminID Store admin ID.
 * @param string $orderStatus Order status.
 * @param string $fromDate From date.
 * @param string $toDate To date.
 * @return int Orders count.
 */
function storeOrdersCountInPeriod($db, $storeAdminID, $orderStatus, $fromDate, $toDate)
{
    $selectSQL = "SELECT COUNT(`users_orders`.`id`)
        FROM `users_orders`
        WHERE `uid` IN (
            SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
            UNION
            SELECT :storeAdminID2
        ) AND `users_orders`.`order_status`= :orderStatus
        AND `users_orders`.`order_date` BETWEEN :fromDate AND :toDate";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":storeAdminID1", $storeAdminID, PDO::PARAM_INT);
    $stmt->bindValue(":storeAdminID2", $storeAdminID, PDO::PARAM_INT);
    $stmt->bindValue(":orderStatus", $orderStatus, PDO::PARAM_STR);
    $stmt->bindValue(":fromDate", $fromDate, PDO::PARAM_STR);
    $stmt->bindValue(":toDate", $toDate, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetches total revenue.
 * Total revenue = sum of the amount of completed orders.
 * @param object $db DB connection.
 * @return float Total revenue.
 */
function totalRevenue($db) {
    $selectSQL = "SELECT SUM(`total`)
        FROM `users_orders`
        WHERE `order_status` = :orderStatus";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetches total revenue of a store.
 * Total revenue = sum of the amount of completed orders.
 * @param object $db DB connection.
 * @param int $storeAdminID Store admin ID.
 * @return float Total revenue.
 */
function storeTotalRevenue($db, $storeAdminID) {
    $selectSQL = "SELECT SUM(`users_orders`.`total`)
        FROM `users_orders`
        WHERE `uid` IN (
            SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
            UNION
            SELECT :storeAdminID2
        ) AND `users_orders`.`order_status` = :orderStatus";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":storeAdminID1", $storeAdminID, PDO::PARAM_INT);
    $stmt->bindValue(":storeAdminID2", $storeAdminID, PDO::PARAM_INT);
    $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetches total revenue in a period.
 * Total revenue = sum of the amount of completed orders.
 * @param object $db DB connection.
 * @param string $fromDate From date.
 * @param string $toDate To date.
 * @return float Total revenue.
 */
function totalRevenueInPeriod($db, $fromDate, $toDate) {
    $selectSQL = "SELECT SUM(`total`)
        FROM `users_orders`
        WHERE `order_status` = :orderStatus
        AND `order_date` BETWEEN :fromDate AND :toDate";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
    $stmt->bindValue(":fromDate", $fromDate, PDO::PARAM_STR);
    $stmt->bindValue(":toDate", $toDate, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetches total revenue of a store in a period.
 * Total revenue = sum of the amount of completed orders.
 * @param object $db DB connection.
 * @param int $storeAdminID Store admin ID.
 * @param string $fromDate From date.
 * @param string $toDate To date.
 * @return float Total revenue.
 */
function storeTotalRevenueInPeriod($db, $storeAdminID, $fromDate, $toDate) {
    $selectSQL = "SELECT SUM(`users_orders`.`total`)
        FROM `users_orders`
        WHERE `uid` IN (
            SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
            UNION
            SELECT :storeAdminID2
        ) AND `users_orders`.`order_status` = :orderStatus
        AND `users_orders`.`order_date` BETWEEN :fromDate AND :toDate";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":storeAdminID1", $storeAdminID, PDO::PARAM_INT);
    $stmt->bindValue(":storeAdminID2", $storeAdminID, PDO::PARAM_INT);
    $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
    $stmt->bindValue(":fromDate", $fromDate, PDO::PARAM_STR);
    $stmt->bindValue(":toDate", $toDate, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchColumn();
}

/**
 * Fetch total selling price and tax.
 * Total selling price = selling price * quantity
 * Total tax amount = (selling price * tax percent) * quantity
 * @param object $db DB connection.
 * @return object Object Total selling price and tax.
 */
function totalSellingPriceAndTax($db) {
    $selectSQL = "SELECT
            SUM(`sellprice` * `quantity`) as `sellingPrice`,
            SUM((`sellprice` * `tax`) * `quantity`) as `tax`
        FROM `ordered_product`
        JOIN `users_orders` ON `users_orders`.`id` = `ordered_product`.`order_id`
        WHERE `order_status` = :orderStatus";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
}

/**
 * Fetch total selling price and tax of a store.
 * @param object $db DB connection.
 * @param int $storeAdminID Store admin ID.
 * @return object Object Total selling price and tax.
 */
function storeTotalSellingPriceAndTax($db, $storeAdminID) {
    $selectSQL = "SELECT
            SUM(`sellprice` * `quantity`) as `sellingPrice`,
            SUM((`sellprice` * `tax`) * `quantity`) as `tax`
        FROM `ordered_product`
        JOIN `users_orders` ON `users_orders`.`id` = `ordered_product`.`order_id`
        WHERE `users_orders`.`uid` IN (
            SELECT `id` FROM `device_users` WHERE `storeadmin_id` = :storeAdminID1
            UNION
            SELECT :storeAdminID2
        ) AND `order_status` = :orderStatus";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":storeAdminID1", $storeAdminID, PDO::PARAM_INT);
    $stmt->bindValue(":storeAdminID2", $storeAdminID, PDO::PARAM_INT);
    $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
}

/**
 * Fetch total selling price and tax in a period.
 * @param object $db DB connection.
 * @param string $fromDate From date.
 * @param string $toDate To date.
 * @return object Total selling price and tax.
 */
function totalSellingPriceAndTaxInPeriod($db, $fromDate, $toDate) {
    $selectSQL = "SELECT
            SUM(`sellprice` * `quantity`) as `sellingPrice`,
            SUM((`sellprice` * `tax`) * `quantity`) as `tax`
        FROM `ordered_product`
        JOIN `users_orders` ON `users_orders`.`id` = `ordered_product`.`order_id`
        WHERE `order_status` = :orderStatus
        AND `order_date` BETWEEN :fromDate AND :toDate";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
    $stmt->bindValue(":fromDate", $fromDate, PDO::PARAM_STR);
    $stmt->bindValue(":toDate", $toDate, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
}

/**
 * Fetch total selling price and tax of a store in a period.
 * @param object $db DB connection.
 * @param int $storeAdminID Store admin ID.
 * @param string $fromDate From date.
 * @param string $toDate To date.
 * @return object Object Total selling price and tax.
 */
function storeTotalSellingPriceAndTaxInPeriod($db, $storeAdminID, $fromDate, $toDate) {
    try {
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
        $stmt = $db->prepare($selectSQL);
        $stmt->bindValue(":storeAdminID1", $storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":storeAdminID2", $storeAdminID, PDO::PARAM_INT);
        $stmt->bindValue(":orderStatus", ORDER_COMPLETED, PDO::PARAM_STR);
        $stmt->bindValue(":fromDate", $fromDate, PDO::PARAM_STR);
        $stmt->bindValue(":toDate", $toDate, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    } catch (PDOException $ex) {
        throw new Exception($ex->getMessage(), 500);
    }
}

require_once("header.php");
require_once("sidebar.php");
require_once("widget.php");
require_once("lib/t3storelib.php");

$storeAdminID   = $_SESSION["id"];

if ($_GET["from_date"] && $_GET["to_date"] && $_GET["submit"] == "Search") {
    // TODO: Validate dates.
    $formFromDate   = $_GET["from_date"];
    $formToDate     = $_GET["to_date"];
    $fromDate       = $formFromDate . " 00:00:00";
    $toDate         = $formToDate . " 23:59:59";

    if ($_SESSION["type_app"]  == "ADMIN") {
        $summary                = totalSellingPriceAndTaxInPeriod($db, $fromDate, $toDate);
        $ordersCompleted        = ordersCountInPeriod($db, ORDER_COMPLETED, $fromDate, $toDate);
        $ordersInProcessing     = ordersCountInPeriod($db, ORDER_IN_PROCESSING, $fromDate, $toDate);
        $ordersCancelled        = ordersCountInPeriod($db, ORDER_CANCELLED, $fromDate, $toDate);
        $totalSellingPrice      = round($summary->sellingPrice, 2);
        $totalTax               = round($summary->tax, 2);
        $totalRevenue           = round(totalRevenueInPeriod($db, $fromDate, $toDate), 2);
    } else {
        $summary                = storeTotalSellingPriceAndTaxInPeriod($db, $storeAdminID, $fromDate, $toDate);
        $ordersCompleted        = storeOrdersCountInPeriod($db, $storeAdminID, ORDER_COMPLETED, $fromDate, $toDate);
        $ordersInProcessing     = storeOrdersCountInPeriod($db, $storeAdminID, ORDER_IN_PROCESSING, $fromDate, $toDate);
        $ordersCancelled        = storeOrdersCountInPeriod($db, $storeAdminID, ORDER_CANCELLED, $fromDate, $toDate);
        $totalSellingPrice      = round($summary->sellingPrice, 2);
        $totalTax               = round($summary->tax, 2);
        $totalRevenue           = round(storeTotalRevenueInPeriod($db, $storeAdminID, $fromDate, $toDate), 2);
    }
} else {
    if ($_SESSION["type_app"]  == "ADMIN") {
        $summary                = totalSellingPriceAndTax($db);
        $ordersCompleted        = ordersCount($db, ORDER_COMPLETED);
        $ordersInProcessing     = ordersCount($db, ORDER_IN_PROCESSING);
        $ordersCancelled        = ordersCount($db, ORDER_CANCELLED);
        $totalSellingPrice      = round($summary->sellingPrice, 2);
        $totalTax               = round($summary->tax, 2);
        $totalRevenue           = round(totalRevenue($db), 2);
    } else {
        $summary                = storeTotalSellingPriceAndTax($db, $storeAdminID);
        $ordersCompleted        = storeOrdersCount($db, $storeAdminID, ORDER_COMPLETED);
        $ordersInProcessing     = storeOrdersCount($db, $storeAdminID, ORDER_IN_PROCESSING);
        $ordersCancelled        = storeOrdersCount($db, $storeAdminID, ORDER_CANCELLED);
        $totalSellingPrice      = round($summary->sellingPrice, 2);
        $totalTax               = round($summary->tax, 2);
        $totalRevenue           = round(storeTotalRevenue($db, $storeAdminID), 2);
    }
}
?>

<!-- Modal content-->
<div class="modal-content ">
    <div class="modal-header">
        <h4 class="modal-title">
            <i class="fa fa-list-ol fa-5" aria-hidden="true"></i> Orders Summary
        </h4>
    </div>
    <div class="modal-body">
        <br>
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <form method="get" action="" name="report">
                                <div class="col-md-2">
                                    <label>From </label>
                                    <input type="date" name="from_date" value="<?php echo $formFromDate; ?>"
                                        class="form-control">
                                </div>

                                <div class="col-md-2">
                                    <label>To </label>
                                    <input type="date" name="to_date" value="<?php echo $formToDate; ?>"
                                        class="form-control">
                                </div>

                                <div class="col-md-2">
                                    <label>&nbsp; </label>
                                    <input type="submit" value="Search" name="submit"
                                        class="btn btn-primary form-control">
                                </div>

                                <div class="col-md-2">
                                    <label>&nbsp; </label>
                                    <input type="submit" value="Reset" name="submit"
                                        class="btn btn-default form-control">
                                </div>

                                <div class="col-md-2">
                                    <label>&nbsp; </label>
                                    <input type="button" value="Print" onclick="printDiv('printableArea')"
                                        class="btn btn-danger form-control">
                                </div>
                            </form>
                        </div>
                        <div class="box-body" id="printableArea">
                            <div class="box-footer">
                                <div class="row">
                                    <div class="col-sm-4 col-xs-6">
                                        <div class="description-block border-right">
                                            <span class="description-percentage text-green"> </span>
                                            <h5 class="description-header"><?php echo $ordersCompleted; ?></h5>
                                            <span class="description-text">Orders Completed</span>
                                        </div>
                                        <!-- /.description-block -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-sm-4 col-xs-6">
                                        <div class="description-block border-right">
                                            <span class="description-percentage text-blue"></span>
                                            <h5 class="description-header"><?php echo $ordersInProcessing; ?> </h5>
                                            <span class="description-text">Orders In Processing</span>
                                        </div>
                                        <!-- /.description-block -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-sm-4 col-xs-6">
                                        <div class="description-block">
                                            <span class="description-percentage text-red"> </span>
                                            <h5 class="description-header"><?php echo $ordersCancelled; ?></h5>
                                            <span class="description-text">Orders cancelled</span>
                                        </div>
                                        <!-- /.description-block -->
                                    </div>
                                </div>
                                <!-- /.row -->
                            </div>
                            <div class="box-footer">
                                <div class="row">
                                    <div class="col-sm-4 col-xs-6">
                                        <div class="description-block border-right">
                                            <span class="description-percentage text-green"> </span>
                                            <h5 class="description-header"><?php echo $currency . $totalSellingPrice; ?>
                                            </h5>
                                            <span class="description-text">TOTAL SELL PRICE</span>
                                        </div>
                                        <!-- /.description-block -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-sm-4 col-xs-6">
                                        <div class="description-block border-right">
                                            <span class="description-percentage text-blue"></span>
                                            <h5 class="description-header"><?php echo $totalTax; ?> </h5>
                                            <span class="description-text">TOTAL TAX</span>
                                        </div>
                                        <!-- /.description-block -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-sm-4 col-xs-6">
                                        <div class="description-block border-right">
                                            <span class="description-percentage text-green"> </span>
                                            <h5 class="description-header"><?php echo $currency . $totalRevenue; ?></h5>
                                            <span class="description-text">TOTAL REVENUE</span>
                                        </div>
                                        <!-- /.description-block -->
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script language="JavaScript" type="text/javascript">
    function checkDelete() {
        return confirm('Are you sure you want to delete?');
    }
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>

<?php require_once("scriptfooter.php"); ?>
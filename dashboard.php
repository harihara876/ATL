<?php

$pageTitle = "Dashboard";

require_once("header.php");
require_once("sidebar.php");
require_once("widget.php");

/**
 * Fetch recent orders.
 * @param object $db DB connection.
 * @param int $rowLimit Row limit.
 * @return array Recent orders.
 */
function getRecentOrders($db, $rowLimit = 8)
{
    $selectSQL = "SELECT
            `users_orders`.`id`,
            `users_orders`.`order_id`,
            `users_orders`.`order_status`,
            `users_orders`.`order_date`,
            `admin`.`name`
        FROM `users_orders`
        JOIN `admin` ON `admin`.`admin_id` = `users_orders`.`uid`
        ORDER BY `users_orders`.`id` DESC
        LIMIT :rowLimit";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":rowLimit", $rowLimit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetch recent orders by store admin ID.
 * @param object $db DB connection.
 * @param int $storeAdminID Store admin ID.
 * @param int $rowLimit Row limit.
 * @return array Recent orders.
 */
function getRecentOrdersByStoreAdmin($db, $storeAdminID, $rowLimit = 8)
{
    $selectSQL = "SELECT
            `users_orders`.`id`,
            `users_orders`.`order_id`,
            `users_orders`.`order_status`,
            `users_orders`.`order_date`,
            CONCAT(`first_name`, ' ', `last_name`) AS `name`
        FROM `device_users`
        JOIN `users_orders`
        WHERE `device_users`.`id` = `users_orders`.`uid`
        AND `device_users`.`storeadmin_id` = :storeAdminID
        ORDER BY `users_orders`.`id` DESC
        LIMIT :rowLimit";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":storeAdminID", $storeAdminID, PDO::PARAM_INT);
    $stmt->bindValue(":rowLimit", $rowLimit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetch recent orders.
 * @param object $db DB connection.
 * @param int $rowLimit Row limit.
 * @return array Recent orders.
 */
function getRecentlyAddedProducts($db, $rowLimit = 5)
{
    $selectSQL = "SELECT
            `p`.`id`,
            `p`.`product_id`,
            `p`.`product_name`,
            `pd`.`sellprice`,
            `pd`.`quantity`
        FROM `products` `p`
        LEFT JOIN `product_details` `pd` ON `pd`.`product_id` = `p`.`product_id`
        GROUP BY `p`.`product_id`
        ORDER BY `p`.`id` DESC
        LIMIT :rowLimit";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":rowLimit", $rowLimit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetch recent orders.
 * @param object $db DB connection.
 * @param int $storeAdminID Store admin ID.
 * @param int $rowLimit Row limit.
 * @return array Recent orders.
 */
function getRecentlyAddedProductsByStoreAdmin($db, $storeAdminID, $rowLimit = 5)
{
    $selectSQL = "SELECT
            `p`.`id`,
            `p`.`product_id`,
            `p`.`product_name`,
            `pd`.`sellprice`,
            `pd`.`quantity`
        FROM `products` `p`
        LEFT JOIN `product_details` `pd` ON `pd`.`product_id` = `p`.`product_id`
        WHERE `pd`.`storeadmin_id` = :storeAdminID
        GROUP BY `p`.`product_id`
        ORDER BY `p`.`id` DESC
        LIMIT :rowLimit";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":storeAdminID", $storeAdminID, PDO::PARAM_INT);
    $stmt->bindValue(":rowLimit", $rowLimit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Fetch product image.
 * @param object $db DB connection.
 * @param int $productID Product ID.
 * @return string Image link.
 */
function getProductImage($db, $productID)
{
    $selectSQL = "SELECT `image` FROM `product_images` WHERE `product_id` = :productID LIMIT 1";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":productID", $productID, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}

$currency = "";

if ($_SESSION['type_app']  == 'ADMIN') {
    $recentOrders = getRecentOrders($db);
    $recentProducts = getRecentlyAddedProducts($db);
} else {
    $recentOrders = getRecentOrdersByStoreAdmin($db, $_SESSION["id"]);
    $recentProducts = getRecentlyAddedProductsByStoreAdmin($db, $_SESSION["id"]);
}
?>

<div class="box with-border box box-success box-solid">
    <div class="box-header with-border ">
        <h4>
            <center><b>Store Report</b></center>
        </h4>
        <div class="box-tools pull-right"></div>
    </div>
    <!-- /.box-header -->

    <div class="box-body">
        <div class="box-footer">
            <div class="row">
                <div class="col-sm-3 col-xs-6">
                    <div class="description-block border-right">
                        <span class="description-percentage text-green"> </span>
                        <h5 class="description-header" id="totalRevenue">Loading...</h5>
                        <span class="description-text">Total Revenue</span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-xs-6">
                    <div class="description-block border-right">
                        <span class="description-percentage text-blue"></span>
                        <h5 class="description-header" id="ordersInProcessingCount">Loading...</h5>
                        <span class="description-text">Orders In Processing</span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-xs-6">
                    <div class="description-block border-right">
                        <span class="description-percentage text-green"> </span>
                        <h5 class="description-header" id="ordersCompletedCount">Loading...</h5>
                        <span class="description-text">Orders Completed</span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-3 col-xs-6">
                    <div class="description-block">
                        <span class="description-percentage text-red"> </span>
                        <h5 class="description-header" id="ordersCancelledCount">Loading...</h5>
                        <span class="description-text">Orders Cancelled</span>
                    </div>
                    <!-- /.description-block -->
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.box-footer -->
        <hr>

        <div class="col-md-8">
            <p class="text-center">
            <div class="widget-user-header bg-aqua-active">
                <p class="text-center"><strong>Recent Orders</strong></p>
            </div>
            </p>
            <div class="chart">
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table no-margin">
                            <?php
                            if (count($recentOrders) == 0) {
                                echo '<div class="alert alert-info alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4><i class="icon fa fa-info"></i> Alert!</h4>
                                    Waiting for your first order.All the best.
                                    </div>';
                            } else {
                            ?>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Admin Name</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($recentOrders as $order) {
                                    echo "<tr>";
                                    echo "<td><a href='vieworderdetails.php?id={$order["id"]}'>{$order["order_id"]}</a></td>";
                                    echo "<td>{$order["name"]}</td>";
                                    echo "<td>{$order["order_date"]}</td>";
                                    echo "<td>";

                                    switch ($order["order_status"]) {
                                        case ORDER_COMPLETED:
                                            echo '<span class="label label-success">Completed</span>';
                                            break;
                                        case ORDER_IN_PROCESSING:
                                            echo '<span class="label label-info">Processing</span>';
                                            break;
                                        case ORDER_CANCELLED:
                                            echo '<span class="label label-danger">Canelled</span>';
                                            break;
                                        default:
                                        echo '<span class="label label-warning">Unknown</span>';
                                    }

                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                            <?php } ?>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <div class="box-footer text-center">
                    <a href="orders.php" class="uppercase">View All Orders</a>
                </div>
            </div>
            <!-- /.chart-responsive -->
        </div>

        <div class="col-md-4">
            <p class="text-center">
            <div class="widget-user-header bg-green-active">
                <strong>
                    <p class="text-center">Recently Added Products</p>
                </strong>
            </div>
            </p>
            <div class="progress-group">
                <?php
                        if (count($recentProducts) == 0) {
                            echo '<div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <h4><i class="icon fa fa-info"></i> Alert!</h4>
                                Please add your first product.
                                </div>';
                        } else {
                            foreach ($recentProducts as $product) {
                        ?>
                <ul class="products-list product-list-in-box">
                    <li class="item">
                        <div class="product-img">
                            <?php
                                    $productImage = getProductImage($db, $product["product_id"]);
                                    if ($productImage) {
                                        echo "<img src='{$productImage}' alt='Product Image'>";
                                    }
                                    ?>
                        </div>
                        <div class="product-info">
                            <a href="viewproductsdetails.php?id=<?php echo base64_encode($product['id']);?>"
                                class="product-title"><?php echo $product['product_name'];?>
                                <span
                                    class="label label-info pull-right"><?php  echo $currency.$product['sellprice']; ?></span>
                            </a>
                            <span class="product-description">
                                Availability In Stock:<?php echo $product['quantity'];?>
                            </span>
                        </div>
                    </li>
                    <?php }} ?>
                    <!-- /.item -->
                </ul>
                <div class="box-footer text-center">
                    <a href="products.php" class="uppercase">View All Products</a>
                </div>
            </div>
            <!-- /.progress-group -->
        </div>
        <!-- /.col-md-4 -->
    </div>
    <!-- ./box-body -->
</div>
<!-- /.box -->
</section>
</div>

<?php require_once("footer.php"); ?>
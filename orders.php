<?php

/**
 * Returns orders list.
 * @param object DB connection object.
 * @return array Orders.
 */

// SUPER ADMIN
function getOrders($conn)
{

   // $selectSQL_a = "SELECT * FROM `device_users` where `storeadmin_id`='ADMIN' ";


    $orders = [];
    $selectSQL = "SELECT `device_users`.`id`, `device_users`.`storeadmin_id`,`users_orders`.`id`, `users_orders`.`order_id`, `users_orders`.`paymentref`, `users_orders`.`paymentmode`, `users_orders`.`payment_status`, `users_orders`.`address`, `users_orders`.`order_date`, `users_orders`.`order_status`, `users_orders`.`phone`, `users_orders`.`uid`  FROM `device_users` JOIN `users_orders` WHERE `device_users`.`id` = `users_orders`.`uid`  ";
//    $selectSQL = "SELECT * FROM `users_orders` ORDER BY `id` DESC";
    $result = mysqli_query($conn, $selectSQL);

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $orders[] = $row;
    }
// `users_orders`.`id`, `users_orders`.`order_id`, `users_orders`.`paymentref`, `users_orders`.`paymentmode`, `users_orders`.`payment_status`, `users_orders`.`address`, `users_orders`.`order_date`, `users_orders`.`order_status`, `users_orders`.`phone`, `users_orders`.`total`, `users_orders`.`uid`  

//`device_users`.`id`, `device_users`.`first_name`, `device_users`.`last_name`, `device_users`.`username`, `device_users`.`email`, `device_users`.`password`, `device_users`.`status`, `device_users`.`created_on`, `device_users`.`modified_on`, `device_users`.`storeadmin_id`, `device_users`.`type_app_admin`
    return $orders;
}
// ADMIN
function getOrders_user($conn)
{

   // $selectSQL_a = "SELECT * FROM `device_users` where `storeadmin_id`='ADMIN' ";


    $orders = [];
    $selectSQL = "SELECT `device_users`.`id`, `device_users`.`storeadmin_id`,`users_orders`.`id`, `users_orders`.`order_id`, `users_orders`.`paymentref`, `users_orders`.`paymentmode`, `users_orders`.`payment_status`, `users_orders`.`address`, `users_orders`.`order_date`, `users_orders`.`order_status`, `users_orders`.`phone`, `users_orders`.`uid`   FROM `device_users` JOIN `users_orders` WHERE `device_users`.`id` = `users_orders`.`uid` AND `device_users`.`storeadmin_id`={$_SESSION['id']} ";
//    $selectSQL = "SELECT * FROM `users_orders` ORDER BY `id` DESC";
    $result = mysqli_query($conn, $selectSQL);

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $orders[] = $row;
    }

    return $orders;
}
require_once("header.php");
require_once("sidebar.php");
require_once("widget.php");
?>

<!-- Modal content-->
<div class="modal-content ">
    <div class="modal-header">
        <h4 class="modal-title"> <i class="fa fa-gift fa-5" aria-hidden="true"></i>All Orders</li></h4>
    </div>
<div class="modal-body">

<script language="JavaScript" type="text/javascript">
    function checkDelete(){
        return confirm('Are you sure you want to delete?');
    }
</script>

<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Manage Orders</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="employee_data" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Order-Id</th>
                                <th>Admin Name</th>
                                <th>Users</th>
                                <th class="col-md-3">Address</th>
                                <th>Phone</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>View</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <?php
                            
        if($_SESSION['type_app']  == 'ADMIN'){

                            $orders = getOrders($conn);
                            foreach ($orders as $order) {
                                $id = $order["id"];
                                $orderID = $order["order_id"];
                                $address = (!empty($order["address"])) ? $order["address"] : "NA";
                                $phone = (!empty($order["phone"])) ? $order["phone"] : "NA";
                                $orderDate = $order["order_date"];
                                $orderStatus = $order["order_status"];

                                $orderStatusHTML = "";

                                if ($orderStatus == "In-Processing") {
                                    $orderStatusHTML = "<div class='progress progress-xs progress-striped active'>
                                        <div class='progress-bar progress-bar-primary' style='width: 10%'></div>
                                        </div></div><br>{$orderStatus}";
                                } elseif ($orderStatus == "Dispatch") {
                                    $orderStatusHTML = "<div class='progress progress-xs progress-striped active'>
                                        <div class='progress-bar progress-bar-yellow' style='width: 80%'></div>
                                        </div></div><br>{$orderStatus}";
                                } elseif ($orderStatus == "Complete") {
                                    $orderStatusHTML = "<div class='progress progress-xs progress-striped active'>
                                        <div class='progress-bar progress-bar-success' style='width: 100%'></div>
                                        </div></div><br>{$orderStatus}";
                                } elseif ($orderStatus == "Cancel") {
                                    $orderStatusHTML = "<div class='progress progress-xs progress-striped active'>
                                        <div class='progress-bar progress-bar-danger' style='width: 100%'></div>
                                        </div></div><br>{$orderStatus}";
                                }


                           /*      $od = substr($order["order_id"],-4);  
                                $ud = str_split($od);
                                $userId = $ud[3];



                                if($ud[0]=='0' && $ud[1]=='0' && $ud[2]=='0'){
                                   $userId = $ud[3];
                                }
                                else if( $ud[0]=='0' && $ud[1]=='0' && $ud[2]!='0'){
                                   $userId = $ud[2]."".$ud[3];

                                }
                                else if( $ud[0]=='0' && $ud[1]!='0' && $ud[2]!='0' ){
                                   $userId = $ud[1]."".$ud[2]."".$ud[3];

                                }
                                else{
                                   $userId = $ud[0]."".$ud[1]."".$ud[2]."".$ud[3];

                                }

                                */


                       

                                echo "<tr>
                                    <td>{$orderID}</td>"; ?>

                                    <td><?php 
                                    
                                    $userId = $order['uid'];

                                    $data_u = "SELECT `storeadmin_id`, `first_name` FROM `device_users` where `id`='$userId'";
                                    $result_u = mysqli_query($conn, $data_u);

                                    while ($row_u = mysqli_fetch_array($result_u, MYSQLI_ASSOC)) {
                                    $name_u = $row_u['storeadmin_id']; 


                                    $username = $row_u['first_name'];

                                    $data_s = "SELECT `name` FROM `admin` where `admin_id`='$name_u' ";
                                    $result_s = mysqli_query($conn, $data_s);

                                    while ($row_s = mysqli_fetch_array($result_s, MYSQLI_ASSOC)) {
                                    
                                    echo ucfirst($row_s['name']);
                                  

                                
                                    ?>
                                        
                                    </td>
                                    <td>
                                        <?php echo ucfirst($username);  
                                   

                                } }

                                ?>
                                    </td>
                          <?php echo "<td class='col-md-3'>{$address}</td>
                                    <td>{$phone}</td>
                                    <td>{$orderDate}</td>
                                    <td class='col-md-3'>{$orderStatusHTML}</td>
                                    <td>
                                        <a href='vieworderdetails.php?id={$id}'>
                                            <span class='label'>
                                                <button class='btn btn-primary'>
                                                    View <i class='fa fa-eye' aria-hidden='true'></i>
                                                </button>
                                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        <a href='orderstatusdetails.php?id={$id}'>
                                            <span class='label'>
                                                <button class='btn btn-success'>Edit</button>
                                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        <a href='deleteorderdetails.php?id={$id}'>
                                            <span class='label'>
                                                <button class='btn btn-danger' onClick='return checkDelete()'>
                                                    Delete <i class='fa fa-trash' aria-hidden='true'></i>
                                                </button>
                                            </span>
                                        </a>
                                    </td>
                                </tr>";
                            }
        }
        else{
                            $orders_user = getOrders_user($conn);
                                foreach ($orders_user as $order) {
                                $id = $order["id"];
                                $orderID = $order["order_id"];
                                $address = (!empty($order["address"])) ? $order["address"] : "NA";
                                $phone = (!empty($order["phone"])) ? $order["phone"] : "NA";
                                $orderDate = $order["order_date"];
                                $orderStatus = $order["order_status"];

                                $orderStatusHTML = "";

                                if ($orderStatus == "In-Processing") {
                                    $orderStatusHTML = "<div class='progress progress-xs progress-striped active'>
                                        <div class='progress-bar progress-bar-primary' style='width: 10%'></div>
                                        </div></div><br>{$orderStatus}";
                                } elseif ($orderStatus == "Dispatch") {
                                    $orderStatusHTML = "<div class='progress progress-xs progress-striped active'>
                                        <div class='progress-bar progress-bar-yellow' style='width: 80%'></div>
                                        </div></div><br>{$orderStatus}";
                                } elseif ($orderStatus == "Complete") {
                                    $orderStatusHTML = "<div class='progress progress-xs progress-striped active'>
                                        <div class='progress-bar progress-bar-success' style='width: 100%'></div>
                                        </div></div><br>{$orderStatus}";
                                } elseif ($orderStatus == "Cancel") {
                                    $orderStatusHTML = "<div class='progress progress-xs progress-striped active'>
                                        <div class='progress-bar progress-bar-danger' style='width: 100%'></div>
                                        </div></div><br>{$orderStatus}";
                                }


                           /*      $od = substr($order["order_id"],-4);  
                                $ud = str_split($od);
                                $userId = $ud[3];



                                if($ud[0]=='0' && $ud[1]=='0' && $ud[2]=='0'){
                                   $userId = $ud[3];
                                }
                                else if( $ud[0]=='0' && $ud[1]=='0' && $ud[2]!='0'){
                                   $userId = $ud[2]."".$ud[3];

                                }
                                else if( $ud[0]=='0' && $ud[1]!='0' && $ud[2]!='0' ){
                                   $userId = $ud[1]."".$ud[2]."".$ud[3];

                                }
                                else{
                                   $userId = $ud[0]."".$ud[1]."".$ud[2]."".$ud[3];

                                }

                                */


                       

                                echo "<tr>
                                    <td>{$orderID}</td>"; ?>

                                    <td><?php 
                                    
                                    $userId = $order['uid'];

                                    $data_u = "SELECT `storeadmin_id`, `first_name` FROM `device_users` where `id`='$userId'";
                                    $result_u = mysqli_query($conn, $data_u);

                                    while ($row_u = mysqli_fetch_array($result_u, MYSQLI_ASSOC)) {
                                    $name_u = $row_u['storeadmin_id']; 


                                    $username = $row_u['first_name'];

                                     $data_s = "SELECT `name` FROM `admin` where `admin_id`='$name_u' ";
                                    $result_s = mysqli_query($conn, $data_s);

                                    while ($row_s = mysqli_fetch_array($result_s, MYSQLI_ASSOC)) {
                                    
                                    echo ucfirst($row_s['name']);
                                  

                                
                                    ?>
                                        
                                    </td>
                                    <td>
                                        <?php echo ucfirst($username);  
                                   

                                } }

                                ?>
                                    </td>
                          <?php echo "<td class='col-md-3'>{$address}</td>
                                    <td>{$phone}</td>
                                    <td>{$orderDate}</td>
                                    <td class='col-md-3'>{$orderStatusHTML}</td>
                                    <td>
                                        <a href='vieworderdetails.php?id={$id}'>
                                            <span class='label'>
                                                <button class='btn btn-primary'>
                                                    View <i class='fa fa-eye' aria-hidden='true'></i>
                                                </button>
                                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        <a href='orderstatusdetails.php?id={$id}'>
                                            <span class='label'>
                                                <button class='btn btn-success'>Edit</button>
                                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        <a href='deleteorderdetails.php?id={$id}'>
                                            <span class='label'>
                                                <button class='btn btn-danger' onClick='return checkDelete()'>
                                                    Delete <i class='fa fa-trash' aria-hidden='true'></i>
                                                </button>
                                            </span>
                                        </a>
                                    </td>
                                </tr>";
                            }
        }
                            

                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function () {
        $('#employee_data').DataTable({
            "scrollX"       : true,
            'paging'        : true,
            "processing"    : true,
            'searching'     : true,
            'ordering'      : true,
            'order'         : [[ 3, "desc" ]],
            'info'          : true,
            'autoWidth'     : false
        });
    });
</script>

<?php require_once("scriptfooter.php"); ?>
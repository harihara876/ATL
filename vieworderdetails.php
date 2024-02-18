<?php
require_once("header.php");
require_once("sidebar.php");
require_once("widget.php");
error_reporting(0);
$id = $_GET["id"];

$editimageshandel = "SELECT * FROM admin where `admin_id`={$_SESSION['id']}";
$gethandel = mysqli_query($conn, $editimageshandel);
$resulthandel = mysqli_fetch_array($gethandel, MYSQLI_ASSOC);
$imagehandel = $resulthandel['currency'];
$tax1 = $resulthandel['tax'];
$ship = $resulthandel['shipping'];
$store_name = $resulthandel['store_name'];
$address = $resulthandel['address'];
//var_dump($imagehandel);


$orderdetails="SELECT * FROM users_orders where id=".$id;
$orderquery=mysqli_query($conn, $orderdetails);
$orderdata=mysqli_fetch_array($orderquery);



$userdetails="SELECT * FROM users_profile where uid='".$orderdata['uid']."'";
$userquery=mysqli_query($conn, $userdetails);
$userdata=mysqli_fetch_array($userquery);
//var_dump($userdata);

$sqlwish="SELECT * FROM `ordered_product` WHERE `order_id`=".$orderdata['id'];
 $checkcart= mysqli_query($conn, $sqlwish);
$productdata=mysqli_fetch_array($checkcart);


$qrycurr = "SELECT * FROM admin where `admin_id`={$_SESSION['id']} ";
    $res = mysqli_query($conn, $qrycurr);
    $admindata = mysqli_fetch_array($res, MYSQLI_ASSOC);
    $tax=$admindata['tax'];
    // if($admindata['currency']=='USD'){ $currency='$'; }else{$currency='₹';}
    $currency = "$";

 $userId = $orderdata['uid'];

$data_u = "SELECT `storeadmin_id`, `first_name` FROM `device_users` where `id`='$userId'";
 $result_u = mysqli_query($conn, $data_u);
$row_u = mysqli_fetch_array($result_u, MYSQLI_ASSOC);
$name_u = $row_u['storeadmin_id'];
$Device_username = ucfirst($row_u['first_name']);

$data_s = "SELECT `name` FROM `admin` where `admin_id`='$name_u' ";
$result_s = mysqli_query($conn, $data_s);
$row_s = mysqli_fetch_array($result_s, MYSQLI_ASSOC);
$Admin_Name = ucfirst($row_s['name']);





?>

<section class="invoice">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
           <?php echo $store_name;?>
            <small class="pull-right"><?php echo $orderdata['order_date'];?></small>
          </h2>
        </div>
        <!-- /.col -->
      </div>
      <!-- info row -->
      <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
          From
          <address>
            <strong><?php echo $store_name;?></strong><br>
           <?php echo $address;?>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          To
          <address>
            <strong><?php echo $userdata['name'];?></strong><br>
            <?php echo $orderdata['address'];?><br>

            Phone: <?php echo $orderdata['phone'];?><br>
            Email: <?php echo $userdata['email'];?>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          <b>Invoice #<?php echo $orderdata['order_id'];?></b><br>
          <br>
          <b>Admin Name:</b> <?php echo $Admin_Name;?> <br>
          <b>User Name:</b> <?php echo $Device_username;?> <br>
          <b>Order ID:</b> <?php echo $orderdata['order_id'];?><br>
          <b>Payment Status:</b> <?php echo $orderdata['payment_status'];?> <br>


        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
          <table class="table table-striped">
            <thead>
            <tr>
              <th>Product Name</th>
              <th>Color</th>
              <th>Size</th>
              <th>Image</th>
              <th>Qty</th>
              <th>Price</th>
              <th>Tax</th>

            </tr>
            </thead>
            <tbody>
            <?php $totalPrice =0; $totalPrice1=0;$totalTax=0;
			  foreach($checkcart as $cartiteams){ ?>

<tr>
              <td><?php echo $cartiteams['product_name'];?></td>
              <td><?php echo $cartiteams['color'];?> </td>
              <td><?php echo $cartiteams['size'];?> </td>
              <td><img src="<?php echo $cartiteams['product_image'];?>" height="50" width="40"></td>
              <td><?php echo $cartiteams['quantity'];?></td>
              <td><?php echo $currency; echo round($cartiteams['sellprice'], 2);?></td>
              <td>
                <?php
                  $taxAmount = $cartiteams["sellprice"] * $cartiteams["tax"];
                  echo $currency; echo round($taxAmount, 2); echo " (" . round($cartiteams["tax"], 2) . "%)";
                ?>
              </td>
            </tr>
           <?php  $totalPrice1 += round($cartiteams['quantity'] * $cartiteams['sellprice'], 2);
                  if($cartiteams["Special_Value"] != '0'){
                      $tp = $cartiteams["Special_Value"]* $totalPrice1;
                    }else{
                      $tp = 0;
                    }
                  $totalPrice += $tp + $totalPrice1;

           ?>
           <?php
              $totalTax += round($cartiteams['quantity'] * $taxAmount, 2);
            ?>
            <?php }?>

            </tbody>
          </table>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <div class="row">
        <!-- accepted payments column -->
        <div class="col-xs-6">
          <p class="lead">Payment Methods: <?php echo $orderdata['paymentmode']; ?><?php if($orderdata['paymentmode']=='Stripe Payment-Gateway'){  ?></p>

          <img src="dist/img/credit/visa.png" alt="Visa">
          <img src="dist/img/credit/mastercard.png" alt="Mastercard">
          <img src="dist/img/credit/american-express.png" alt="American Express">
          <img src="dist/img/credit/paypal2.png" alt="Paypal">

          <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
           <b>Payment TXN ID: <?php echo $orderdata['paymentref'];?></b>
          </p>
          <?php }elseif($orderdata['paymentmode']=='RazorPay Payment-Gateway'){ ?>

          <br><img src="razor.png" width="200">

           <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
           <b>Payment TXN ID: <?php echo $orderdata['paymentref'];?></b>
          </p>
          <?php }elseif($orderdata['paymentmode']=='Paypal Payment-Gateway'){ ?>

          <br><img src="paypal.png"  height="30" width="120">

           <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
           <b>Payment TXN ID: <?php echo $orderdata['paymentref'];?></b>
          </p>
          <?php }else{?>
          <br><img src="cod.png" alt="Visa" height="70" width="200">
           <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
           <b>Payment TXN ID: <?php echo $orderdata['paymentref'];?></b>
          </p>
          <?php }?>
        </div>
        <!-- /.col -->
        <div class="col-xs-6">
          <p class="lead"><?php echo $orderdata['order_date'];?></p>

          <div class="table-responsive">
            <table class="table">
              <tbody><tr>
                <th style="width:50%">Subtotal:</th>
                <td><?php echo $currency; echo round($totalPrice, 2); ?></td>
              </tr>
              <tr>
                <!-- <th>Tax (<?php // echo $admindata['tax'];?> %)</th> -->
                <th>Tax:</th>
                <!-- <td><?php //echo $currency; echo $tax1=round($totalPrice * ( $tax/ 100), 2);?></td> -->
                <td><?php echo $currency; echo $tax1=round($totalTax, 2);?></td>
              </tr>
              <!-- <tr>
                <th>Shipping:</th>
                <td><?php //echo $currency; echo $shipping=$admindata['shipping'];?></td>
              </tr> -->
              <tr>
                <th>Total:</th>
                <!-- <td><?php //echo $currency; echo $grand=round($totalPrice+$tax1+$shipping, 2);?></td> -->
                <td><?php echo $currency; echo $grand=round($totalPrice+$tax1, 2);?></td>
              </tr>
            </tbody></table>
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->


      </div>
    </section>
<?php include'scriptfooter.php';?>
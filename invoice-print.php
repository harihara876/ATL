<?php
include_once'config.php';

$qrycurr = "SELECT * FROM admin ";
    $res = mysqli_query($conn, $qrycurr);
    $admindata = mysqli_fetch_array($res, MYSQLI_ASSOC);
    $tax=$admindata['tax']; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title> <?php echo $admindata['store_name'];?>| Invoice</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body onload="window.print();">
<?php
$id=$_GET['id'];
$id= base64_decode($id);
//echo $id;

$editimageshandel="SELECT * FROM admin";
$gethandel=mysqli_query($conn, $editimageshandel);
$resulthandel=mysqli_fetch_array($gethandel,MYSQLI_ASSOC);
$imagehandel=$resulthandel['currency'];
$tax1=$resulthandel['tax'];
$ship=$resulthandel['shipping'];
$store_name=$resulthandel['store_name'];
$address=$resulthandel['address'];
//var_dump($imagehandel);


$orderdetails="SELECT * FROM users_orders where id='".$id."'";
$orderquery=mysqli_query($conn, $orderdetails);
$orderdata=mysqli_fetch_array($orderquery);
//var_dump($orderdata);



$userdetails="SELECT * FROM users_profile where uid='".$orderdata['uid']."'";
$userquery=mysqli_query($conn, $userdetails);
$userdata=mysqli_fetch_array($userquery);
//var_dump($userdata);

$sqlwish="SELECT * FROM `ordered_product` WHERE `order_id`='".$orderdata['order_id']."' ";
 $checkcart= mysqli_query($conn, $sqlwish);
$productdata=mysqli_fetch_array($checkcart);
//var_dump($productdata);


$qrycurr = "SELECT * FROM admin ";
    $res = mysqli_query($conn, $qrycurr);
    $admindata = mysqli_fetch_array($res, MYSQLI_ASSOC);
    $tax=$admindata['tax'];
    if($admindata['currency']=='USD'){ $currency='$'; }else{$currency='₹';}
?>




<section class="invoice">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
           <span class="logo-lg"><b><?php echo $store_name;?> <img src="logo.png" height="50" width="50"></b></span>
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
          <b>Order ID:</b> <?php echo $orderdata['order_id'];?><br>
          <b>Payment Status:</b><?php echo $orderdata['payment_status'];?> <br>

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
              <th>Qty</th>
              <th>Product Name</th>
<th>Color</th>
<th>Size</th>
              <th>Image</th>
              <th>Price</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($checkcart as $cartiteams){ ?>

<tr>
              <td><?php echo $cartiteams['quantity'];?></td>
              <td><?php echo $cartiteams['product_name'];?></td>
              <td><?php echo $cartiteams['color'];?> </td>
              <td><?php echo $cartiteams['size'];?> </td>

              <td><img src="<?php echo $cartiteams['product_image'];?>" height="50" width="40"></td>
              <td><?php echo $currency; echo $cartiteams['sellprice'];?></td>
            </tr>
           <?php  $totalPrice += $cartiteams['quantity'] * $cartiteams['sellprice'];?>
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
          <p class="lead">Payment Methods:<?php echo $orderdata['paymentmode']; ?><?php if($orderdata['paymentmode']=='Stripe Payment-Gateway'){  ?></p>

          <img src="dist/img/credit/visa.png" alt="Visa">
          <img src="dist/img/credit/mastercard.png" alt="Mastercard">
          <img src="dist/img/credit/american-express.png" alt="American Express">
          <img src="dist/img/credit/paypal2.png" alt="Paypal">

          <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
           <b>Payment TXN ID: <?php echo $orderdata['paymentref'];?></b>
          </p>
          <?php }elseif($orderdata['paymentmode']=='Paytm Payment-Gateway'){ ?>

          <br><img src="paytm.png" width="70">

           <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
           <b>Payment TXN ID: <?php echo $orderdata['paymentref'];?></b>
          </p>
          <?php }elseif($orderdata['paymentmode']=='Paypal Payment-Gateway'){ ?>

          <br><img src="paypal.png"  height="30" width="120">

           <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
           <b>Payment TXN ID: <?php echo $orderdata['paymentref'];?></b>
          </p>
          <?php }else{?>
          <br><br><img src="cod.png" alt="Visa" height="70" width="200">
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
                <td><?php echo $currency; echo $totalPrice; ?></td>
              </tr>
              <tr>
                <th>Tax (<?php echo $admindata['tax'];?> %)</th>
                <td><?php echo $currency; echo $tax1=$totalPrice * ( $tax/ 100);?></td>
              </tr>
              <tr>
                <th>Shipping:</th>
                <td><?php echo $currency; echo $shipping=$admindata['shipping'];?></td>
              </tr>
              <tr>
                <th>Total:</th>
                <td><?php echo $currency; echo $grand=$totalPrice+$tax1+$shipping;?></td>
              </tr>
            </tbody></table>
          </div>
        </div>
        <!-- /.col -->
      </div>
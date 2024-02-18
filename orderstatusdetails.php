<?php include 'header.php'; ?>
  <?php include 'sidebar.php'; ?>
<?php include 'widget.php'; ?>

<script language="JavaScript" type="text/javascript">
 function checkcancel(){
    return confirm('Are you sure you want to Cancel?');
}
</script>
<?php

$id=$_GET['id'];
//$id= base64_decode($id);
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

$sqlwish="SELECT * FROM `ordered_product` WHERE `order_id`='".$orderdata['id']."' ";
$checkcart= mysqli_query($conn, $sqlwish);
$productdata=mysqli_fetch_array($checkcart);
//var_dump($productdata);


$qrycurr = "SELECT * FROM admin ";
    $res = mysqli_query($conn, $qrycurr);
    $admindata = mysqli_fetch_array($res, MYSQLI_ASSOC);
    $tax=$admindata['tax'];
    //if($admindata['currency']=='USD'){ $currency='$'; }else{$currency='₹';}
    if($admindata['currency']=='USD'){ $currency='$'; }else{$currency='$';}
?>
<section class="invoice">
      <!-- title row -->
      <div class="row">
      <?php if($orderdata['order_status']=='Complete'){ echo '<center><h1>Order Completed</h1></center>';}

      if($orderdata['order_status']=='Cancel'){ echo '<center><h1>Order canceled</h1></center>';}?>
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
          <b>Order ID:</b> <?php echo $orderdata['order_id'];?><br>
          <b>Payment Status:</b><?php echo $orderdata['payment_status'];?><br>

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
            <?php $totalPrice=0;
			    foreach($checkcart as $cartiteams){ ?>

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
      <!-- /.row -->

      <!-- this row will not appear when printing -->
      <div class="row no-print">
        <div class="col-xs-12">
<?php  if($orderdata['paymentmode']=='COD' && $orderdata['paymentref']=='COD' &&  $orderdata['order_status']=='In-Processing' || $orderdata['order_status']=='Complete' || $orderdata['order_status']=='Dispatch'  ){?>
          <a href="submitpayment.php?id=<?php echo base64_encode($id);?>" class="btn btn-success"><i class="fa fa-credit-card"></i> Submit payment</a>
<?php }elseif($orderdata['paymentmode']=='COD' && $orderdata['order_status']=='Cancel'){ ?><a class="btn btn-success" disabled><i class="fa fa-credit-card"></i> Submit payment</a><?php }?>

          <?php  if($orderdata['order_status']=='In-Processing'){?>

          <a href="updateorderdetails.php?id=<?php echo base64_encode($id);?>&case=<?php echo base64_encode('Dispatch');?>" class="btn btn-info pull-right" style="margin-right: 5px;">
            <i class="fa fa-paper-plane-o"></i> Dispatch
          </a>


           <a href="updateorderdetails.php?id=<?php echo base64_encode($id);?>&case=<?php echo base64_encode('Complete');?>" class="btn btn-success pull-right" style="margin-right: 5px;">
            <i class="fa fa-check"></i> Complete
          </a>



           <a href="updateorderdetails.php?id=<?php echo base64_encode($id);?>&case=<?php echo base64_encode('Cancel');?>" class="btn btn-danger pull-right" style="margin-right: 5px;">
            <i class="fa fa-times"></i> Cancel
          </a>

          <a   class="btn btn-primary pull-right" style="margin-right: 5px;" disabled>
            <i class="fa fa-download"></i> Generate Invoice
          </a>

          <?php }elseif($orderdata['order_status']=='Dispatch'){  ?>

          <a  class="btn btn-info pull-right" style="margin-right: 5px;" disabled>
            <i class="fa fa-paper-plane-o"></i> Dispatch
          </a>


           <a href="updateorderdetails.php?id=<?php echo base64_encode($id);?>&case=<?php echo base64_encode('Complete');?>" class="btn btn-success pull-right" style="margin-right: 5px;">
            <i class="fa fa-check"></i> Complete
          </a>



           <a href="updateorderdetails.php?id=<?php echo base64_encode($id);?>&case=<?php echo base64_encode('Cancel');?>" class="btn btn-danger pull-right" style="margin-right: 5px;">
            <i class="fa fa-times"></i> Cancel
          </a>

          <a  class="btn btn-primary pull-right" style="margin-right: 5px;" disabled>
            <i class="fa fa-download"></i> Generate Invoice
          </a>
          <?php }?>


          <?php if($orderdata['order_status']=='Complete'){  ?>

          <a  class="btn btn-info pull-right" style="margin-right: 5px;" disabled>
            <i class="fa fa-paper-plane-o"></i> Dispatch
          </a>


           <a class="btn btn-success pull-right" style="margin-right: 5px;" disabled>
            <i class="fa fa-check"></i> Complete
          </a>



           <a class="btn btn-danger pull-right" style="margin-right: 5px;" disabled>
            <i class="fa fa-times"></i> Cancel
          </a>

          <a  href="invoice-print.php?id=<?php echo base64_encode($id);?>" class="btn btn-primary pull-right" style="margin-right: 5px;" >
            <i class="fa fa-download"></i> Generate Invoice
          </a>

          <?php }?>

           <?php if($orderdata['order_status']=='Cancel'){  ?>
           <a  class="btn btn-info pull-right" style="margin-right: 5px;" disabled>
            <i class="fa fa-paper-plane-o"></i> Dispatch
          </a>


           <a class="btn btn-success pull-right" style="margin-right: 5px;" disabled>
            <i class="fa fa-check"></i> Complete
          </a>



           <a class="btn btn-danger pull-right" style="margin-right: 5px;" disabled>
            <i class="fa fa-times"></i> Cancel
          </a>

           <a  class="btn btn-primary pull-right" style="margin-right: 5px;" disabled>
            <i class="fa fa-download"></i> Generate Invoice
          </a>
           <?php }?>



        </div>
      </div>
    </section>
<?php include'scriptfooter.php';?>
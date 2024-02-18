<?php include 'header.php'; ?>
  <?php include 'sidebar.php'; ?>
<?php include 'widget.php'; ?>


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



$paid='succeeded';
$paymentref='Txn_'.uniqid(mt_rand(),true);


 $orderdetails="UPDATE users_orders SET payment_status='".$paid."',paymentref='".$paymentref."' where id='".$id."' AND paymentmode='COD'";
$orderquery=mysqli_query($conn, $orderdetails);

if($orderquery==TRUE)
{
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Payment received ",
  text: "order status changed to Paid",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href="orderstatusdetails.php?id=<?php echo base64_encode($id);?>";
//console.log('The Ok Button was clicked.');
});
</script>
<?php }else{ ?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
   title: "Payment received fail ",
  text: "order status changed to UnPaid",
  icon: "error",button: "close"
}).then(function() {
// Redirect the user
window.location.href="orderstatusdetails.php?id=<?php echo base64_encode($id);?>";
//console.log('The Ok Button was clicked.');
});
</script>
<?php } ?>

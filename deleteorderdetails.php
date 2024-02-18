<?php
include'header.php';    
    
 $id=$_GET['id'];
       $id=base64_decode($id); 
 
$orderdetails="SELECT * FROM users_orders where id='".$id."'";
$orderquery=mysqli_query($conn, $orderdetails);
$orderdata=mysqli_fetch_array($orderquery,MYSQLI_ASSOC);
$orderid=$orderdata['order_id'];
//echo $orderid;

$orderdetails="SELECT * FROM ordered_product where order_id='".$orderid."'";
$orderquery=mysqli_query($conn, $orderdetails);
$orderdata1=mysqli_fetch_array($orderquery,MYSQLI_ASSOC);
$image=$orderdata1['product_image'];

//echo $image;
//exit();
unlink($image);

$orderdetails="delete FROM users_orders where id='".$id."'";
$orderquery=mysqli_query($conn, $orderdetails);

$sqlwish="delete  FROM `ordered_product` WHERE `order_id`='".$orderid."' ";
 $checkcart= mysqli_query($conn, $sqlwish);
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "order Deleted",
  text: "Successfully",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "orders.php";
//console.log('The Ok Button was clicked.');
});
</script>
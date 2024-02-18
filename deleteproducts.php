<?php include'header.php';

$id=$_GET['id'];
$id=base64_decode($id);
  //var_dump($id);

// SUPER - ADMIN
if($_SESSION['type_app']  == 'ADMIN'){

$sql="SELECT * FROM `products` where id='".$id."' ";
$check= mysqli_query($conn, $sql);
$resultcheck= mysqli_fetch_array($check,MYSQLI_ASSOC);
$product_id=$resultcheck['product_id'];
$category_id= $resultcheck['Category_Id'];


if($category_id){
	$selectSQL = "SELECT COUNT(*) AS `count` FROM `products` WHERE `Category_Id`='".$category_id."'";
	$result = mysqli_query($conn, $selectSQL);
	$count =  mysqli_fetch_array($result, MYSQLI_ASSOC)["count"];
	if($count==1){
		// delete category
		$query="DELETE FROM `category` where cat_id='".$category_id."' ";
		$result=mysqli_query($conn,$query) or die("not Deleted". mysql_error());
		// delete subcategory
		$query="DELETE FROM `subcategories` where cat_id='".$category_id."' ";
		$result=mysqli_query($conn,$query) or die("not Deleted". mysql_error());
	}
}

$query="DELETE FROM `products` where product_id='".$product_id."' ";
$result=mysqli_query($conn,$query) or die("not Deleted". mysql_error());

if($result==TRUE)   {
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
	swal({
		title: "Product deleted successfully",
		icon: "success",
		button: "close"
	}).then(function() {
		window.location.href = "products.php";
	});
</script>

<?php }



$query="DELETE FROM `product_details` where product_id='".$product_id."' ";
$result=mysqli_query($conn,$query) or die("not Deleted". mysql_error());

if($result==TRUE)   { ?>


<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
	swal({
		title: "Product deleted from categories successfully",
		icon: "success",
		button: "close"
	}).then(function() {
		window.location.href = "products.php";
	});
</script>

<?php }
$imagepath=array();

$query="SELECT image FROM `product_images` where product_id='".$product_id."' ";

$result=mysqli_query($conn,$query);

$data=mysqli_fetch_array($result,MYSQLI_ASSOC);

$imagetocopybefor=$data['image'];
$imagetocopy=str_replace('uploads/products','',$imagetocopybefor);
//echo $imagetocopy;

copy($imagetocopybefor, 'uploads/order/'.$imagetocopy);

$final='uploads/order'.$imagetocopy;



$query="UPDATE  `ordered_product` SET product_image='".$final."' where product_id='".$product_id."' ";
          $result=mysqli_query($conn,$query) or die("not Deleted". mysql_error());

//foreach($result as $image){ $imagepath[]=str_replace($serverimg,'',$image['image']); }



$query1="DELETE FROM `product_images` where product_id='".$product_id."' ";
          $result1=mysqli_query($conn,$query1) or die("not Deleted". mysql_error());


$query="DELETE FROM `slider` where product_id='".$product_id."' ";
          $result=mysqli_query($conn,$query) or die("not Deleted". mysql_error());


foreach($imagepath as $del){ unlink($del);}


if($result==TRUE)   {?>


<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script type="text/javascript">
	swal({
		title: "Product deleted",
		icon: "success",
		button: "close"
	}).then(function() {
		window.location.href = "products.php";
	});
</script>

<?php }

}

// STORE - ADMIN

else{


 $sql="SELECT * FROM `products` where id='".$id."' ";
 $check= mysqli_query($conn, $sql);
 $resultcheck= mysqli_fetch_array($check,MYSQLI_ASSOC);
 //echo'<pre>'; var_dump($resultcheck);
 $product_id=$resultcheck['product_id'];

 $query="DELETE FROM `product_details` where `storeadmin_id`='{$_SESSION['id']}' and product_id='".$product_id."' ";
 $result=mysqli_query($conn,$query) or die("not Deleted". mysql_error());

if($check==TRUE)   {
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
	swal({
		title: "Product deleted successfully",
		icon: "success",
		button: "close"
	}).then(function() {
		window.location.href = "products.php";
	});
</script>

<?php }
}
?>
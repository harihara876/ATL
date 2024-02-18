<?php
error_reporting(0); 
if($_POST['price']){
   require_once("config.php");
   $temp_product_id = $_POST['temp_product_id'];
   $product_sellprice = '$'.$_POST['price'];
   $insert_newprice_up = "UPDATE `products_temp` SET `selling_price`='{$product_sellprice}',`regular_price`='{$product_sellprice}' WHERE id='{$temp_product_id}' and storeadmin_id='{$_SESSION['id']}'";
   $successmain_newprice_up = mysqli_query($conn, $insert_newprice_up);
   echo 1;
   exit; 
}

include 'header.php';	
if(isset($_POST['addcat'])){
 //echo 'button pressed';
$UPC_name = $_POST['catname'];
///////////////////////////////////////////////////


$sql_n="SELECT * FROM `products` where `UPC`='{$UPC_name}' ";
         // var_dump($sql);
          $check_n= mysqli_query($conn, $sql_n);
          $resultcheck_n= mysqli_fetch_array($check_n,MYSQLI_BOTH);
         // $catname=$resultcheck['category_name'];
          //$cat_id=$resultcheck['Category_Id'];
          $checkcat_n=$resultcheck_n['UPC'];
          
//foreach($check as $checkcat){

// UPC is not present send it to Temp products page.
if($checkcat_n!=$UPC_name)
{
$product_name = 'Unknown_'.$UPC_name;
//$sql="SELECT max(product_id) as product_id FROM `appuser_productsmain`";
$sql ="SELECT IFNULL(MAX(product_id), 0) product_id FROM(SELECT product_id FROM products UNION ALL SELECT product_id FROM products_temp) a";
$check= mysqli_query($conn, $sql);
$maxproduct= mysqli_fetch_array($check,MYSQLI_BOTH);
$maxproductid=$maxproduct['product_id']+1;
$category_insert1 = mysqli_query($conn,"INSERT INTO `products_temp` (`product_name`,`product_id`, `upc`,`upc_status_request`,`storeadmin_id`) VALUES( '{$product_name}','{$maxproductid}','{$UPC_name}','1','{$_SESSION['id']}')");
$last_inser_temp_product_id = mysqli_insert_id($conn);
?>
 <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
 <script type="text/javascript">	 
    swal({
     title: "UPC Doesnot Exist ",
     text: "Added in Admin UPC",
	 content: {
		element: "input",
		attributes: {
		  placeholder: "Enter Price",
		},
	  },
     icon: "error",
	}).then(function(value) {
	   var temp_product_id = <?php echo $last_inser_temp_product_id; ?>;
	   $.ajax({
		  url: 'add-UPC.php',
		  type: 'POST',
		  data: { price : value, temp_product_id : temp_product_id },
		  success: function(data){
			window.location="add-product.php";
		  }
	   });
	});
</script>
<?php
}
// if UPC is present then show edit option to show data and update the selling price and quantity.

else{
	
   $sql="SELECT p.id,UPC FROM `products` p LEFT JOIN product_details pd ON pd.product_id = p.product_id where p.`UPC`='{$UPC_name}' AND pd.storeadmin_id = '{$_SESSION['id']}'";
    $check= mysqli_query($conn, $sql);
    $resultcheck= mysqli_fetch_array($check,MYSQLI_BOTH);
    $UPC_id=$resultcheck['UPC'];
	$product_base_id = base64_encode($resultcheck['id']);
	if(!$UPC_id){
			
		$sql="SELECT * FROM `products` p LEFT JOIN product_details pd ON pd.product_id = p.product_id where p.`UPC`='{$UPC_name}'";
		$check= mysqli_query($conn, $sql);
		$resultcheck= mysqli_fetch_array($check,MYSQLI_BOTH);
		$cat_id=$resultcheck['Category_Id'];
		$product_name=$resultcheck['product_name'];
		$product_id=$resultcheck['product_id'];
		$description=$resultcheck['description'];
		$price=$resultcheck['price'];
		$sellprice=$resultcheck['sellprice'];
		$color=$resultcheck['color'];
		$size=$resultcheck['size'];
		$product_status=$resultcheck['product_status'];
		$quantity=$resultcheck['quantity'];
		$plimit=$resultcheck['plimit'];
		$UPC=$resultcheck['UPC'];
		$Regular_Price=$resultcheck['Regular_Price'];
		$Buying_Price=$resultcheck['Buying_Price'];
		$Tax_Status=$resultcheck['Tax_Status'];
		$Tax_Value=$resultcheck['Tax_Value'];
		$Special_Value=$resultcheck['Special_Value'];
		$Category_Id=$resultcheck['Category_Id'];
		$Category_Type=$resultcheck['Category_Type'];
		$Date_Created=$resultcheck['Date_Created'];
		$SKU=$resultcheck['SKU'];
		$Image=$resultcheck['Image'];
		$Stock_Quantity=$resultcheck['Stock_Quantity'];
		$Manufacturer=$resultcheck['Manufacturer'];
		$Brand=$resultcheck['Brand'];
		$Vendor=$resultcheck['Vendor'];
		$ProductMode=$resultcheck['ProductMode'];
		$Age_Restriction=$resultcheck['Age_Restriction'];
		$sale_type=$resultcheck['sale_type'];
		$status=$resultcheck['status'];
		$cat_id=$resultcheck['Category_Id'];

		$category_insert = mysqli_query($conn,"INSERT INTO product_details (`product_id`,`description`, `price`, `sellprice`, `color`, `size`, `product_status`, `quantity`, `plimit`, `Regular_Price`, `Buying_Price`, `Tax_Status`, `Tax_Value`, `Special_Value`, `Date_Created`, `SKU`, `Stock_Quantity`, `ProductMode`, `Age_Restriction`, `sale_type`, `status`, `storeadmin_id`) VALUES('{$product_id}', '{$description}', '{$price}', '{$sellprice}', '{$color}', '{$size}', '{$product_status}', '{$quantity}', '{$plimit}', '{$Regular_Price}', '{$Buying_Price}', '{$Tax_Status}', '{$Tax_Value}', '{$Special_Value}','{$Date_Created}', '{$SKU}', '{$Stock_Quantity}', '{$ProductMode}', '{$Age_Restriction}', '{$sale_type}', '{$status}','{$_SESSION['id']}')");
		//$last_inser_product_id = base64_encode(mysqli_insert_id($conn));
		$last_inser_product_id = base64_encode($resultcheck['id']);
		?>
		 <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
		 <script type="text/javascript">
		  swal({
		  title: "UPC Present ",
		  text: "Added Product Details",
		  icon: "success",button: "close"
		}).then(function() {
		// Redirect the user
    	  window.location.href = "editproductsdetails.php?id=<?php echo $last_inser_product_id; ?>";
		});
		</script>	
	<?php			
	  }else{
    ?>
   <script type="text/javascript">
	swal({
	  title: "UPC Product Already Exist ",
	  text: "Check your Store Products",
	  icon: "error",
	  button: "Edit",
	}).then(function() {
      window.location.href = "editproductsdetails.php?id=<?php echo $product_base_id; ?>";
	});
	</script>
<?php	
	}
}
}else {       
  header("location:dashboard.php"); 
}        

?>
<?php
require_once("scriptfooter.php");
?>
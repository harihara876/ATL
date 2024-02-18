<?php
require_once("header.php");

/**
 * Checks if product already exists.
 * @param object $conn DB connection object.
 * @return boolean Status.
 */
function checkIfProductAlreadyExists($conn)
{
    $upc = $_POST["upc"];
    $selectSQL = "SELECT COUNT(*) FROM `products` WHERE `UPC` = '{$upc}'";
    $result = mysqli_query($conn, $selectSQL);
    $count = mysqli_fetch_array($result, MYSQLI_ASSOC)["count"];

    if ($count) {
        return TRUE;
    }

    return FALSE;
}

/**
 * Adds new product.
 * @param object $conn DB connection object.
 * @return int Last insert ID.
 */
function addProduct($conn)
{
    $removeChars = function ($data) {
        $remove[] = "'";
        $remove[] = '"';
        $remove[] = "-"; // just as another example
        return str_replace($remove, "", $data);
    };

    $postVal = function ($key, $defaultValue = NULL) {
        return (isset($_POST[$key])) ? $_POST[$key] : $defaultValue;
    };

    $productID              = $postVal("product_id");
	$temp_product_id        = $postVal("temp_product_id");
    $productName            = $postVal("name");
    $productDesc            = (isset($_POST["editor1"])) ? htmlspecialchars($_POST["editor1"]) : NULL;
    $productSize            = $postVal("size");
    $productColor           = $postVal("colour");
    $productPrice           = $postVal("price");
    $productSellingPrice    = $postVal("sellprice");
    $productStatus          = $postVal("product_status");
    $productQuantity        = $postVal("quantity");
    $productLimit           = $postVal("plimit");
    $productUPC             = $postVal("upc");
    $productSKU             = $postVal("product-sku");
    $manufacturer           = $postVal("manufacturer");
    $brand                  = $postVal("brand");
    $vendor                 = $postVal("vendor");
    $ageRestriction         = $postVal("age-restriction");
    $category               = $postVal("chk");
    $subCategoryID          = $postVal("sub-category");
    $date                   = date("d/m/Y");
    $taxStatus              = $_POST["tax-status"];
    $taxValue               = $_POST["tax-value"];

    $productName            = $removeChars($productName);
    $productSize            = $removeChars($productSize);
    $productColor           = $removeChars($productColor);
    $productSellingPrice    = $removeChars($productSellingPrice);
    $productPrice           = $removeChars($productPrice);
    $productQuantity        = $removeChars($productQuantity);
    $productStatus          = $removeChars($productStatus);
	
	$image = "";
	$Special_Value = "0";
	$ProductMode ="0";
	$sale_type="0";
	$status =1;
	
	$sql="SELECT max(product_id) as product_id FROM `products`";
    $check= mysqli_query($conn, $sql);
    $maxproduct= mysqli_fetch_array($check,MYSQLI_BOTH);
    $pid=$maxproduct['product_id']+1;

    if (!$productID) {
        return FALSE;
    }
	
	if(!$temp_product_id){
	   $temp_product_id = rand(500,10000);
	}
	
	 $insertSQL = "INSERT INTO products (
					product_id,
					product_name,
					cat_id,
					UPC,
					Category_Id,
					Category_Type,
					Date_Created,
					Image,
					Manufacturer,
					Brand,
					Vendor,
					status
                  ) VALUES (
					'{$pid}',
					'{$productName}',
					 {$category},
					'{$productUPC}',
					 {$category},
					 {$subCategoryID},
					'{$date}',
					'{$image}',
					'{$manufacturer}',
					'{$brand}',
					'{$vendor}',
					'{$status}'
                  )";
		
		//echo $insertSQL;  die;
    $inserted = mysqli_query($conn, $insertSQL);
	$last_inserted_id = mysqli_insert_id($conn);

    if ($last_inserted_id) {
	   
	  /*copy for admin*/
	   $insertadmin = "INSERT INTO product_details(product_id,description,price,sellprice,color,size,product_status,quantity,plimit,Regular_Price,Buying_Price,Tax_Status,
Tax_Value,Special_Value,Date_Created,SKU,Stock_Quantity,ProductMode,Age_Restriction,sale_type,status,storeadmin_id
			) VALUES ('{$pid}','{$productDesc}','{$productPrice}','{$productSellingPrice}','{$productColor}','{$productSize}','{$productStatus}','{$productQuantity}','{$productLimit}',		'{$productPrice}','{$productSellingPrice}','{$taxStatus}','{$taxValue}','{$Special_Value}','{$date}','{$productSKU}','{$productQuantity}','{$ProductMode}','{$ageRestriction}','{$sale_type}','{$status}','{$_SESSION['id']}')";
	  $success= mysqli_query($conn, $insertadmin);
	  /*copy for admin end*/
	  
	  $targetFolder = "uploads/products";
      $targetorder = "uploads/order";

      $errorMsg = [];
      $successMsg = [];

        foreach($_FILES as $file => $fileArray) {
            if(!empty($fileArray['name']) && $fileArray['error'] == 0) {
                $getFileExtension = pathinfo($fileArray['name'], PATHINFO_EXTENSION);;

                if(($getFileExtension =='jpg') || ($getFileExtension =='jpeg') || ($getFileExtension =='png') || ($getFileExtension =='gif')) {
                    if ($fileArray["size"] <= 500000) {
                        $breakImgName = explode(".", $fileArray['name']);
                        $imageOldNameWithOutExt = $breakImgName[0];
                        $imageOldExt = $breakImgName[1];
                        $newFileName = strtotime("now")."-".str_replace(" ","-",strtolower($imageOldNameWithOutExt)).".".$imageOldExt;
                        $targetPath = $targetFolder."/".$newFileName;
                        $targetorders = $targetorder."/".$newFileName;
                        //$path=$serverimg.$targetFolder."/".$newFileName;

                        if (move_uploaded_file($fileArray["tmp_name"], $targetPath)) {
                            $insertplacemain = "INSERT INTO product_images (product_id,image) VALUES('{$pid}','{$targetPath}')";
                            $successmain = mysqli_query($conn, $insertplacemain);
                            if($successmain) {
                              
                            } else {
                                $errorMsg[$file] = "Unable to save ".$file." file ";
                            }
                        } else {
                            $errorMsg[$file] = "Unable to save ".$file." file ";
                        }
                    } else {
                        $errorMsg[$file] = "Image size is too large in ".$file;
                    }
                } else {
                    $errorMsg[$file] = 'Only image file required in '.$file.' position';
                }
            }
        }
				
      return $last_inserted_id;
  }
   return 0;
}

/**
 * Deletes temporary product.
 * @param object $conn DB connection object.
 * @return boolean Status.
 */
function deleteTempProduct($conn)
{
    $productID = (isset($_POST["product_id"])) ? $_POST["product_id"] : NULL;
    if (!$productID) {
        return FALSE;
    }
	
	$sql="SELECT upc FROM `products_temp2` where `id`='{$productID}'";
    $check= mysqli_query($conn, $sql);
    $resultcheck= mysqli_fetch_array($check,MYSQLI_BOTH);
    $UPC_id=$resultcheck['upc'];
    $deleteSQL  = "DELETE FROM `products_temp2` WHERE upc= '{$UPC_id}'";
    return mysqli_query($conn, $deleteSQL);
}

$alreadyExists = checkIfProductAlreadyExists($conn);
if ($alreadyExists) {
    echo <<<_SCRIPT_
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script type="text/javascript">
                swal({
                    title: "Product Already Exists",
                    icon: "error",
                    button: "close"
                }).then(function() {
                    window.location.href = "extra-new-products.php";
                });
            </script>
_SCRIPT_;
} else {
    $created = addProduct($conn);
    if ($created) {
        deleteTempProduct($conn);
        echo <<<_SCRIPT_
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script type="text/javascript">
                swal({
                    title: "Successfully Created",
                    icon: "success",
                    button: "close"
                }).then(function() {
                    window.location.href = "extra-new-products.php";
                });
            </script>
_SCRIPT_;
    } else {
        echo <<<_SCRIPT_
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script type="text/javascript">
                swal({
                    title: "Failed to Create Product",
                    text: "Please try later.",
                    icon: "error",
                    button: "close"
                }).then(function() {
                    window.location.href = "extra-new-products.php";
                });
            </script>
_SCRIPT_;
    }
}
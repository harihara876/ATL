<?php
require_once("header.php");

if (isset($_POST["addproduct"])) {
    $product_name = $_POST["name"];
    $remove[] = "'";
    $remove[] = '"';
    $remove[] = "-"; // just as another example
    $product_name = str_replace($remove, "", $product_name);

    $product_des = htmlspecialchars($_POST["editor1"]);

    $product_size = $_POST["size"];
    $remove[] = "";
    $remove[] = "'";
    $remove[] = '"';
    $remove[] = "-"; // just as another example
    $product_size = str_replace($remove, "", $product_size);
    //if($product_size==''){$product_size="NULL";}

    $product_color = $_POST["colour"];
    $remove[] = "'";
    $remove[] = '"';
    $remove[] = "-"; // just as another example
    $product_color = str_replace($remove, "", $product_color);
    //if($product_color==''){ $product_color="NULL"; }

    $product_price = $_POST["price"];
    $remove[] = "'";
    $remove[] = '"';
    $remove[] = "-"; // just as another example
    $product_price = str_replace($remove, "", $product_price);
    //$product_price=$product_price.$currency;

    $product_sellprice = $_POST["sellprice"];
    $remove[] = "'";
    $remove[] = '"';
    $remove[] = "-"; // just as another example
    $product_sellprice = str_replace($remove, "", $product_sellprice);
    //$product_sellprice=$product_sellprice.' '.$currency;

    $product_quntity = $_POST["quantity"];
    $remove[] = "'";
    $remove[] = '"';
    $remove[] = "-"; // just as another example
    $product_quantity = str_replace($remove, "", $product_quantity);

    $product_status = $_POST["product_status"];
    $remove[] = "'";
    $remove[] = '"';
    $remove[] = "-"; // just as another example
    $product_status = str_replace($remove, "", $product_status);

    //$pid = rand(500,10000);

	$sql="SELECT max(product_id) as product_id FROM `products`";
    $check= mysqli_query($conn, $sql);
    $maxproduct= mysqli_fetch_array($check,MYSQLI_BOTH);
    $pid=$maxproduct['product_id']+1;

    $dat = date("d/m/Y");

    $category           = $_POST["chk"];
    $product_limit      = $_POST["plimit"];
    $upc                = $_POST["product-upc"];
    $sku                = $_POST["product-sku"];
    $manufacturer       = $_POST["manufacturer"];
    $brand              = $_POST["brand"];
    $vendor             = $_POST["vendor"];
    $ageRestriction     = $_POST["age-restriction"];
    $subCategoryID      = $_POST["sub-category"];
    $taxStatus          = $_POST["tax-status"];
    $taxValue           = $_POST["tax-value"];

    $subCategoryID = (is_int($subCategoryID)) ? $subCategoryID : 0;

	$image = "";
	$Special_Value = "0";
	$ProductMode ="0";
	$sale_type="0";
	$status =1;

// SUPER - ADMIN

if($_SESSION['type_app']  == 'ADMIN'){


    $sql = "SELECT * FROM `products`";
    $check = mysqli_query($conn, $sql);

    foreach ($check as $checkcat) {
        if ($checkcat["product_name"] == $product_name) {
            $ok = 1;
        } else {
            $ok = 0;
        }
    }

    if ($ok == 1) {
?>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script type="text/javascript">
            swal({
                title: "Product Already Exist",
                text: "Choose a different name",
                icon: "error",button: "close"
            }).then(function() {
                // Redirect the user
                window.location.href = "add-product.php";
                //console.log('The Ok Button was clicked.');
            });
        </script>
<?php
    } else {


        $insertplacemain = "INSERT INTO products (
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
                '{$product_name}',
				 {$category},
                '{$upc}',
				 {$category},
                 {$subCategoryID},
                '{$dat}',
                '{$image}',
                '{$manufacturer}',
                '{$brand}',
                '{$vendor}',
                '{$status}'
            )";

		//echo $insertplacemain; die;
        $successmain= mysqli_query($conn, $insertplacemain);
        $lastid = $conn->insert_id;

        if($lastid) {
            $insertplace = "INSERT INTO product_details(
                    product_id,
                    description,
                    price,
					sellprice,
					color,
					size,
                    product_status,
                    quantity,
                    plimit,
					Regular_Price,
                    Buying_Price,
                    Tax_Status,
                    Tax_Value,
					Special_Value,
					Date_Created,
                    SKU,
                    Stock_Quantity,
                    ProductMode,
                    Age_Restriction,
                    sale_type,
                    status,
                    storeadmin_id
                ) VALUES (
                    '{$pid}',
                    '{$product_des}',
					'{$product_price}',
					'{$product_sellprice}',
					'{$product_color}',
                    '{$product_size}',
                    '{$product_status}',
                    '{$product_quntity}',
					'{$product_limit}',
                    '{$product_price}',
                    '{$product_sellprice}',
					'{$taxStatus}',
                    '{$taxValue}',
					'{$Special_Value}',
                    '{$dat}',
                    '{$sku}',
                    '{$product_quntity}',
					'{$ProductMode}',
                    '{$ageRestriction}',
					'{$sale_type}',
					'{$status}',
					'{$_SESSION['id']}'
                )";
            $success= mysqli_query($conn, $insertplace);
        }

        $sql = "SELECT * FROM `products` where id = '{$lastid}'";
        $check = mysqli_query($conn, $sql);

        foreach ($check as $row) {
            $product_id = $row["product_id"];
        }

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
                            $insertplacemain = "INSERT INTO product_images (product_id,image) VALUES('{$product_id}','{$targetPath}')";
                            $successmain = mysqli_query($conn, $insertplacemain);

                            if($successmain) {
?>
                                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                                <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                                <script type="text/javascript">
                                    swal({
                                        title: "Product Added ",
                                        text: "Successfully.All Image Upload complete.",
                                        icon: "success",button: "close"
                                    }).then(function() {
                                    // Redirect the user
                                    window.location.href = "add-product.php";
                                    //console.log('The Ok Button was clicked.');
                                    });
                                </script>
<?php
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
    }

}
// end of isset .
}
?>
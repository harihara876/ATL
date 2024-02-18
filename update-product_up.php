<?php

require_once("header.php");

if (isset($_POST['addproduct'])) {
    $pid = $_SESSION['product_id'];

    // Product name.
    $product_name = @$_POST['name'];
    $remove[] = "'";
    $remove[] = '"';
    $remove[] = "-"; // just as another example
    $product_name = str_replace($remove, "", $product_name);

    // Product description.
    $product_des = htmlspecialchars(@$_POST['editor1']);

    $product_size = @$_POST['size'];
    $remove[] = "";
    $remove[] = "'";
    $remove[] = '"';
    $remove[] = "-"; // just as another example
    $product_size = str_replace($remove, "", $product_size);

    //if($product_size==''){$product_size="NULL";}

    $product_color = @$_POST['colour'];
    $remove[] = "'";
    $remove[] = '"';
    $remove[] = "-"; // just as another example
    $product_color = str_replace($remove, "", $product_color);

    //if($product_color==''){ $product_color="NULL"; }
    $product_sellprice = @$_POST['sellprice'];
    $remove[] = "'";
    $remove[] = '"';
    $remove[] = "-"; // just as another example
    $product_sellprice = str_replace($remove, "", $product_sellprice);

    $product_price = @$_POST['price'];
    $remove[] = "'";
    $remove[] = '"';
    $remove[] = "-"; // just as another example
    $product_price = str_replace($remove, "", $product_price);

    $product_special_value = @$_POST['Special_Value'];
    $remove[] = "'";
    $remove[] = '"';
    $remove[] = "-"; // just as another example
    $product_special_value = str_replace($remove, "", $product_special_value);

    $product_quntity = @$_POST['quantity'];
    $remove[] = "'";
    $remove[] = '"';
    $remove[] = "-"; // just as another example
    $product_quntity = str_replace($remove, "", $product_quntity);

    $product_status = @$_POST['product_status'];
    $remove[] = "'";
    $remove[] = '"';
    $remove[] = "-"; // just as another example
    $product_status = str_replace($remove, "", $product_status);

    if ($product_status == "comingsoon") {
        $product_quntity = 0;
    }

    $category = @$_POST['chk'];
    $subcategory = @$_POST['sub-category'];
    $dat = date("d/m/Y");
    $product_limit = @$_POST['plimit'];

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ($_SESSION['type_app'] == 'ADMIN') {

        $insertplacemain_a = "UPDATE  products SET product_name='" . $product_name . "',Date_Created='" . $dat . "',`cat_id`='" . $category . "',`Category_Id`='" . $category . "',`Category_Type`='" . $subcategory . "'  WHERE product_id='" . $pid . "'";
        $successmain_a = mysqli_query($conn, $insertplacemain_a);

        $insertplacemain_a = "UPDATE  product_details SET Special_Value='" . $product_special_value . "',sellprice='" . $product_sellprice . "',description='" . $product_des . "',size='" . $product_size . "',price='" . $product_price . "',Regular_Price='" . $product_price . "',color='" . $product_color . "',quantity='" . $product_quntity . "',product_status='" . $product_status . "',Date_Created='" . $dat . "',plimit='" . $product_limit . "'  WHERE product_id='" . $pid . "'";
        $successmain_a = mysqli_query($conn, $insertplacemain_a);
    } else {
        $insertplacemain_b = "UPDATE  product_details SET Special_Value='" . $product_special_value . "',sellprice='" . $product_sellprice . "',price='" . $product_price . "',Regular_Price='" . $product_price . "', `quantity`='{$product_quntity}' WHERE `storeadmin_id`='{$_SESSION['id']}' and product_id='" . $pid . "'";
        $successmain_b = mysqli_query($conn, $insertplacemain_b);
    }


    $product_id = $_SESSION['product_id'];

    $targetFolder = "uploads/products";

    $errorMsg = array();
    $successMsg = array();
    foreach ($_FILES as $file => $fileArray) {

        if (!empty($fileArray['name']) && $fileArray['error'] == 0) {
            $getFileExtension = pathinfo($fileArray['name'], PATHINFO_EXTENSION);

            if (in_array($getFileExtension, ALLOWED_IMAGE_EXTENSIONS)) {
                if ($fileArray["size"] <= 1500000) {
                    $breakImgName = explode(".", $fileArray['name']);
                    $imageOldNameWithOutExt = $breakImgName[0];
                    $imageOldExt = $breakImgName[1];

                    $newFileName = strtotime("now") . "-" . str_replace(" ", "-", strtolower($imageOldNameWithOutExt)) . "." . $imageOldExt;


                    $targetPath = $targetFolder . "/" . $newFileName;
                    //$path=$serverimg.$targetFolder."/".$newFileName;

                    if (move_uploaded_file($fileArray["tmp_name"], $targetPath)) {


                        $insertplacemain = "INSERT INTO product_images (product_id,image) VALUES('" . $pid . "','" . $targetPath . "')";
                        $successmainimages = mysqli_query($conn, $insertplacemain);

                        if ($successmainimages) { ?>
                            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                            <script type="text/javascript">
                                swal({
                                    title: "Product Updated ",
                                    text: "Successfully.All Image Upload complete.",
                                    icon: "success",
                                    button: "close"
                                }).then(function() {
                                    // Redirect the user
                                    window.location.href = "products.php";
                                    //console.log('The Ok Button was clicked.');
                                });
                            </script>
        <?php
                        }
                    } else {
                        $errorMsg[$file] = "Unable to save " . $file . " file ";
                    }
                } else {
                    $errorMsg[$file] = "Unable to save " . $file . " file ";
                }
            } else {
                $errorMsg[$file] = "Image size is too large in " . $file;
            }
        } else {
            $errorMsg[$file] = 'Only image file required in ' . $file . ' position';
        }
    }
    if (@$successmain_a) { ?>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script type="text/javascript">
            swal({
                title: "Product Updated ",
                text: "Successfully Upload complete.",
                icon: "success",
                button: "close"
            }).then(function() {
                // Redirect the user
                window.location.href = "products.php";
                //console.log('The Ok Button was clicked.');
            });
        </script>
    <?php
    }

    if (@$successmain_b) { ?>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script type="text/javascript">
            swal({
                title: "Product Updated ",
                text: "Successfully Upload complete.",
                icon: "success",
                button: "close"
            }).then(function() {
                // Redirect the user
                window.location.href = "products.php";
                //console.log('The Ok Button was clicked.');
            });
        </script>
    <?php
    }

    if (@$successmain_newprice) { ?>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script type="text/javascript">
            swal({
                title: "Product Updated ",
                text: "Successfully Upload complete.",
                icon: "success",
                button: "close"
            }).then(function() {
                // Redirect the user
                window.location.href = "products.php";
                console.log('The Ok Button was clicked.');
            });
        </script>
    <?php
    }

    if (@$successmain_newprice_up) { ?>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script type="text/javascript">
            swal({
                title: "Product Updated ",
                text: "Successfully Upload complete.",
                icon: "success",
                button: "close"
            }).then(function() {
                // Redirect the user
                window.location.href = "products.php";
                console.log('The Ok Button was clicked.');
            });
        </script>
<?php
    }
}

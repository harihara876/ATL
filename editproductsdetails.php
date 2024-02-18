<?php
require_once("header.php");
require_once("sidebar.php");
require_once("widget.php");

function getProductInfo($db, $id, $storeAdminID)
{
    $selectSQL = "SELECT *
        FROM products p
        LEFT JOIN product_details pd
        ON pd.product_id = p.product_id
        WHERE p.id = :id
        AND `storeadmin_id` = :storeAdminID
        LIMIT 1";
    $stmt = $db->prepare($selectSQL);
    $stmt->bindValue(":id", $id);
    $stmt->bindValue(":storeAdminID", $storeAdminID);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>


<style>
    .row li {
        width: 33.3%;
        float: left;
    }

    img {
        border: 0 none;
        display: inline-block;

        max-width: 100%;
        vertical-align: middle;
    }

    .form-container {
        margin-left: 5px;
        margin-top: 10px;
    }

    .input-files input[type=file] {
        display: block;
        border: 1px solid #eeeeee;
        position: relative;
        margin-bottom: 5px;
        width: 250px;
    }

    .add-more-cont {
        margin: 10px 0px 10px 0px;
    }

    #add_more {
        font-size: 13px;
        color: blue;
    }

    #add_more:hover {
        cursor: pointer;
    }

    .error-msg {
        background-color: #f2dede;
        border: 1px solid #ebccd1;
        font-size: 14px;
        color: #a94442;
        width: 350px;
        padding: 4px;
        margin-bottom: 5px;
    }

    .success-msg {
        background-color: #dff0d8;
        border: 1px solid #d6e9c6;
        font-size: 14px;
        color: #3c763d;
        width: 350px;
        padding: 4px;
        margin-bottom: 5px;
    }

    div.show-image {
        position: relative;
        float: left;
        margin: 5px;
        height: 50%;
    }

    div.show-image:hover img {
        opacity: 0.5;
    }

    div.show-image:hover input {
        display: block;
    }

    div.show-image input {
        position: absolute;
        display: none;
    }

    div.show-image input.update {
        top: 0;
        left: 0;
    }

    div.show-image input.delete {
        top: 0;
        left: 79%;
    }
</style>

<?php
// TODO: Add validation.
$id = !empty($_GET["id"]) ? $_GET["id"] : NULL;
$id = base64_decode($id); //  Row ID. Not product_id. E.g. 6643

// if ($_SESSION["type_app"]  == "ADMIN") {
//     $productInfo = getProductInfo($db, $id, 1);
// } else {
//     $productInfo = getProductInfo($db, $id, $_SESSION["id"]);
// }

if ($_SESSION['type_app']  == 'ADMIN') {
    //echo $id;
    $edit = "SELECT *,`p`.`product_name` FROM products p LEFT JOIN product_details pd ON pd.product_id = p.product_id where p.id ='" . $id . "'";
    $getproducts = mysqli_query($conn, $edit);
    $resultofedit = mysqli_fetch_array($getproducts, MYSQLI_ASSOC);
    $product_id = $resultofedit['product_id'];
    $_SESSION['product_id'] = $product_id;
    $editimages = "SELECT * FROM product_images where product_id='" . $product_id . "'";
    $getproductimages = mysqli_query($conn, $editimages);
    $imagecounter = mysqli_num_rows($getproductimages);

    $editimageshandel = "SELECT image_handel FROM admin";
    $gethandel = mysqli_query($conn, $editimageshandel);
    $resulthandel = mysqli_fetch_array($gethandel, MYSQLI_ASSOC);
    $imagehandel = $resulthandel['image_handel'];
    //var_dump($imagehandel);
    $editcurrencyhandel = "SELECT currency FROM admin";
    $gethandelcurrency = mysqli_query($conn, $editcurrencyhandel);
    $resulthandelcurr = mysqli_fetch_array($gethandelcurrency, MYSQLI_ASSOC);
    $currency = $resulthandelcurr['currency'];
} else {
    // Get product details.
    $edit = "SELECT * FROM products p LEFT JOIN product_details pd ON pd.product_id = p.product_id where p.id ='" . $id . "' AND `storeadmin_id`='{$_SESSION['id']}'";
    $getproducts = mysqli_query($conn, $edit);
    $resultofedit = mysqli_fetch_array($getproducts, MYSQLI_ASSOC);

    $product_id = $resultofedit['product_id'];
    $_SESSION['product_id'] = $product_id;

    // Get product images.
    $editimages = "SELECT * FROM product_images where `storeadmin_id`='{$_SESSION['id']}' and  `type_app_admin`='{$_SESSION['type_app']}' and product_id='" . $product_id . "'";
    $getproductimages = mysqli_query($conn, $editimages);
    $imagecounter = mysqli_num_rows($getproductimages);

    $editimageshandel = "SELECT image_handel FROM admin where `admin_id`='{$_SESSION['id']}' and  `type_appstatus`='{$_SESSION['type_app']}' ";
    $gethandel = mysqli_query($conn, $editimageshandel);
    $resulthandel = @mysqli_fetch_array($gethandel, MYSQLI_ASSOC);
    $imagehandel = $resulthandel['image_handel'];
    //var_dump($imagehandel);
    $editcurrencyhandel = "SELECT currency FROM admin where `admin_id`='{$_SESSION['id']}' and  `type_appstatus`='{$_SESSION['type_app']}' ";
    $gethandelcurrency = mysqli_query($conn, $editcurrencyhandel);
    $resulthandelcurr = @mysqli_fetch_array($gethandelcurrency, MYSQLI_ASSOC);
    $currency = $resulthandelcurr['currency'];
}
?>
<?php if ($currency == 'USD') {
    $currency = '$';
} else if ($currency == 'INR') {
    $currency = '₹';
} else {
    $currency = '$';
} ?>


<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-shopping-cart fa-4" aria-hidden="true"></i> Edit Product</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="form-container">

            <a class="btn btn-primary pull-left" href="add-product.php"><i class="fa fa-plus-circle fa-6" aria-hidden="true"></i> Add Products</a><br>
            <hr>



            <form role="form" method="POST" enctype="multipart/form-data" action="update-product_up.php">
                <!-- text input -->
                <div class="form-group"></div>

                <div class="form-group">
                    <label>Product Name:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter name of your product"></i>
                    <input type="text" class="form-control" placeholder="Enter Name of product" name="name" value="<?php echo $resultofedit['product_name']; ?>" required <?php if ($_SESSION['type_app'] == 'storeadmin') {
                                                                                                                                                                                echo "disabled";
                                                                                                                                                                            } ?>>
                </div>


                <div class="form-group">
                    <label>Product Description:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter description of your product"></i>


                    <textarea name="editor1" id="editor1" rows="10" cols="80" required <?php if ($_SESSION['type_app'] == 'storeadmin') {
                                                                                            echo "disabled";
                                                                                        } ?>>
									<?php echo $resultofedit['description']; ?>
								</textarea>
                    <script src="ckeditor/ckeditor.js"></script>
                    <script>
                        // Replace the <textarea id="editor1"> with a CKEditor
                        // instance, using default configuration.

                        CKEDITOR.replace('editor1');
                    </script>
                </div>
                <div class="form-group">
                    <label>Product Size(Optional):</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Seperated by commas. For example: S,M,L,XL,XXL"></i>
                    <input type="text" class="form-control" placeholder="Enter size of product" name="size" value="<?php echo $resultofedit['size']; ?>" <?php if ($_SESSION['type_app'] == 'storeadmin') {
                                                                                                                                                                echo "disabled";
                                                                                                                                                            } ?>>
                </div>

                <div class="form-group">
                    <label>Product Color (Optional) :</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Seperated by commas. For example: Red,Green,Blue"></i>
                    <input type="text" class="form-control" placeholder="Enter colour of product" name="colour" value="<?php echo $resultofedit['color']; ?>" <?php if ($_SESSION['type_app'] == 'storeadmin') {
                                                                                                                                                                    echo "disabled";
                                                                                                                                                                } ?>>
                </div>

                <label>Price Range(Optional):</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Please make sure your price range is in between 1000 - 1500"></i>
                <div class="input-group">
                    <span class="input-group-addon"><?php echo $currency; ?></span>
                    <input type="text" step="0.01" min="0" max="10" class="form-control" id="price_rang" placeholder="Enter price of product" name="price_rang" value="">

                </div>


                <label>Regular Price(Optional):</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="MRP of your product. Don't use comma. For example: 1000"></i>
                <div class="input-group">
                    <span class="input-group-addon"><?php echo $currency; ?></span>
                    <input type="text" step="0.01" min="0" max="10" class="form-control" id="mrp" placeholder="Enter price of product" name="price" value="<?php echo $resultofedit['price']; ?>" <?php /*?><?php if($_SESSION['type_app'] == 'storeadmin'){echo "disabled"; } ?><?php */ ?>>

                </div>

                <div class="form-group">
                    <label>Selling Price:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Selling price of your product. Must be less than MRP. Don't use comma. For example: 900"></i>
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo $currency; ?></span>

                        <?php
                        $sellprice_New = $resultofedit["sellprice"];
                        ?>

                        <input type="text" class="form-control" step="0.01" min="0" max="10" id="sp" placeholder="Enter price of product" name="sellprice" value="<?php echo $sellprice_New; ?>" onfocusout="myFunction()" required>

                    </div>

                    <div class="form-group">
                        <label>Special Value:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Special value of your product. Must be less than MRP. Don't use comma. For example: 900"></i>
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo $currency; ?></span>
                            <input type="number" placeholder="1.0" step="0.01" min="0" max="1000000000" class="form-control" name="Special_Value" value="<?php echo $resultofedit['Special_Value']; ?>">
                        </div>
                    </div>



                    <span class="input-group-add"></span>


                    <div class="form-group">
                        <label for="sel1">Product Status:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Choose status of your product."></i>
                        <select class="form-control" id="sel1" name="product_status" required <?php if ($_SESSION['type_app'] == 'storeadmin') {
                                                                                                    echo "disabled";
                                                                                                } ?>>
                            <option value="In-stock">Available</option>
                            <option value="coming-soon">Not Available</option>
                            <option value="low-stock">Low-Stock</option>

                        </select>
                    </div>
                    <div class="form-group">
                        <label>Product Quantity:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Stock quantity of your product. For example: 10"></i>
                        <!--<input type="text" class="form-control" placeholder="Enter quantity of product" name="quantity"  value="<?php echo $resultofedit['quantity']; ?>" required <?php if ($_SESSION['type_app'] == 'storeadmin') {
                                                                                                                                                                                            echo "disabled";
                                                                                                                                                                                        } ?>>-->
                        <input type="text" class="form-control" placeholder="Enter quantity of product" name="quantity" value="<?php echo $resultofedit['quantity']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Product Quantity Limit Per Order:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="How many quantity customer can order at one time. Keep it between 1 to 5 to avoid spam bulk orders."></i>
                        <input type="text" class="form-control" placeholder="Enter quantity of product" name="plimit" value="<?php echo $resultofedit['plimit']; ?>" required <?php if ($_SESSION['type_app'] == 'storeadmin') {
                                                                                                                                                                                    echo "disabled";
                                                                                                                                                                                } ?>>
                    </div>

                    <div class="form-group">
                        <label>Current Images:</label><br>
                        <?php foreach ($getproductimages as $files) {
                            $id = $files['id'];
                            $image = $files['image'];
                            echo '<div class="show-image"><img src="' . $image . '" width="100" height="200">';
                            echo '<input type="hidden" value="' . $image . '" name="delete_file" />';
                            echo '<input type="button" value="Delete image" onclick="delete_post(' . $id . ');" class="btn btn-sm btn-danger" /></div>';
                        }   ?>
                        <script>
                            function delete_post(id) {
                                m = confirm("Are you sure you want to delete this product image?");
                                if (m == true) {
                                    $.post('productimagedelete.php', {
                                            post_id: id
                                        }, // Set your ajax file path
                                        function(data) {
                                            $('#yourDataContainer').html(data); // You can Use .load too
                                            alert('Deleted');
                                            location.reload();
                                        });
                                } else {
                                    return false;
                                }
                            }
                        </script>
                    </div>
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>


                    <?php if ($imagecounter < $imagehandel) { ?><br>
                        <br><br><br><br><br><br><br><br>
                        <div class="form-group"><br>

                            <br><br><br>

                            <label for="exampleInputFile">Product Images</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Add at least one image of your product."></i>
                            <br>
                            <div class="input-files1"><a class="fa fa-plus fa-4 btn btn-primary" aria-hidden="true" id="moreImg">Add More Image</a></div>

                            <br>
                            <div class="input-files">

                                <input type="file" name="image_upload-1" <?php if ($_SESSION['type_app'] == 'storeadmin') {
                                                                                echo "disabled";
                                                                            } ?>>

                            </div>
                            <p class="help-block">Please upload GIf,JPG,Jpeg,BMP,PNG files only.</p>

                        <?php } else {
                        echo '<br><br><br><br><br><br><br><br><br><br><br><br><div class="form-group">
 			 <p class="help-block">You have used your image limit(' . $imagehandel . ' images for single product please delete one and try )</p><div>';
                    } ?>

                        <div class="form-group text-muted well well-sm no-shadow">
                            <p class="help-block" for="terms" id="terms">
                            <div id="cont"></div>Please check Atleast One Category.</p>
                            <lable><b>Categories</b></lable><br>
                            <?php

                            //if($_SESSION['type_app']  == 'ADMIN'){
                            $sql1 = "SELECT * FROM `category` ";
                            $check1 = mysqli_query($conn, $sql1);
                            $resultcheck1 = mysqli_fetch_array($check1, MYSQLI_BOTH);
                            //}

                            foreach ($check1 as $row) {   ?>
                                <div class="checkbox-inline">
                                    <span style="margin-left:10px"></span><input name="chk" value="<?php echo $row['cat_id']; ?>" type="radio" id="mycheckbox" <?php echo ($row['cat_id'] == $resultofedit['Category_Id']) ? 'checked' : '' ?> <?php if ($_SESSION['type_app'] == 'storeadmin') {
                                                                                                                                                                                                                                                    echo "disabled";
                                                                                                                                                                                                                                                } ?> onchange="subcat(this.value)"><span><?php echo $row['category_name']; ?> </span><span style="margin-left:10px"></span>
                                </div>

                            <?php } ?>
                            <br>

                        </div>

                        <?php
                        $subscat = "";
                        $selectSQL = "SELECT `Sub_Category_Id` AS `sub_cat_id`, `Sub_Category_Name` AS `sub_cat_name`, `cat_id`
					 FROM `subcategories` WHERE cat_id=" . $resultofedit['Category_Id'] . " ORDER BY `sub_cat_name` ASC";
                        $result = mysqli_query($conn, $selectSQL);
                        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                            if ($row['sub_cat_id'] == $resultofedit['Category_Type']) {
                                $subscat .= '<option value="' . $row['sub_cat_id'] . '" selected="selected">' . $row['sub_cat_name'] . '</option>';
                            } else {
                                $subscat .= '<option value="' . $row['sub_cat_id'] . '">' . $row['sub_cat_name'] . '</option>';
                            }
                        }
                        ?>

                        <div class="form-group">
                            <label for="sub-cat">Sub Categories:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Choose sub category."></i>
                            <select class="form-control" id="sub-cat" name="sub-category" required>
                                <option>Select Sub Category</option>
                                <?php
                                echo $subscat;
                                ?>
                            </select>
                        </div>
                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="Update product" name="addproduct" id="postme" title='Fill all the deatails completely'>

                        </div>
            </form>
        </div>
        <!-- /.box-body -->
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            var id = 1;
            var high = "<?php echo $imagehandel; ?>";
            var counter = "<?php echo $imagecounter; ?>";
            var total = high - counter;
            //alert(high);
            $("#moreImg").click(function() {
                var showId = ++id;
                //alert(counter);
                if (showId <= total) {
                    $(".input-files").append('<br><input type="file" name="image_upload-' + showId + '">');
                }
            });
        });
    </script>
    <script>
        function myFunction() {
            //alert("Input field lost focus.");



            var mrp = $('#mrp').val();
            var sp = $('#sp').val();
            //alert('mrp is:'+mrp);
            //alert('selleing price:'+sp);
            if (sp >= mrp) {

                //alert('oye teri');
                swal({
                    title: "Please Review",
                    text: "selling price must be less then Regular price",
                    icon: "warning",

                });
            }

        }
    </script>
    <script>
        function subcat(id) {
            //alert(id);
            var cat = $('#mycheckbox').val();
            // alert(cat);
            if (id) {
                $.ajax({
                    type: 'POST',
                    url: 'findState.php',
                    data: 'cat_id=' + id,
                    success: function(html) {
                        //alert(html);
                        $('#sub-cat').html(html);
                        //$('#city').html('<option value="">Select state first</option>');
                    }
                });
            } else {
                $('#sub-cat').html('<option value="">Select Category first</option>');

            }

        }
    </script>
    <?php include 'scriptfooter.php'; ?>
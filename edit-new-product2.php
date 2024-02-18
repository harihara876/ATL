<?php

/**
 * Fetches new product info by ID.
 * @param object DB connection object.
 * @param int $newProductID New product ID.
 * @return array New product Info.
 */
function getNewProductInfoByID($conn, $newProductID)
{
    $selectSQL = "SELECT * FROM `products_temp2` WHERE `id` = {$newProductID} LIMIT 1";
    $result = mysqli_query($conn, $selectSQL);
    return mysqli_fetch_array($result, MYSQLI_ASSOC);
}

/**
 * Fetches categories.
 * @param object DB connection object.
 * @return array Categories.
 */
function getCategories($conn)
{
    $categories = [];
    $selectSQL = "SELECT * FROM `category`";
    $result = mysqli_query($conn, $selectSQL);

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $categories[] = $row;
    }

    return $categories;
}

/**
 * Returns sub categories list.
 * @param object DB connection object.
 * @return array Sub categories.
 */
function getSubCategories($conn)
{
    $subCategories = [];
    $selectSQL = "SELECT
            `Sub_Category_Id` AS `sub_cat_id`,
            `Sub_Category_Name` AS `sub_cat_name`,
            `cat_id`
        FROM `subcategories`
        ORDER BY `sub_cat_name` ASC";
    $result = mysqli_query($conn, $selectSQL);

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $subCategories[] = $row;
    }

    return $subCategories;
}

/**
 * Fetches currency.
 * @param object DB connection object.
 * @return string Currency.
 */
function getCurrency($conn)
{
    $selectSQL = "SELECT `currency` FROM `admin`";
    $result = mysqli_query($conn, $selectSQL);
    $currency = mysqli_fetch_array($result, MYSQLI_ASSOC)["currency"];

    if ($currency == "USD") {
        $currency = "$";
    } else {
        $currency = "₹";
    }

    return $currency;
}

require_once("header.php");
require_once("sidebar.php");
require_once("widget.php");
?>

<style>
    .form-container {
        margin-left:5px;
        margin-top:10px;
    }
</style>

<?php
// Get new product ID from query string.
$newProductID = ($_GET["id"]) ? $_GET["id"] : NULL;
if (!$newProductID) {
    echo "<script>
            alert('Access not allowed.');
            window.location.assign('new-products.php');
        </script>";
} else {
    $newProductInfo = getNewProductInfoByID($conn, $newProductID);
    if (!$newProductInfo) {
        echo "<script>
            alert('Product not found.');
            window.location.assign('new-products.php');
        </script>";
    }
}

// $currency = getCurrency($conn);
$currency = "$";
?>

<form role="form" method="POST" enctype="multipart/form-data" action="update-new-product2.php">
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-user fa-4" aria-hidden="true"></i> Add New Product
            </h3>
        </div>
        <div class="box-body">
            <div class="form-container">
                <input type="hidden" name="product_id" value="<?php echo $newProductInfo['id']; ?>">
                <input type="hidden" name="temp_product_id" value="<?php echo $newProductInfo['product_id']; ?>">
                <input type="hidden" name="upc" value="<?php echo $newProductInfo['upc']; ?>">
                 <?php
					$editimageshandel = "SELECT image_handel FROM admin";
					$gethandel = mysqli_query($conn, $editimageshandel);
					$resulthandel = mysqli_fetch_array($gethandel, MYSQLI_ASSOC);
					$imagehandel = $resulthandel['image_handel'];
				?>
                <div class="form-group">
                    <label>Product Name:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter name of your product"></i>
                    <input type="text" class="form-control" placeholder="Enter name of your product" name="name" required>
                </div>
                <div class="form-group">
                    <label>Product Description:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter description of your product"></i>
                    <textarea name="editor1" id="editor1" rows="10" cols="80" required></textarea>
                    <script src="ckeditor/ckeditor.js"></script>
                    <script>
                        // Replace the <textarea id="editor1"> with a CKEditor
                        // instance, using default configuration.
                        CKEDITOR.replace( 'editor1' );
                    </script>
                </div>
                <div class="form-group">
                    <label >Product Size (Optional):</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Seperated by commas. For example: S,M,L,XL,XXL"></i>
                    <input type="text" class="form-control" placeholder="Enter size of your product like S,M,L,Xl" name="size" >
                </div>
                <div class="form-group">
                    <label>Product Color (Optional):</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Seperated by commas. For example: Red,Green,Blue"></i>
                    <input type="text" class="form-control" placeholder="Enter colour of your product like Red,Green,Blue" name="colour">
                </div>
                <div class="form-group">
                    <label>Regular Price (Optional):</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="MRP of your product. Don't use comma. For example: 1000"></i>
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo $currency;?></span>
                        <input type="text" placeholder="eg. 1.0" class="form-control" name="price" id="mrp" value="<?php echo $newProductInfo['regular_price']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Selling Price:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Selling price of your product. Must be less than MRP. Don't use comma. For example: 900"></i>
                    <div class="input-group">
                        <span class="input-group-addon"><?php echo $currency;?></span>
                        <input type="text" placeholder="eg. 1.0" class="form-control" name="sellprice" onfocusout="myFunction()" required id="sp" value="<?php echo $newProductInfo['selling_price']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="sel1">Product Status:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Choose status of your product."></i>
                    <select class="form-control" id="sel1" name="product_status" required>
                        <option value="In-stock">Available</option>
                        <option value="coming-soon">Not Available</option>
                        <option value="low-stock">Low Stock</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Product Quantity:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Stock quantity of your product. For example: 10"></i>
                    <input type="number"  class="form-control" placeholder="Enter stock quantity of your product" name="quantity" required id="stock">
                </div>
                <div class="form-group">
                    <label>Product Quantity Limit Per Order:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="How many quantity customer can order at one time. Keep it between 1 to 5 to avoid spam bulk orders."></i>
                    <input type="number" class="form-control" placeholder="No. of quantity customer can order at one time" name="plimit" onfocusout="myFunction1()" id="limit" required >
                </div>
                <div class="form-group">
                    <label>UPC:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Product UPC"></i>
                    <input type="text" class="form-control" value='<?php echo $newProductInfo["upc"]; ?>' name="product_upc">
                </div>
                <div class="form-group">
                    <label>SKU:</label> <i class="fa fa-question-circle" data-toggle="tooltip"
                        data-placement="top" title="Stock Keeping Unit"></i>
                    <input type="text" class="form-control" placeholder="Enter SKU" name="product-sku" required>
                </div>
                <div class="form-group">
                    <label>Manufacturer:</label> <i class="fa fa-question-circle" data-toggle="tooltip"
                        data-placement="top" title="Manufacturer name"></i>
                    <input type="text" class="form-control" placeholder="Manufacturer name" name="manufacturer" required>
                </div>
                <div class="form-group">
                    <label>Brand:</label> <i class="fa fa-question-circle" data-toggle="tooltip"
                        data-placement="top" title="Brand name"></i>
                    <input type="text" class="form-control" placeholder="Brand name" name="brand" required>
                </div>
                <div class="form-group">
                    <label>Vendor:</label> <i class="fa fa-question-circle" data-toggle="tooltip"
                        data-placement="top" title="Vendor name"></i>
                    <input type="text" class="form-control" placeholder="Vendor name" name="vendor" required>
                </div>
                <div class="form-group">
                    <label>Age Restriction:</label> <i class="fa fa-question-circle" data-toggle="tooltip"
                        data-placement="top" title="Age Restriction"></i>
                    <input type="text" class="form-control" placeholder="Age Restriction" name="age-restriction" required>
                </div>
                <div class="form-group">
                    <label>Tax Status:</label> <i class="fa fa-question-circle" data-toggle="tooltip"
                        data-placement="top" title="Tax Status"></i>
                    <input type="text" class="form-control" placeholder="Tax Status" name="tax-status" required>
                </div>
                <div class="form-group">
                    <label>Tax Value:</label> <i class="fa fa-question-circle" data-toggle="tooltip"
                        data-placement="top" title="Tax Value"></i>
                    <input type="text" class="form-control" placeholder="Tax Value" name="tax-value" required>
                </div>
                <div class="form-group">
                            <label for="exampleInputFile">Product Images</label> <i class="fa fa-question-circle"
                                data-toggle="tooltip" data-placement="top"
                                title="Add at least one image of your product."></i>
                            <br>
                            <div class="input-files1">
                                <a class="fa fa-plus fa-4 btn btn-primary" aria-hidden="true" id="moreImg">Add More
                                    Image</a>
                            </div>
                            <br>
                            <div class="input-files">
                                <input type="file" name="image_upload-1" required>
                            </div>
                            <p class="help-block">Please upload GIf, JPG, Jpeg, BMP, PNG files only.</p>
                        </div>
                <div class="form-group text-muted well well-sm no-shadow">
                            <p class="help-block">Please check at least one category.</p>
                            <label><b>Categories</b></label><br>
                            <?php
                                $sql1 = "SELECT * FROM `category` ";
                                $check1 = mysqli_query($conn, $sql1);
                                $resultcheck1 = mysqli_fetch_array($check1, MYSQLI_BOTH);

                                foreach ($check1 as $row) {
                            ?>
                            <div class="checkbox-inline">
                                <span style="margin-left:10px"></span><input name="chk"
                                    value="<?php echo $row['cat_id']; ?>" type="radio" onchange="subcat(this.value)"
                                    id="mycheckbox<?php echo ($row['cat_id']);?>"><span><?php echo $row['category_name']; ?> </span><span
                                    style="margin-left:10px"></span>
                            </div>
                            <?php } ?>
                            <br>
                        </div>
                        <div class="form-group">
                            <label for="sub-cat">Sub Categories:</label> <i class="fa fa-question-circle"
                                data-toggle="tooltip" data-placement="top" title="Choose sub category."></i>
                            <select class="form-control" id="sub-cat" name="sub-category" required>
                                <option>Select Sub Category</option>
                            </select>
                        </div>
                <!-- <div class="form-group text-muted well well-sm no-shadow">
                    <p class="help-block">Please check at least one category.</p>
                    <label><b>Categories</b></label><br>
                    <?php
                        $categories = getCategories($conn);

                        foreach ($categories as $category) {
                            echo "<div class='checkbox-inline'>
                                    <span style='margin-left:10px'></span><input name='chk' value='{$category["cat_id"]}' type='radio' id='mycheckbox'><span>{$category["category_name"]}</span><span style='margin-left:10px'></span>
                                </div>";
                        }
                    ?>
                    <br>
                </div>
                <div class="form-group">
                    <label for="sub-cat">Sub Categories:</label> <i class="fa fa-question-circle"
                        data-toggle="tooltip" data-placement="top" title="Choose sub category."></i>
                    <select class="form-control" id="sub-cat" name="sub-category" required>
                        <?php
                            $subCategories = getSubCategories($conn);
                            if ($subCategories) {
                                foreach ($subCategories as $subCat) {
                                    echo "<option value='{$subCat["sub_cat_id"]}'>{$subCat["sub_cat_name"]}</option>";
                                }
                            } else {
                                echo "<option disabled>No records found</option>";
                            }
                        ?>
                    </select>
                </div> -->
            </div>
        </div>
        <div class="box-footer">
            <input type="submit" class="btn btn-primary" value="Add product" name="addproduct" id="postme" disabled title='Fill all the deatails completely'>
        </div>
    </div>
</form>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
    var checkboxes = $("input[type='radio']");
    var submitButt = $("input[type='submit']");

    checkboxes.click(function () {
        submitButt.attr("disabled", !checkboxes.is(":checked"));
    });
    $("#sel1").change(function () {
        var disabled = (this.value == "not" || this.value == "default");
        console.log(disabled);
        $("#product_text").prop("disabled", disabled);
    }).change(); //to trigger on load

    function myFunction() {
        var mrp = $('#mrp').val();
        var sp = $('#sp').val();
        if (sp >= mrp) {
            swal({
                title: "Please Review",
                text: "Selling price must be less than Regular price",
                icon: "warning"
            });
        }
    }

  function subcat(id){
        //alert(id);
        var cat =  $('#mycheckbox').val();
       // alert(cat);
         if(id){
            $.ajax({
                type:'POST',
                url:'findState.php',
                data:'cat_id='+id,
                success:function(html){
                    //alert(html);
                    $('#sub-cat').html(html);
                    //$('#city').html('<option value="">Select state first</option>'); 
                }
            }); 
        }else{
            $('#sub-cat').html('<option value="">Select Category first</option>');

        }

        }



    function myFunction1() {
        var stock = $('#stock').val();
        var limit = $('#limit').val();
        if(limit >= stock ) {
            swal({
                title: "Please Review",
                text: "Quantity per order limit must be less than availability in your stock. Keep it between 1 - 5 to avoid spam bulk orders.",
                icon: "warning"
            });
        }
    }
</script>
<script>
    $(document).ready(function () {
        var id = 1;
        var high = "<?php echo $imagehandel; ?>";
        $("#moreImg").click(function () {
            var showId = ++id;
            if (showId <= high) {
                $(".input-files").append('<br><input type="file" name="image_upload-' + showId + '">');
            }
        });
    });
</script>
<?php require_once("scriptfooter.php"); ?>
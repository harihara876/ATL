<?php

// Currency.
$currency = "$";

/**
 * Returns sub categories list.
 * @param object DB connection object.
 * @return array Sub categories.
 */
// function getSubCategories($conn)
// {
//     $subCategories = [];
//     $selectSQL = "SELECT
//             `Sub_Category_Id` AS `sub_cat_id`,
//             `Sub_Category_Name` AS `sub_cat_name`,
//             `cat_id`
//         FROM `subcategories`
//         ORDER BY `sub_cat_name` ASC";
//     $result = mysqli_query($conn, $selectSQL);

//     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
//         $subCategories[] = $row;
//     }

//     return $subCategories;
// }

require_once("header.php");
require_once("sidebar.php");
require_once("widget.php");
?>

<style>
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

    .my-error-class {
        color: red;
    }

    .my-valid-class {
        color: green;
    }

    #sub-cat {
        max-height: 300px;
    }
</style>

<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title"></h4>
    </div>
     <?php
if($_SESSION['type_app']  == 'storeadmin'){
    ?>
    <div class="modal-body">

    <button class="btn btn-primary " class="pull-left" data-toggle="modal" data-target="#myModal">Add UPC</button>

    </div>
    <?php }else{ ?>
    <div class="modal-body">

        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-shopping-cart fa-4" aria-hidden="true"></i> Add Product
                </h3>
            </div>
            <form role="form" method="POST" enctype="multipart/form-data" action="product-process.php">
                <div class="box-body">
                    <div class="form-container">
                        <?php
                            $editimageshandel = "SELECT image_handel FROM admin";
                            $gethandel = mysqli_query($conn, $editimageshandel);
                            $resulthandel = mysqli_fetch_array($gethandel, MYSQLI_ASSOC);
                            $imagehandel = $resulthandel['image_handel'];
                        ?>
                        <div class="form-group">
                            <label>Product Name:</label> <i class="fa fa-question-circle" data-toggle="tooltip"
                                data-placement="top" title="Enter name of your product"></i>
                            <input type="text" class="form-control" placeholder="Enter name of your product" name="name"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Product Description:</label> <i class="fa fa-question-circle" data-toggle="tooltip"
                                data-placement="top" title="Enter description of your product"></i>
                            <textarea name="editor1" id="editor1" rows="10" cols="80" required></textarea>
                            <script src="ckeditor/ckeditor.js"></script>
                            <script>
                                // Replace the <textarea id="editor1"> with a CKEditor
                                // instance, using default configuration.
                                CKEDITOR.replace('editor1');
                            </script>
                        </div>
                        <div class="form-group">
                            <label>Product Size(Optional):</label> <i class="fa fa-question-circle"
                                data-toggle="tooltip" data-placement="top"
                                title="Seperated by commas. For example: S,M,L,XL,XXL"></i>
                            <input type="text" class="form-control"
                                placeholder="Enter size of your product like S,M,L,Xl" name="size">
                        </div>
                        <div class="form-group">
                            <label>Product Color (Optional) :</label> <i class="fa fa-question-circle"
                                data-toggle="tooltip" data-placement="top"
                                title="Seperated by commas. For example: Red,Green,Blue"></i>
                            <input type="text" class="form-control"
                                placeholder="Enter colour of your product like Red,Green,Blue" name="colour">
                        </div>

                        <div class="form-group">
                            <label>Price Range(Optional):</label> <i class="fa fa-question-circle"
                                data-toggle="tooltip" data-placement="top"
                                title="Please make sure your price range is in between 1000 - 1500"></i>
                            <div class="input-group">
                                <span class="input-group-addon"><?php echo $currency; ?></span>
                                <input type="number" placeholder="1.0" step="0.01" min="0" max="10000000"
                                    class="form-control" name="price_rang" id="price_rang">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Regular Price(Optional):</label> <i class="fa fa-question-circle"
                                data-toggle="tooltip" data-placement="top"
                                title="MRP of your product. Don't use comma. For example: 1000"></i>
                            <div class="input-group">
                                <span class="input-group-addon"><?php echo $currency; ?></span>
                                <input type="number" placeholder="1.0" step="0.01" min="0" max="10000000"
                                    class="form-control" name="price" id="mrp">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Selling Price:</label> <i class="fa fa-question-circle" data-toggle="tooltip"
                                data-placement="top"
                                title="Selling price of your product. Must be less than MRP. Don't use comma. For example: 900"></i>
                            <div class="input-group">
                                <span class="input-group-addon"><?php echo $currency; ?></span>
                                <input type="number" placeholder="1.0" step="0.01" min="0" max="1000000000"
                                    class="form-control" name="sellprice" onfocusout="myFunction()" required id="sp">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Special Value:</label> <i class="fa fa-question-circle" data-toggle="tooltip"
                                data-placement="top"
                                title="Special value of your product. Must be less than MRP. Don't use comma. For example: 900"></i>
                            <div class="input-group">
                                <span class="input-group-addon"><?php echo $currency; ?></span>
                                <input type="number" placeholder="1.0" step="0.01" min="0" max="1000000000" class="form-control" name="Special_Value">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="sel1">Product Status:</label> <i class="fa fa-question-circle"
                                data-toggle="tooltip" data-placement="top" title="Choose status of your product."></i>
                            <select class="form-control" id="sel1" name="product_status" required>
                                <option value="In-stock">Available</option>
                                <option value="coming-soon">Not Available</option>
                                <option value="low-stock">Low Stock</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Product Quantity:</label> <i class="fa fa-question-circle" data-toggle="tooltip"
                                data-placement="top" title="Stock quantity of your product. For example: 10"></i>
                            <input type="number" class="form-control" placeholder="Enter stock quantity of your product"
                                name="quantity" required id="stock">
                        </div>
                        <div class="form-group">
                            <label>Product Quantity Limit Per Order:</label> <i class="fa fa-question-circle"
                                data-toggle="tooltip" data-placement="top"
                                title="How many quantity customer can order at one time. Keep it between 1 to 5 to avoid spam bulk orders."></i>
                            <input type="number" class="form-control"
                                placeholder="No. of quantity customer can order at one time" name="plimit"
                                onfocusout="myFunction1()" id="limit" required>
                        </div>
                        <div class="form-group">
                            <label>UPC:</label> <i class="fa fa-question-circle" data-toggle="tooltip"
                                data-placement="top" title="Universal Product Code"></i>
                            <input type="text" class="form-control" placeholder="Enter UPC" name="product-upc" required>
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
                            //if($_SESSION['type_app']  == 'ADMIN'){

                                $sql1 = "SELECT * FROM `category` ";
                                $check1 = mysqli_query($conn, $sql1);
                                $resultcheck1 = mysqli_fetch_array($check1, MYSQLI_BOTH);
                            /*}
                            else{
                                $sql1 = "SELECT * FROM `appuser_category` where `storeadmin_id`='{$_SESSION['id']}' and  `type_app_admin`='{$_SESSION['type_app']}' ";
                                $check1 = mysqli_query($conn, $sql1);
                                $resultcheck1 = mysqli_fetch_array($check1, MYSQLI_BOTH);
                            }*/


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
                                <?php
                                    // $subCategories = getSubCategories($conn);
                                    // if ($subCategories) {
                                    //     foreach ($subCategories as $subCat) {
                                    //         echo "<option value='{$subCat["sub_cat_id"]}'>{$subCat["sub_cat_name"]}</option>";
                                    //     }
                                    // } else {
                                    //     echo "<option disabled>No records found</option>";
                                    // }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <input type="submit" class="btn btn-primary" value="Add product" name="addproduct" id="postme"
                            disabled title='Fill all the deatails completely'>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php } ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>
    var checkboxes = $("input[type='radio']"),
        submitButt = $("input[type='submit']");

    checkboxes.click(function () {
        submitButt.attr("disabled", !checkboxes.is(":checked"));
    });
    $("#sel1").change(function () {
        var disabled = (this.value == "not" || this.value == "default");
        console.log(disabled);
        $("#product_text").prop("disabled", disabled);
    }).change(); //to trigger on load
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
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
<script>
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
                text: "Selling price must be less than Regular price",
                icon: "warning",
            });
        }
    }

    function myFunction1() {
        //alert("Input field lost focus.");
        var stock = $('#stock').val();
        var limit = $('#limit').val();
        //alert('stock is:'+stock);
        //alert('limit:'+limit);
        if (limit >= stock) {
            swal({
                title: "Please Review",
                text: "Quantity per order limit must be less than availability in your stock. Keep it between 1 - 5 to avoid spam bulk orders.",
                icon: "warning",
            });
        }
    }
</script>

<?php
require_once("scriptfooter.php");
?>

      <!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add New UPC</h4>
      </div>
      <div class="modal-body">

          <form role="form" action="add-UPC.php" method="POST" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label for="exampleInputEmail1">UPC Name</label>
                  <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Enter UPC Name" name="catname" required="required">
                </div>


              </div>

          <input type="submit" class="btn btn-success" value="Add UPC" name="addcat">
      </form>
</div>
      </div>
    </div>

  </div>

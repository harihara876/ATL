<?php
require_once("header.php");
require_once("sidebar.php");
require_once("widget.php");
?>

<!-- Modal content-->
<div class="modal-content ">
    <div class="modal-header">
        <h4 class="modal-title">
            <i class="fa fa-shopping-cart fa-2" aria-hidden="true"></i> Products Details
        </h4>
    </div>
    <div class="modal-body">
        <br>
        <?php
        $id = $_GET['id'];
        $id = base64_decode($id);

        if (empty($id)) {
            echo '<script> alert("unauthrosize access not allowed");
                    window.location.assign("dashboard.php")
                    </script>';
        } else {

            if ($_SESSION['type_app']  == 'ADMIN') {
                $sql = "SELECT * FROM `products` p LEFT JOIN product_details pd ON pd.product_id = p.product_id where p.id = {$id}";
                $check = mysqli_query($conn, $sql);
            } else {
                $sql = "SELECT * FROM `products` p LEFT JOIN product_details pd ON pd.product_id = p.product_id where `storeadmin_id`='{$_SESSION['id']}' and p.id = {$id} ";
                $check = mysqli_query($conn, $sql);
            }

            $resultcheck = mysqli_fetch_array($check, MYSQLI_BOTH);
            $image1 = $resultcheck['Image'];
            $image2 = $resultcheck['Image'];
            $image3 = $resultcheck['Image'];
            $image4 = $resultcheck['Image'];
            $image5 = $resultcheck['Image'];
            $_SESSION['product_id'] = $resultcheck['product_id'];

            $sql = "SELECT * FROM `product_images` where product_id = '" . $_SESSION['product_id'] . "' ";
            $checkimage = mysqli_query($conn, $sql);
            $resultcheckimage = mysqli_fetch_array($checkimage, MYSQLI_BOTH);
        ?>

            <!-- Main content -->
            <section class="invoice">
                <!-- title row -->
                <div class="row">
                    <div class="col-xs-12">
                        <h2 class="page-header">
                            <i class="fa fa-eercast" aria-hidden="true"></i>Details
                            <small class="pull-right"><?php echo $resultcheck['Date_Created']; ?></small>
                        </h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-10">
                        <p class="lead"><?php echo $resultcheck['product_name']; ?></p>
                        <div class="table">
                            <table class="table">
                                <tr>
                                    <th>UPC:</th>
                                    <td><?php echo $resultcheck['UPC']; ?></td>
                                </tr>
                                <tr>
                                    <th>Manufacturer:</th>
                                    <td><?php echo $resultcheck['Manufacturer']; ?></td>
                                </tr>
                                <tr>
                                    <th>Brand:</th>
                                    <td><?php echo $resultcheck['Brand']; ?></td>
                                </tr>
                                <tr>
                                    <th>Vendor:</th>
                                    <td><?php echo $resultcheck['Vendor']; ?></td>
                                </tr>
                                <tr>
                                    <th>Regular Price:</th>
                                    <td><?php echo $resultcheck['price']; ?></td>
                                </tr>
                                <tr>
                                    <th>Selling Price:</th>
                                    <?php echo '<td> ' . $resultcheck["sellprice"] . '</td>'; ?>
                                </tr>
                                <tr>
                                    <th>Colour:</th>
                                    <td><?php echo $resultcheck['color']; ?></td>
                                </tr>
                                <tr>
                                    <th>Size:</th>
                                    <td><?php echo $resultcheck['size']; ?></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td><?php if ($resultcheck['product_status'] == 1) {
                                            echo 'Available';
                                        } elseif ($resultcheck['product_status'] == 0) {
                                            echo 'Not avialable';
                                        } elseif ($resultcheck['product_status'] == 2) {
                                            echo 'Low stock';
                                        } ?></td>
                                </tr>
                                <tr>
                                    <th>Images:</th>
                                    <td><img src="<?php echo $resultcheckimage['image']; ?>" height="200" width="100"></td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>
                                        <p>
                                            <?php
                                            $product_des = $resultcheck['description'];
                                            $resultcheck['description'] = htmlspecialchars_decode(str_replace("&quot;", "\"", $product_des));
                                            echo htmlspecialchars_decode($product_des);
                                            ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Category</th>
                                    <td>
                                        <?php

                                        $sql1 = "SELECT * FROM `category` ";
                                        $check11 = mysqli_query($conn, $sql1);
                                        $resultcheck1 = mysqli_fetch_array($check11, MYSQLI_BOTH);

                                        foreach ($check11 as $row) {
                                        ?>
                                            <div class="checkbox-inline">
                                                <?php
                                                foreach ($check11 as $row1) {
                                                    if ($row['cat_id'] == $row1['cat_id']) {
                                                        echo $row['category_name'];
                                                    } else {
                                                    }
                                                }
                                                ?>
                                            </div>
                                        <?php } ?>
                                        <br>
                                    </td>
                                </tr>
                                <tr>
                                    <th> Gallery</th>
                                </tr>
                            </table>
                            <table class="table ">
                                <tr>
                                    <?php
                                    foreach ($checkimage as $image) {
                                    ?>
                                        <td>
                                            <li data-toggle="modal" data-target="#myModal">
                                                <a href="#myGallery" data-slide-to="0">
                                                    <img src="<?php echo $image['image']; ?>" height="200" width="100">
                                                </a>
                                            </li>
                                        </td>
                                    <?php } ?>
                                </tr>
                            </table>
                            <!--begin modal window-->
                            <div class="modal fade" id="myModal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div class="pull-left"><?php echo $resultcheck['product_name']; ?></div>
                                            <button type="button" class="close" data-dismiss="modal" title="Close"> <span class="glyphicon glyphicon-remove"></span></button>
                                        </div>
                                        <div class="modal-body">





                                            <!--begin carousel-->
                                            <div id="myGallery" class="carousel slide" data-interval="false">
                                                <div class="carousel-inner">
                                                    <div class="item active">
                                                        <center> <img class="img-responsive" src="<?php echo $image['image']; ?>" alt="item0" height="200" width="100">
                                                    </div>

                                                    <?php
                                                    foreach ($checkimage as $image) {
                                                    ?>

                                                        <div class="item">
                                                            <center><img src="<?php echo $image['image']; ?>" alt="item1" height="200" width="100"></center>

                                                        </div>

                                                    <?php } ?>

                                                    <!--end carousel-inner-->
                                                </div>
                                                <!--Begin Previous and Next buttons-->
                                                <a class="left carousel-control" href="#myGallery" role="button" data-slide="prev"> <span class="glyphicon glyphicon-chevron-left"></span></a> <a class="right carousel-control" href="#myGallery" role="button" data-slide="next"> <span class="glyphicon glyphicon-chevron-right"></span></a>
                                                <!--end carousel-->
                                            </div>









                                            <!--end modal-body-->
                                        </div>
                                        <div class="modal-footer">
                                            <div class="pull-left">

                                            </div>
                                            <button class="btn-sm close" type="button" data-dismiss="modal">Close</button>
                                            <!--end modal-footer-->
                                        </div>
                                        <!--end modal-content-->
                                    </div>
                                    <!--end modal-dialoge-->
                                </div>
                                <!--end myModal-->>
                            </div>

                            <tr>
                                <td><br>

                                    <a href="editproductsdetails.php?id=<?php echo $_GET['id']; ?>"><button type="button" class="btn btn-primary btn-lg  btn-success"><i class="fa fa-pencil-square fa-6" aria-hidden="true"></i> EDIT</button></a>
                                </td>
                                <td>
                                    <br><a href="deleteproducts.php?id=<?php echo base64_encode($resultcheck['id']); ?>" onClick="return checkDelete()" class="btn btn-social-icon btn-google btn-lg"><i class="fa fa fa-trash-o"></i></a>
                                </td>
                            </tr>
                            </table>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->


            </section>
    </div>
    </section>
</div>
</div>
<?php include 'footer.php'; ?>

<?php } ?>
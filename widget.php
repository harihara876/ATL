<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Ecommerce Store Dashboard
            <small></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active"> <a href="<?php echo basename($_SERVER['PHP_SELF']); ?>"><?php echo str_replace(".php", "", basename($_SERVER['PHP_SELF'])); ?></a></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Info boxes -->
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <div class="info-box">
                    <a href="orders.php"><span class="info-box-icon bg-aqua"><i class="fa fa-gift fa-5"></i></span></a>

                    <div class="info-box-content">
                        <span class="info-box-text">Orders</span>
                        <span class="info-box-number" id="ordersCount2">Loading...</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <a href="category.php"><span class="info-box-icon bg-red"><i class="glyphicon glyphicon-folder-close big-icon"></i></span></a>

                    <div class="info-box-content">
                        <span class="info-box-text">Categories</span>
                        <span class="info-box-number" id="categoriesCount2">Loading...</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->

            <!-- fix for small devices only -->
            <div class="clearfix visible-sm-block"></div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <a href=" products.php"><span class="info-box-icon bg-green"><i class="ion ion-ios-cart-outline"></i></span></a>

                    <div class="info-box-content">
                        <span class="info-box-text">Products</span>
                        <span class="info-box-number" id="productsCount2">Loading...</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <a href="users.php"><span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span></a>

                    <div class="info-box-content">
                        <span class="info-box-text">All Users</span>
                        <span class="info-box-number" id="usersCount2">Loading...</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
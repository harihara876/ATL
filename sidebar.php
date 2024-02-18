<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="admin.png" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><?php echo ucfirst($_SESSION['name']); ?></p>
            </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>
            <li>
                <a href="dashboard.php">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-laptop"></i>
                    <span>Store Settings</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if($_SESSION['type_app']  == 'ADMIN'){  ?>
                    <li><a href="add-storeadmin.php"><i class="fa fa-user-plus"></i>Add Store Admin</a></li>
                    <li><a href="storeadmin.php"><i class="fa fa-users fa-4"></i>Manage Store Admin</a></li>
                    <!-- <li><a href="currency_settings.php"><i class="fa fa-money"></i>Store Details</a></li> -->
                    <li><a href="image_settings.php"><i class="fa fa-file-image-o"></i>Product Image</a></li>
                    <?php }else{ ?>
                    <li><a href="currency_settings.php"><i class="fa fa-money"></i>Store Details</a></li>
                    <li><a href="image_settings.php"><i class="fa fa-file-image-o"></i>Product Image</a></li>
                    <?php } ?>
                </ul>
            </li>
            <li><a href="slider.php"><i class="fa fa-image fa-4"></i> Banner</a></li>
            <li>
                <a href="category.php">
                    <i class="fa fa-th"></i> <span>Categories</span>
                    <span class="pull-right-container">
                        <span class="label label-primary pull-right" id="categoriesCount">...</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="subcategory.php">
                    <i class="fa fa-th"></i> <span>Sub Categories</span>
                    <span class="pull-right-container">
                        <span class="label label-primary pull-right" id="subcategoriesCount">...</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="subsubcategory.php">
                    <i class="fa fa-th"></i> <span>Sub Sub Categories</span>
                    <span class="pull-right-container">
                        <span class="label label-primary pull-right" id="subSubcategoriesCount">...</span>
                    </span>
                </a>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-archive"></i>
                    <span>Products</span>
                    <span class="pull-right-container">
                        <span class="label label-primary pull-right" id="productsCount">...</span>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="add-product.php"><i class="ion ion-ios-cart-outline"></i>Add Products</a></li>
                    <li><a href="products.php"><i class="fa fa-shopping-basket fa-4"></i>Manage Products</a></li>
                    <?php
                    if($_SESSION['type_app']  == 'ADMIN'){ ?>
                    <li>
                        <a href="new-products.php">
                            <i class="fa fa-shopping-basket"></i>
                            <span>New Products</span>
                            <span class="pull-right-container">
                                <span class="label label-primary pull-right" id="newProductsCount">...</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="extra-new-products.php">
                            <i class="fa fa-shopping-basket"></i>
                            <span>Extra Products</span>
                            <span class="pull-right-container">
                            </span>
                        </a>
                    </li>
                    <?php }?>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-users fa-4"></i>
                    <span>Users</span>
                    <span class="pull-right-container">
                        <span class="label label-primary pull-right" id="usersCount">...</span>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="add-user.php"><i class="fa fa-user-plus"></i>Add User</a></li>
                    <li><a href="users.php"><i class="fa fa-users fa-4"></i>Manage Users</a></li>
                </ul>
            </li>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-file"></i>
                    <span>Pages</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="faq.php"><i class="fa fa-question-circle fa-4"></i>FAQ</a></li>
                    <li><a href="policy.php"><i class="fa  fa-info fa-4"></i>Policies</a></li>
                </ul>
            </li>
            <li>
                <a href="orders.php">
                    <i class="fa  fa-truck"></i>
                    <span>Order List</span>
                    <span class="pull-right-container">
                        <span class="label label-primary pull-right" id="ordersCount">...</span>
                    </span>
                </a>
            </li>

            <?php if($_SESSION['type_app']){ ?>
            <li class="treeview">
                <a href="#">
                    <i class="fa fa-file"></i>
                    <span>Reports</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="order-summary.php">Summary</a></li>
                    <li><a href="order-reports.php">Orders</a></li>
                    <li><a href="product-reports.php">Products</a></li>
                    <?php if($_SESSION['type_app']=="ADMIN"){ ?>
                    <li><a href="user-reports.php">Users</a></li>
                    <?php } ?>
                </ul>
            </li>
            <?php } ?>

            <li>
                <a href="notification.php">
                    <i class="fa fa-bell"></i> <span>Push Notification</span>
                </a>
            </li>
            <li><a href="profile.php"><i class="fa  fa-gear fa-4"></i>Admin Settings</a></li>
            <li><a href="logs.php"><i class="fa  fa-gear fa-4"></i>Last Login List</a></li>
            <li>
                <a href="logout.php">
                    <i class="fa fa-edit"></i> <span>Logout</span>
                </a>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
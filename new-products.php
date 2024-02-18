<?php

/**
 * Returns new products list.
 * @param object DB connection object.
 * @return array Users.
 */
function getNewProducts($conn)
{
    $newProducts = [];
    $selectSQL = "SELECT
            `id`,
            `upc`,
            products_temp.`created_on`,
			admin.`name`
        FROM `products_temp` 
		LEFT JOIN `admin` ON admin.`admin_id`=products_temp.`storeadmin_id`
		WHERE `upc_status_request`='0'
        ORDER BY `upc` ASC";
    $result = mysqli_query($conn, $selectSQL);

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $newProducts[] = $row;
    }

    return $newProducts;
}

// Super Admin
function getAdminNewProducts($conn)
{
    $newProducts = [];
    $selectSQL = "SELECT
            `id`,
            `upc`,
            products_temp.`created_on`,
			admin.`name`
        FROM `products_temp` 
		LEFT JOIN `admin` ON admin.`admin_id`=products_temp.`storeadmin_id`
		WHERE `upc_status_request`='1'
        ORDER BY `upc` ASC";
    $result = mysqli_query($conn, $selectSQL);

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $newProducts[] = $row;
    }

    return $newProducts;
}

require_once("header.php");
require_once("sidebar.php");
require_once("widget.php");
?>

<!-- Modal content-->
<div class="modal-content ">
    <div class="modal-header">
        <h4 class="modal-title"> <i class="fa  fa-dropbox fa-5" aria-hidden="true"></i>
            New Products
        </h4>
    </div>
    <div class="modal-body">
    <?php 
if($_SESSION['type_app']  == 'storeadmin'){
    ?>
    <button class="btn btn-primary " class="pull-left" data-toggle="modal" data-target="#myModal">Add UPC</button>
<?php } ?>
        <script language="JavaScript" type="text/javascript">
            function checkDelete(){
                return confirm('Are you sure you want to delete?');
            }
        </script>
    </div>  
    <div class="modal-body">
        <script language="JavaScript" type="text/javascript">
            function checkUpdate(){
                return confirm('Are you sure you want to Send Request to Admin?');
            }
        </script>
    </div>  
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body">
                            <table id="employee_data" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>UPC</th>
                                        <th>Created By</th>
                                        <th>Created On</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    if($_SESSION['type_app']  == 'ADMIN'){
                                        $newProducts = getAdminNewProducts($conn);
                                    }
                                    else{

                                        $newProducts = getNewProducts($conn);
                                    }
                                    foreach ($newProducts as $newProduct) {
                                        echo "<tr>
                                                <td>{$newProduct["upc"]}</td>
												<td>{$newProduct["name"]}</td>
                                                <td>{$newProduct["created_on"]}</td>
                                                <td>";
                                                if($_SESSION['type_app']  == 'ADMIN'){
                                                    echo"
                                                    
                                                    <a href='edit-new-product.php?id={$newProduct["id"]}'>
                                                        <button class='btn btn-primary btn-success'>
                                                            <i class='fa fa-pencil' aria-hidden='true'></i>
                                                        </button>
                                                    </a>";
                                                    }
                                                    else{
                                                        echo"
                                                    <a href='sync-new-product.php?id={$newProduct["id"]}' class='btn btn-primary btn-warning' onClick='return checkUpdate()'>
                                                        <i class='fa fa fa-refresh'></i>
                                                    </a>";
                                                    }
                                                    echo"
                                                    <a href='delete-new-product.php?id={$newProduct["id"]}' class='btn btn-social-icon btn-google' onClick='return checkDelete()'>
                                                        <i class='fa fa fa-trash-o'></i>
                                                    </a>
                                                </td>
                                            </tr>";
                                    }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    
</div>
<script>
$(document).ready(function () {
    $('#employee_data').DataTable({
        "scrollX"       : true,
        "pagingType"    : "numbers",
        "processing"    : true,
        "searching"     : true,
        "ordering"      : true,
        "info"          : true,
        "autoWidth"     : false
    });
});
</script>
<?php require_once("scriptfooter.php"); ?>

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
        
          <form role="form" action="add-newUPC.php" method="POST" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label for="exampleInputEmail1">UPC Name</label>
                  <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Enter UPC Name" name="catname" required="required">
                </div>
              
                
              </div>
        
        
        
      
      <div class="modal-footer">
          <input type="submit" class="btn btn-success" value="Add UPC" name="addcat">
      </form>
</div>
      </div>
    </div>

  </div>
<?php include 'header.php'; ?>
  <?php include 'sidebar.php'; ?>
<?php include 'widget.php'; ?>
 
 
  <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <!-- Left col -->
        <section class="col-lg-17 connectedSortable">
          <!-- Custom tabs (Charts with tabs)-->
          <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs pull-right">
            <li class="pull-left header"><i class="fa fa-inbox"></i>Category</li>
           
            </ul>
          </div>
            
             <button class="btn btn-primary " class="pull-left" data-toggle="modal" data-target="#myModal">Add Category</button>
             <hr>
                 <br>
            <!--<h1>hello</h1>-->
            <?php
            $id=$_GET['id'];
            $id= base64_decode($id);
        
        if($_SESSION['type_app']  == 'ADMIN'){
           $sql="SELECT * FROM `category` where cat_id='".$id."' ";
         // var_dump($sql);
          $check= mysqli_query($conn, $sql);
        }
        else{
          $sql="SELECT * FROM `appuser_category` where `storeadmin_id`='{$_SESSION['id']}' and  `type_app_admin`='{$_SESSION['type_app']}' and cat_id='".$id."' ";
         // var_dump($sql);
          $check= mysqli_query($conn, $sql);
        }
          $resultcheck= mysqli_fetch_array($check,MYSQLI_BOTH);
          //var_dump($resultcheck);

 
 ?>
         
            
                
               
             <div class="box">
            <div class="box-header">
              <h3 class="box-title">All Categories</h3>

             
            </div>
            
                 <form role="form" action="edit-cat.php?id=<?php echo base64_encode($id); ?>" method="POST" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label for="exampleInputEmail1">Category Name</label>
                  <input type="text" class="form-control"  value="<?php echo $resultcheck['category_name']; ?>" name="catname" required="required">
                </div>
                
                <div class="form-group">
                  <label for="exampleInputFile">Category Image</label>
                  <input type="file" id="exampleInputFile" name="catimg" >

                  <p class="help-block">Please upload GIf,JPG,Jpeg,BMP,PNG files only.Old Image will deleted if you do not want to delete Leave is Blank</p>
                </div>
                
              </div>
        
        
        
      
      <div class="modal-footer">
          <input type="submit" class="btn btn-primary" value="Edit Category" name="addcat">
      </form>
            </section>
      </div></section>
</div>
      <?php include 'footer.php'; ?>

             <!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add New Category</h4>
      </div>
      <div class="modal-body">
        
          <form role="form" action="add-cat.php" method="POST" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label for="exampleInputEmail1">Category Name</label>
                  <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Enter Category Name" name="catname" required="required">
                </div>
                
                <div class="form-group">
                  <label for="exampleInputFile">Category Image</label>
                  <input type="file" id="exampleInputFile" name="catimg" >

                  <p class="help-block">Please upload GIf,JPG,Jpeg,BMP,PNG files only.</p>
                </div>
                
              </div>
        
        
        
      
      <div class="modal-footer">
          <input type="submit" class="btn btn-default" value="Add Category" name="addcat">
      </form>
</div>
      </div>
    </div>

  </div>
</div>
      
      
      
      
      
      
      
     
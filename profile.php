<?php include 'header.php';?>
<?php include  'sidebar.php';?>
  <?php include 'widget.php';?>
      <?php
       
       //$uid=$_GET['id'];
       //$uid=base64_decode($uid);
           $sql="SELECT * FROM `admin` where `admin_id`={$_SESSION['id']}";
         // var_dump($sql);
          $check= mysqli_query($conn, $sql);
          $resultcheck= mysqli_fetch_array($check,MYSQLI_ASSOC);
          //var_dump($resultcheck);


 
 
// var_dump($resultcheck);
 ?>
    <!-- right column -->
        <div class="col-md-6">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Edit Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form class="form-horizontal" action="upprofile.php" method="post">
              <div class="box-body">
                 
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">Email</label>

                  <div class="col-sm-10">
                      <input type="email" class="form-control" id="inputEmail3" placeholder="Email" value="<?php echo $resultcheck['email']; ?>" name="email" readonly="readonly" >
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputPassword3" class="col-sm-2 control-label">Name</label>

                  <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputPassword3" placeholder="Name" value="<?php echo $resultcheck['name']; ?>" name="name">
                  </div>
                </div>
                <div class="form-group">
                  <label for="inputPassword3" class="col-sm-2 control-label">Old Password</label>

                  <div class="col-sm-10">
                      <input type="password" class="form-control" id="inputPassword3" placeholder="old password" name="oldpass">
                  </div>
                </div>
                
                <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">New Password</label>

                 <div class="col-sm-10">
                      <input type="password" class="form-control" id="inputPassword3" placeholder="New password" name="password">
                  </div>
                </div>
                
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                  <input type="submit" class="btn btn-default pull-right" value="Update" name="update">
               
              </div>
              <!-- /.box-footer -->
            </form>
          
      <?php include 'scriptfooter.php'; ?>


      

             
            





 

 

  
 

















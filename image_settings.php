<?php include 'header.php';?>
<?php include  'sidebar.php';?>
  <?php include 'widget.php';?>
      
       

<?php $editimageshandel="SELECT image_handel FROM admin";
$gethandel=mysqli_query($conn, $editimageshandel);
$resulthandel=mysqli_fetch_array($gethandel,MYSQLI_ASSOC);
$imagehandel=$resulthandel['image_handel'];
//var_dump($imagehandel);
?> 
 
 

 
    <!-- right column -->
        <div class="col-md-6">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Product Image Upload Settings</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form class="form-horizontal" method="post">
              <div class="box-body">
                
                <div class="form-group">
                  <label for="inputEmail3" class="col-sm-2 control-label">NO OF IMAGE</label>

                  <div class="col-sm-10">
           <input type="number" class="form-control" id="inputEmail3" placeholder="How much product images you want to upload" value="<?php echo $imagehandel; ?>" name="imagehandel"  >
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


      <?php 
if(isset($_POST['update']))
{


$imagehandel1=$_POST['imagehandel'];
$editimageshandel="Update admin set image_handel='".$imagehandel1."'";
$gethandel=mysqli_query($conn, $editimageshandel);

if($gethandel=='true');
{
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Image handel updated ",
  text: "now you can add new products",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "add-product.php";
//console.log('The Ok Button was clicked.');
});
</script>
<?php

}
}
  ?>           
            





 

 

  
 

















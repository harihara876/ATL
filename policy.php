<?php include 'header.php'; ?>
  <?php include 'sidebar.php'; ?>
<?php include 'widget.php'; ?>
<?php
if(isset($_POST['addterm']))
	{ 

$des=htmlspecialchars($_POST['editor0']);

$sql1="UPDATE `policy` SET terms='".$des."'";
$update_admin = mysqli_query($conn,$sql1);
if($update_admin){
    ?>
      
      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Terms Updated ",
  text: "Successfully",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "policy.php";
//console.log('The Ok Button was clicked.');
});
</script>

<?php
}
}
             
             
             
?>
<?php 

 $policy="SELECT * FROM  `policy`";

$result=mysqli_query($conn,$policy);

$row = mysqli_fetch_assoc($result);

?>
 <?php 

 $policy="SELECT * FROM  `policy`";

$result=mysqli_query($conn,$policy);

$row = mysqli_fetch_assoc($result);

?>
<div class="modal-content ">
      <div class="modal-header">
        
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
<div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-question-circle fa-4" aria-hidden="true"></i> Policies</h3>
            </div>
            <!-- /.box-header -->
            <table class="table  table-responsive">
                <tr>
                    <td>
            <div class="box-body">
            <div class="form-container">
<form role="form" method="POST" enctype="multipart/form-data"  >
                <!-- text input -->
               
                  
                
                <div class="form-group">
                 <center> <label>Terms & Conditions</label></center>
                   
								<textarea name="editor0" id="editor0" rows="10" cols="80">
								<?php echo $row['terms'];?>	
								</textarea>
								<script src="ckeditor/ckeditor.js"></script>
								<script>
									// Replace the <textarea id="editor1"> with a CKEditor
									// instance, using default configuration.
									
									CKEDITOR.replace( 'editor0' );
								</script>
							</div>

<div class="box-footer">
                   <input type="submit" class="btn btn-primary pull-right" value="Update Terms & Conditions" name="addterm" id="postme"  title='Fill all the deatails completely'>

              </div>
              </form>
            </div>
            </div>
            
            
                   
            
       <?php include'scriptfooter.php'?>     
            

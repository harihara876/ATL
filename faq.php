<?php include 'header.php'; ?>
  <?php include 'sidebar.php'; ?>
<?php include 'widget.php'; ?>
<?php
if(isset($_POST['addfaq']))
	{ 
$title=$_POST['name'];
        $remove[] = "'";
        $remove[] = '"';
        $remove[] = "-"; // just as another example
        $title = str_replace( $remove, "", $title ); 


$des=htmlspecialchars($_POST['editor1']);

$sql1="UPDATE `faq` SET title='".$title."', description='".$des."'";
$update_admin = mysqli_query($conn,$sql1);
if($update_admin){
    ?>
      
      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "FAQ Updated ",
  text: "Successfully",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "faq.php";
//console.log('The Ok Button was clicked.');
});
</script>

<?php
               } else {
                            
                     echo 'query fail';
echo 'window.location.href = "faq.php"';

                        
                        }
   //exit();
}else{ 

?>
<?php $faq="SELECT *   FROM faq";
$faqquery=mysqli_query($conn, $faq);
$resultfaq=mysqli_fetch_array($faqquery,MYSQLI_ASSOC);?>
<div class="modal-content ">
      <div class="modal-header">
        
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
<div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-question-circle fa-4" aria-hidden="true"></i> FAQ</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
            <div class="form-container">
<form role="form" method="POST" enctype="multipart/form-data"  >
                <!-- text input -->
                <div class="form-group">
                  <label>Title:</label>
                  <input type="text" class="form-control" placeholder="Enter Title" name="name" required value="<?php echo $resultfaq['title']; ?>">
                </div>
                
                
                <div class="form-group">
                  <label>Description:</label>
                   
								<textarea name="editor1" id="editor1" rows="10" cols="80">
								<?php echo $resultfaq['description']; ?>	
								</textarea>
								<script src="ckeditor/ckeditor.js"></script>
								<script>
									// Replace the <textarea id="editor1"> with a CKEditor
									// instance, using default configuration.
									
									CKEDITOR.replace( 'editor1' );
								</script>
							</div>

<div class="box-footer">
                   <input type="submit" class="btn btn-primary pull-right" value="Update FAQ" name="addfaq" id="postme"  title='Fill all the deatails completely'>

              </div>
              </form>
            </div>

<?php } ?>
<?php include'scriptfooter.php';?>
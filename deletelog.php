<?php 
include 'header.php';

  $id=$_GET['id'];
  $id=base64_decode($id);
  //var_dump($id);


// SUPER - ADMIN
if($_SESSION['type_app']  == 'ADMIN'){
     
          $query="DELETE FROM `logs` where id='".$id."' ";
          $result=mysqli_query($conn,$query) or die("not Deleted". mysql_error());
          if($result==TRUE)
                                    {
              ?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    swal({
  title: "Log Deleted",
  text: "Log User Deleted ",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "logs.php";
//console.log('The Ok Button was clicked.');
});
</script>
<?php
          }else
          {?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    swal({
  title: "Log Not Deleted",
  text: "Fail to Deleted ",
  icon: "error",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "logs.php";
//console.log('The Ok Button was clicked.');
});
</script>
<?php }
          
}
         

?>
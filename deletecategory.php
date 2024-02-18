<?php 
include 'header.php';

  $id=$_GET['id'];
  $id=base64_decode($id);
  //var_dump($id);


// SUPER - ADMIN
if($_SESSION['type_app']  == 'ADMIN'){
      
           $sql="SELECT * FROM `category` where cat_id='".$id."' ";
         // var_dump($sql);
          $check= mysqli_query($conn, $sql);
          $resultcheck= mysqli_fetch_array($check,MYSQLI_ASSOC);
          //echo'<pre>'; var_dump($resultcheck);

          $imgpath=$resultcheck['category_image'];

          $imgpath=str_replace($serverimg,'',$imgpath);
          //var_dump( $imgpath);
          //exit();
          $query="DELETE FROM `category` where cat_id='".$id."' ";
          $result=mysqli_query($conn,$query) or die("not Deleted". mysql_error());
          if($result==TRUE)
                                    {
              unlink($imgpath);
              ?>
               <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Category Deleted",
  text: "Category Image Deleted ",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "category.php";
//console.log('The Ok Button was clicked.');
});
</script><?php
          }else
          {?>

              <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Category NOt Deleted",
  text: "Fail to Deleted ",
  icon: "error",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "category.php";
//console.log('The Ok Button was clicked.');
});
</script>
         <?php }
          
}

// STORE - ADMIN

else{


           $sql="SELECT * FROM `appuser_category` where `storeadmin_id`='{$_SESSION['id']}' and  `type_app_admin`='{$_SESSION['type_app']}' and cat_id='".$id."' ";
         // var_dump($sql);
          $check= mysqli_query($conn, $sql);
          $resultcheck= mysqli_fetch_array($check,MYSQLI_ASSOC);
          //echo'<pre>'; var_dump($resultcheck);

          $imgpath=$resultcheck['category_image'];

          $imgpath=str_replace($serverimg,'',$imgpath);
          //var_dump( $imgpath);
          //exit();
          $query="DELETE FROM `appuser_category` where `storeadmin_id`='{$_SESSION['id']}' and  `type_app_admin`='{$_SESSION['type_app']}' and cat_id='".$id."' ";
          $result=mysqli_query($conn,$query) or die("not Deleted". mysql_error());
          if($result==TRUE)
                                    {
              unlink($imgpath);
              ?>
               <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Category Deleted",
  text: "Category Image Deleted ",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "category.php";
//console.log('The Ok Button was clicked.');
});
</script><?php
          }else
          {?>

              <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Category NOt Deleted",
  text: "Fail to Deleted ",
  icon: "error",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "category.php";
//console.log('The Ok Button was clicked.');
});
</script>
         <?php }


}          

?>
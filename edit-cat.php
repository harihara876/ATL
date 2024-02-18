<?php 
include'header.php';
$category_name = $_POST['catname'];
$category_image = $_FILES['catimg']['name'];

 $id=$_GET['id'];
 
          


            $id= base64_decode($id);
// SUPER - ADMIN
if($_SESSION['type_app']  == 'ADMIN'){

           $sql="SELECT * FROM `category` where cat_id='".$id."' ";
         // var_dump($sql);
          $check= mysqli_query($conn, $sql);
          $resultcheck= mysqli_fetch_array($check,MYSQLI_BOTH);
$oldimg=$resultcheck['category_image'];
$oldimg=str_replace($serverimg,'',$oldimg);

if(empty($category_image))
{
 $category_insert = mysqli_query($conn,"UPDATE category SET category_name='".$category_name."' , category_image='".$oldimg."' where cat_id='".$id."'");
 ?><script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Succesfully Edited ",
  text: "Image File Not Updated",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "category.php";
//console.log('The Ok Button was clicked.');
});
</script>
   <?php     
}else
 {
     
$new=rand(99,1000);
$target_dir = "uploads/";
$target_file = $target_dir .$new.preg_replace('/\s/', '',basename($_FILES["catimg"]["name"]));
$target_file1= $serverimg.$target_file;
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
echo'failed to upload file';
// if everything is ok, try to upload file
} else {

    unlink($oldimg);
    if (move_uploaded_file($_FILES["catimg"]["tmp_name"], $target_file)) {
        
        $category_insert = mysqli_query($conn,"UPDATE category SET category_name='".$category_name."' , category_image='".$target_file1."' where cat_id='".$id."'");
        ?>

 <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Category Update ",
  text: "Image Uploded Successfully",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "category.php";
//console.log('The Ok Button was clicked.');
});
</script>
<?php
        
        //echo "The file ". basename( $_FILES["catimg"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }

}
 }


}
// STORE - ADMIN 
else
{

           $sql="SELECT * FROM `appuser_category` where `storeadmin_id`='{$_SESSION['id']}' and  `type_app_admin`='{$_SESSION['type_app']}' and cat_id='".$id."' ";
         // var_dump($sql);
          $check= mysqli_query($conn, $sql);
          $resultcheck= mysqli_fetch_array($check,MYSQLI_BOTH);
$oldimg=$resultcheck['category_image'];
$oldimg=str_replace($serverimg,'',$oldimg);

if(empty($category_image))
{
 $category_insert = mysqli_query($conn,"UPDATE `appuser_category` SET category_name='".$category_name."' , category_image='".$oldimg."' where `storeadmin_id`='{$_SESSION['id']}' and  `type_app_admin`='{$_SESSION['type_app']}' and cat_id='".$id."'");
 ?><script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Succesfully Edited ",
  text: "Image File Not Updated",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "category.php";
//console.log('The Ok Button was clicked.');
});
</script>
   <?php     
}else
 {
     
$new=rand(99,1000);
$target_dir = "uploads/";
$target_file = $target_dir .$new.preg_replace('/\s/', '',basename($_FILES["catimg"]["name"]));
$target_file1= $serverimg.$target_file;
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
echo'failed to upload file';
// if everything is ok, try to upload file
} else {

    unlink($oldimg);
    if (move_uploaded_file($_FILES["catimg"]["tmp_name"], $target_file)) {
        
        $category_insert = mysqli_query($conn,"UPDATE `appuser_category` SET category_name='".$category_name."' , category_image='".$target_file1."' where `storeadmin_id`='{$_SESSION['id']}' and  `type_app_admin`='{$_SESSION['type_app']}' and cat_id='".$id."'");
        ?>

 <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Category Update ",
  text: "Image Uploded Successfully",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "category.php";
//console.log('The Ok Button was clicked.');
});
</script>
<?php
        
        //echo "The file ". basename( $_FILES["catimg"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }

}
 }




}


 ?>
 

    
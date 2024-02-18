<?php 
include'header.php';

$Sub_Category_Id = $_POST['chk'];
$Sub_Sub_Category_Name = $_POST['catname'];
$Description = $_POST['Description'];
$Image = $_FILES['catimg']['name'];

 $id=$_GET['id'];
   
            $id= base64_decode($id);

// SUPER - ADMIN 

if($_SESSION['type_app']  == 'ADMIN'){



           $sql="SELECT * FROM `subsubcategories` where Sub_Sub_Category_Id='".$id."' ";
         // var_dump($sql);
          $check= mysqli_query($conn, $sql);
          $resultcheck= mysqli_fetch_array($check,MYSQLI_BOTH);
$oldimg=$resultcheck['Image'];
$oldimg=str_replace($serverimg,'',$oldimg);

if(empty($Image))
{
 $category_insert = mysqli_query($conn,"UPDATE subsubcategories SET  Sub_Sub_Category_Name='".$Sub_Sub_Category_Name."',Description='".$Description."',Sub_Category_Id='".$Sub_Category_Id."' , Image='".$oldimg."' where Sub_Sub_Category_Id='".$id."'");
 ?><script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Succesfully Edited ",
  text: "Image File Not Updated",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "subsubcategory.php";
//console.log('The Ok Button was clicked.');
});
</script>
   <?php     
}else
 {
     
$new=rand(99,1000);
$target_dir = "uploads/subsubcategory/";
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
        
        $category_insert = mysqli_query($conn,"UPDATE subsubcategories SET Sub_Sub_Category_Name='".$Sub_Sub_Category_Name."',Description='".$Description."',Sub_Category_Id='".$Sub_Category_Id."' ,  Image='".$target_file1."' where Sub_Category_Id='".$id."'");
        ?>

 <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Sub SubCategory Update ",
  text: "Image Uploded Successfully",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "subsubcategory.php";
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
else{


           $sql="SELECT * FROM `appuser_subsubcategories` where `storeadmin_id`='{$_SESSION['id']}' and `type_app_admin`='{$_SESSION['type_app']}' and Sub_Sub_Category_Id='".$id."' ";
         // var_dump($sql);
          $check= mysqli_query($conn, $sql);
          $resultcheck= mysqli_fetch_array($check,MYSQLI_BOTH);
$oldimg=$resultcheck['Image'];
$oldimg=str_replace($serverimg,'',$oldimg);

if(empty($Image))
{
 $category_insert = mysqli_query($conn,"UPDATE appuser_subsubcategories SET  Sub_Sub_Category_Name='".$Sub_Sub_Category_Name."',Description='".$Description."',Sub_Category_Id='".$Sub_Category_Id."' , Image='".$oldimg."' where `storeadmin_id`='{$_SESSION['id']}' and `type_app_admin`='{$_SESSION['type_app']}' and Sub_Sub_Category_Id='".$id."'");
 ?><script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Succesfully Edited ",
  text: "Image File Not Updated",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "subsubcategory.php";
//console.log('The Ok Button was clicked.');
});
</script>
   <?php     
}else
 {
     
$new=rand(99,1000);
$target_dir = "uploads/subsubcategory/";
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
        
        $category_insert = mysqli_query($conn,"UPDATE appuser_subsubcategories SET Sub_Sub_Category_Name='".$Sub_Sub_Category_Name."',Description='".$Description."',Sub_Category_Id='".$Sub_Category_Id."' ,  Image='".$target_file1."' where `storeadmin_id`='{$_SESSION['id']}' and `type_app_admin`='{$_SESSION['type_app']}' and Sub_Category_Id='".$id."'");
        ?>

 <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Sub SubCategory Update ",
  text: "Image Uploded Successfully",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "subsubcategory.php";
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
 

    
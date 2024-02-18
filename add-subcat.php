<?php 
include 'header.php';
if(isset($_POST['addcat']))
    {
//echo 'button pressed';
$cat_id = $_POST['chk'];
$Sub_Category_Name = $_POST['catname'];
///////////////////////////////////////////////////
//print_r($_POST);
// SUPER - ADMIN
if($_SESSION['type_app']  == 'ADMIN'){


$sql="SELECT * FROM `subcategories` ";
         // var_dump($sql);
          $check= mysqli_query($conn, $sql);
          $resultcheck= mysqli_fetch_array($check,MYSQLI_BOTH);
         // $catname=$resultcheck['Sub_Category_Name'];
          
          
foreach($check as $checkcat){
if($checkcat['Sub_Category_Name']==$Sub_Category_Name)
{
$ok=1;
}else{
$ok=0;
}
}

if($ok==1){?>
 <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Sub Category Already Exist ",
  text: "Choose a different name",
  icon: "error",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "subcategory.php";
//console.log('The Ok Button was clicked.');
});
</script>
<?php }else{          











//////////////////////////////////////////////////////////
$new=rand(99,1000);
$category_image = $_FILES['catimg']['name'];
$category_image=preg_replace('/\s/', '',$category_image);
$target_dir = "uploads/subcategory/";
$target_file = $target_dir . $new.preg_replace('/\s/', '',basename($_FILES["catimg"]["name"]));
$target_file1= $serverimg.$target_file;
$uploadOk = 1;
//var_dump($target_file);
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
//var_dump($imageFileType);
//var_dump($uploadOk);

// Check if file already exists
//if (file_exists($target_file)) {
   // echo "Sorry, file already exists.";
   // $uploadOk = 0;
//}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
    echo '$uploadOk';
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
   ?>
         <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "OOPS ",
  text: "file Not Uploded",
  icon: "error",button: "close"
}).then(function() {
// Redirect the user
//window.location.href = "subcategory.php";
//console.log('The Ok Button was clicked.');
});
</script>
        
        <?php
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["catimg"]["tmp_name"], $target_file)) {
        
        $category_insert = mysqli_query($conn,"INSERT INTO subcategories (Sub_Category_Name,Image,cat_id) VALUES('".$Sub_Category_Name."','".$target_file1."','".$cat_id."')");
        ?>

 <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Sub Category Added ",
  text: "Successfully",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "subcategory.php";
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
else{

$sql="SELECT * FROM `subcategories` where `storeadmin_id`='{$_SESSION['id']}' and `type_app_admin`='{$_SESSION['type_app']}' ";
         // var_dump($sql);
          $check= mysqli_query($conn, $sql);
          $resultcheck= mysqli_fetch_array($check,MYSQLI_BOTH);
         // $catname=$resultcheck['Sub_Category_Name'];
          
          
foreach($check as $checkcat){
if($checkcat['Sub_Category_Name']==$Sub_Category_Name)
{
$ok=1;
}else{
$ok=0;
}
}

if($ok==1){?>
 <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Sub Category Already Exist ",
  text: "Choose a different name",
  icon: "error",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "subcategory.php";
//console.log('The Ok Button was clicked.');
});
</script>
<?php }else{          











//////////////////////////////////////////////////////////
$new=rand(99,1000);
$category_image = $_FILES['catimg']['name'];
$category_image=preg_replace('/\s/', '',$category_image);
$target_dir = "uploads/subcategory/";
$target_file = $target_dir . $new.preg_replace('/\s/', '',basename($_FILES["catimg"]["name"]));
$target_file1= $serverimg.$target_file;
$uploadOk = 1;
//var_dump($target_file);
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
//var_dump($imageFileType);
//var_dump($uploadOk);

// Check if file already exists
//if (file_exists($target_file)) {
   // echo "Sorry, file already exists.";
   // $uploadOk = 0;
//}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
    echo '$uploadOk';
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
   ?>
         <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "OOPS ",
  text: "file Not Uploded",
  icon: "error",button: "close"
}).then(function() {
// Redirect the user
//window.location.href = "subcategory.php";
//console.log('The Ok Button was clicked.');
});
</script>
        
        <?php
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["catimg"]["tmp_name"], $target_file)) {
        
        $category_insert = mysqli_query($conn,"INSERT INTO subcategories (Sub_Category_Name,Image,cat_id,storeadmin_id,type_app_admin) VALUES('".$Sub_Category_Name."','".$target_file1."','".$cat_id."','{$_SESSION['id']}','{$_SESSION['type_app']}')");
        ?>

 <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Sub Category Added ",
  text: "Successfully",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "subcategory.php";
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





// STORE - ADMIN ENDS

 }else {
        
        header("location:dashboard.php"); }
        
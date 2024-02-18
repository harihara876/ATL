<?php  if(isset($_POST['update']))
{
include 'header.php';


$email=$_POST['email'];
$name=$_POST['name'];
$old=$_POST['oldpass'];
$newpass=$_POST['password'];
//$email=filter_var($email, FILTER_SANITIZE_EMAIL);
//var_dump($uid);
//var_dump($email);
//var_dump($password);
 $sql="SELECT * FROM `admin` where `admin_id`={$_SESSION['id']} ";
         // var_dump($sql);
          $check= mysqli_query($conn, $sql);
          $resultcheck= mysqli_fetch_array($check,MYSQLI_ASSOC);
          $oldpass=$resultcheck['password'];


          if($oldpass==$old)
          {
$pwhash = password_hash($newpass, PASSWORD_DEFAULT);
$sql1="UPDATE `admin` SET name='".$name."', email='".$email."', password='".$newpass."', pwhash='" . $pwhash . "' WHERE `admin_id`={$_SESSION['id']}  ";
$update_admin = mysqli_query($conn,$sql1);
if($update_admin){
    ?>

      <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Password Updated Successfully",
  text: "Please Login Again",
  icon: "success",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "logout.php";
//console.log('The Ok Button was clicked.');
});
</script><?php
               } else {

                     echo 'query fail';

                        }
   //exit();
}

else
{?>
 <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
     <script type="text/javascript">
    swal({
  title: "Incorrect old Password",
  text: "enter your previous password",
  icon: "error",button: "close"
}).then(function() {
// Redirect the user
window.location.href = "profile.php";
//console.log('The Ok Button was clicked.');
});
</script>
<?php
}
}else {

echo "button is not pressed ";
echo'<script>
window.location.href = "profile.php"</script>';

}
?>
<?php

/**
 * Fetches user info by ID.
 * @param object DB connection object.
 * @param int $userID User ID.
 * @return array User Info.
 */
function getUserInfoByID($conn, $userID)
{
    $selectSQL = "SELECT * FROM `device_users` WHERE `id` = {$userID} LIMIT 1";
    $result = mysqli_query($conn, $selectSQL);
    return mysqli_fetch_array($result, MYSQLI_ASSOC);
}

require_once("header.php");
require_once("sidebar.php");
require_once("widget.php");
?>

<style>
    .form-container{
        margin-left:5px;
        margin-top:10px;
    }
</style>

<?php
// Get user ID from query string.
$userID = ($_GET["id"]) ? $_GET["id"] : NULL;
if (!$userID) {
    echo "<script>
            alert('Access not allowed.');
            window.location.assign('users.php');
        </script>";
} else {
    $userInfo = getUserInfoByID($conn, $userID);
    if (!$userInfo) {
        echo "<script>
            alert('User not found.');
            window.location.assign('users.php');
        </script>";
    }
}
?>

<form role="form" method="POST" enctype="multipart/form-data" action="update-user.php">
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-user fa-4" aria-hidden="true"></i> Edit User
            </h3>
        </div>
        <div class="box-body">
            <div class="form-container">
                <input type="hidden" name="user_id" value="<?php echo $userInfo['id']; ?>">
                <div class="form-group">
                    <label>First Name:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter first name"></i>
                    <input type="text" class="form-control" placeholder="Enter first name" name="first_name" required value="<?php echo $userInfo['first_name']; ?>" required>
                </div>
                <div class="form-group">
                    <label >Last Name:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter last name"></i>
                    <input type="text" class="form-control" placeholder="Enter last name" name="last_name" required value="<?php echo $userInfo['last_name']; ?>">
                </div>
                <div class="form-group">
                    <label>Email:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter email"></i>
                    <input type="text" class="form-control" placeholder="Enter email" name="user_email" required value="<?php echo $userInfo['email'];?>">
                </div>
                <div class="form-group">
                    <label >Password:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter password"></i>

                    <input type="password" class="form-control" placeholder="Enter password only Numeric" name="password" value="<?php echo $userInfo['password']; ?>" title="Only Numeric Text">
                </div>
                <?php  if($_SESSION['type_app']  == 'ADMIN'){ ?>
                <div class="form-group">
                    <label >Admin Name:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter Admin"></i>
                    <select class="form-control" name="admin_name" required>
 <?php 
 $a = "SELECT `admin_id`,`name` FROM `admin` where `admin_id`={$userInfo["storeadmin_id"]} "; 
    $r_a = mysqli_query($conn, $a);

 while ($r_u = mysqli_fetch_array($r_a, MYSQLI_ASSOC)) {
                                    $name_u = $r_u['name']; 
                                    $name_id = $r_u['admin_id']; 

?>     
                                <option value="<?php echo $name_id;   ?>"><?php echo ucfirst($name_u);   ?></option>
                                
                                
<?php } ?>  
<?php 
 $a = "SELECT `admin_id`,`name` FROM `admin` "; 
    $r_a = mysqli_query($conn, $a);

 while ($r_u = mysqli_fetch_array($r_a, MYSQLI_ASSOC)) {
                                    $name_u = $r_u['name']; 
                                    $name_id = $r_u['admin_id']; 

?>     
                                <option value="<?php echo $name_id;   ?>"><?php echo ucfirst($name_u);   ?></option>
                                
<?php } ?> 
                               
                    </select>

                </div>
            <?php } ?>
                
            </div>
        </div>
        <div class="box-footer">
            <input type="submit" class="btn btn-primary" value="Update User" name="update-user-btn" id="postme">
        </div>
    </div>
</form>

<?php require_once("scriptfooter.php"); ?>
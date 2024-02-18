<?php

/**
 * Fetches user info by ID.
 * @param object DB connection object.
 * @param int $userID User ID.
 * @return array User Info.
 */
function getUserInfoByID($conn, $userID)
{
    $selectSQL = "SELECT * FROM `admin` WHERE `admin_id` = {$userID} LIMIT 1";
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
            window.location.assign('storeadmin.php');
        </script>";
} else {
    $userInfo = getUserInfoByID($conn, $userID);
    if (!$userInfo) {
        echo "<script>
            alert('User not found.');
            window.location.assign('storeadmin.php');
        </script>";
    }
}
?>

<form role="form" method="POST" enctype="multipart/form-data" action="update-storeadmin.php">
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-user fa-4" aria-hidden="true"></i> Edit Store Admin
            </h3>
        </div>
        <div class="box-body">
            <div class="form-container">
                <input type="hidden" name="user_id" value="<?php echo $userInfo['admin_id']; ?>">
                <div class="form-group">
                    <label>Name:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter name"></i>
                    <input type="text" class="form-control" placeholder="Enter name" name="name" required value="<?php echo $userInfo['name']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter email"></i>
                    <input type="text" class="form-control" placeholder="Enter email" name="email"  value="<?php echo $userInfo['email'];?>">
                </div>
            </div>
        </div>
        <div class="box-footer">
            <input type="submit" class="btn btn-primary" value="Update Store Admin" name="update-user-btn" id="postme">
        </div>
    </div>
</form>

<?php require_once("scriptfooter.php"); ?>
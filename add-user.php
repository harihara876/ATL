<?php
require_once("header.php");

/**
 * Creates new user.
 * @param object DB connection object.
 * @return int Last insert ID.
 */
function createUser($conn)
{
    $firstName  = (isset($_POST["first_name"])) ? $_POST["first_name"] : NULL;
    $lastName   = (isset($_POST["last_name"])) ? $_POST["last_name"] : NULL;
    $email      = (isset($_POST["user_email"])) ? $_POST["user_email"] : NULL;
    $store_admin_id   = (isset($_POST["store_id"])) ? $_POST["store_id"] : NULL;
    $password_u   = (isset($_POST["password"])) ? $_POST["password"] : NULL;
    

if($_SESSION['type_app']  == 'ADMIN'){
    $storeadmin_id   = (isset($_POST["admin_name"])) ? $_POST["admin_name"] : NULL;
$a = "SELECT `admin_id`,`type_appstatus` FROM `admin` where `admin_id`={$storeadmin_id} "; 
    $r_a = mysqli_query($conn, $a);

 while ($r_u = mysqli_fetch_array($r_a, MYSQLI_ASSOC)) {
                                    $type_app_admin = $r_u['type_appstatus']; 
}
}
else{
    $type_app_admin   = $_SESSION["type_app"];
    $storeadmin_id   = $_SESSION['id'];
    
}
    //$type_app_admin   = $_SESSION["type_app"];

    if (!$firstName) {
        return FALSE;
    }

    if (!$email) {
        return FALSE;
    }
    //$password = md5($password_u);
    
    //$password = password_hash("1234", PASSWORD_DEFAULT);
    


    //$password = password_hash($password_u, PASSWORD_DEFAULT);
	$password = password_hash($password_u, PASSWORD_DEFAULT);

    $insertSQL = "INSERT INTO `device_users` (`first_name`, `last_name`, `email`, `password`,`storeadmin_id`,`type_app_admin`)
        VALUES ('{$firstName}', '{$lastName}', '{$email}', '{$password}','{$storeadmin_id}','{$type_app_admin}' )";
    $inserted = mysqli_query($conn, $insertSQL);

    if ($inserted) {
        return mysqli_insert_id($conn);
    }

    return 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $created = createUser($conn);

    if ($created) {
        echo <<<_SCRIPT_
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script type="text/javascript">
                swal({
                    title: "Successfully Created",
                    icon: "success",
                    button: "close"
                }).then(function() {
                    window.location.href = "users.php";
                });
            </script>
_SCRIPT_;
    } else {
        echo <<<_SCRIPT_
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script type="text/javascript">
                swal({
                    title: "Failed to Create",
                    text: "Please try later.",
                    icon: "error",
                    button: "close"
                }).then(function() {
                    window.location.href = "users.php";
                });
            </script>
_SCRIPT_;
    }
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

<form role="form" method="POST" action="add-user.php">
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-user-plus fa-4" aria-hidden="true"></i> Add New User
            </h3>
        </div>
        <div class="box-body">
            <div class="form-container">
                <input type="hidden" name="store_id" value="<?php echo $_SESSION['id']; ?>">
                <div class="form-group">
                    <label>First Name:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter first name"></i>
                    <input type="text" class="form-control" placeholder="Enter first name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label >Last Name:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter last name"></i>
                    <input type="text" class="form-control" placeholder="Enter last name" name="last_name">
                </div>
                <div class="form-group">
                    <label>Email:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter email"></i>
                    <input type="text" class="form-control" placeholder="Enter email" name="user_email">
                </div>
                <div class="form-group">
                    <label >Password:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter password"></i>
                    <input type="password" class="form-control" placeholder="Enter password only Numeric" pattern="\d*" title="Only Numeric Text" name="password" required>
                </div>
                <?php  if($_SESSION['type_app']  == 'ADMIN'){?>
                <div class="form-group">
                    <label >Admin Name:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter Admin"></i>
                    <select class="form-control" name="admin_name" required>

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
            <input type="submit" class="btn btn-primary" value="Add User" name="add-user-btn" id="postme">
        </div>
    </div>
</form>

<?php require_once("scriptfooter.php"); ?>
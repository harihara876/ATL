<?php
require_once("header.php");

/**
 * Updates user info.
 * @param object $conn DB connection object.
 * @return boolean Status.
 */
function updateUser($conn)
{
    $userID     = (isset($_POST["user_id"])) ? $_POST["user_id"] : NULL;
    $firstName  = (isset($_POST["first_name"])) ? $_POST["first_name"] : NULL;
    $lastName   = (isset($_POST["last_name"])) ? $_POST["last_name"] : NULL;
    $email      = (isset($_POST["user_email"])) ? $_POST["user_email"] : NULL;
    $password_u   = (isset($_POST["password"])) ? $_POST["password"] : NULL;

    $modified_on = date("Y-m-d H:i:s");

    if (!$firstName) {
        return FALSE;
    }

    if (!$email) {
        return FALSE;
    }
   // $password = md5($password_u);

 if($_SESSION['type_app']  == 'ADMIN'){
    $storeadmin_id   = (isset($_POST["admin_name"])) ? $_POST["admin_name"] : NULL;

$a = "SELECT `storeadmin_id`,`password` FROM `device_users` where `admin_id`={$storeadmin_id} ";
    $r_a = mysqli_query($conn, $a);

 while ($r_u = mysqli_fetch_array($r_a, MYSQLI_ASSOC)) {
                                    $Pass_old = $r_u['password'];
}

if($password_u == $Pass_old ){
    $password = $Pass_old;
}else{
    $password = password_hash($password_u, PASSWORD_DEFAULT);
}


$a = "SELECT `admin_id`,`type_appstatus` FROM `admin` where `admin_id`={$storeadmin_id} ";
    $r_a = mysqli_query($conn, $a);

 while ($r_u = mysqli_fetch_array($r_a, MYSQLI_ASSOC)) {
                                    $type_app_admin = $r_u['type_appstatus'];
}
}else{

    $type_app_admin   = $_SESSION["type_app"];
    $storeadmin_id   = $_SESSION['id'];

    $a = "SELECT `storeadmin_id`,`password` FROM `device_users` where `admin_id`={$storeadmin_id} ";
    $r_a = mysqli_query($conn, $a);

 while ($r_u = mysqli_fetch_array($r_a, MYSQLI_ASSOC)) {
                                    $Pass_old = $r_u['password'];
}

if($password_u == $Pass_old ){
    $password = $Pass_old;
}else{
    $password = password_hash($password_u, PASSWORD_DEFAULT);
}




}

    $updateSQL  = "UPDATE `device_users`
        SET `first_name` = '{$firstName}',
        `last_name` = '{$lastName}',
        `email` = '{$email}',
        `password` = '{$password}',
        `modified_on`= '{$modified_on}',
        `storeadmin_id`= '{$storeadmin_id}',
        `type_app_admin`= '{$type_app_admin}'
        WHERE `id` = {$userID}";
    return mysqli_query($conn, $updateSQL);
}


if (isset($_POST["update-user-btn"])) {
    $updated = updateUser($conn);

    if ($updated) {
        echo <<<_SCRIPT_
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script type="text/javascript">
                swal({
                    title: "Successfully Updated",
                    // text: "Successfully updated.",
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
                    title: "Failed to Update",
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
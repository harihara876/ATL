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
    $Name  = (isset($_POST["name"])) ? $_POST["name"] : NULL;
    $email      = (isset($_POST["email"])) ? $_POST["email"] : NULL;
    $modified_on = date("Y-m-d H:i:s");
    if (!$Name) {
        return FALSE;
    }

    if (!$email) {
        return FALSE;
    }

    $updateSQL  = "UPDATE `admin`
        SET `name` = '{$Name}',
        `email` = '{$email}', `modified_on`= '{$modified_on}'
        WHERE `admin_id` = {$userID}";
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
                    window.location.href = "storeadmin.php";
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
                    window.location.href = "storeadmin.php";
                });
            </script>
_SCRIPT_;
    }
}
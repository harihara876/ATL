<?php
require_once("header.php");

/**
 * Deletes user.
 * @param object $conn DB connection object.
 * @return boolean Status.
 */
function deleteUser($conn)
{
    $userID = (isset($_GET["id"])) ? $_GET["id"] : NULL;
    if (!$userID) {
        return FALSE;
    }

    $deleteSQL  = "DELETE FROM `admin` WHERE `admin_id` = {$userID}";
    return mysqli_query($conn, $deleteSQL);
}

// $deleted = deleteUser($conn);

// Setting FALSE to stop deleting users from admin portal for security reasons.
$deleted = FALSE;

if ($deleted) {
    echo <<<_SCRIPT_
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script type="text/javascript">
            swal({
                title: "Successfully Deleted",
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
                title: "Failed to Delete",
                text: "Please try later.",
                icon: "error",
                button: "close"
            }).then(function() {
                window.location.href = "storeadmin.php";
            });
        </script>
_SCRIPT_;
}
<?php
require_once("header.php");

/**
 * Creates new user.
 * @param object DB connection object.
 * @return int Last insert ID.
 */
// function createStoreUser($conn)
// {

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $storeName  = (isset($_POST["store_name"])) ? $_POST["store_name"] : NULL;
    $password   = (isset($_POST["store_password"])) ? $_POST["store_password"] : NULL;
    $email      = (isset($_POST["storeuser_email"])) ? $_POST["storeuser_email"] : NULL;

    $sql = "SELECT * FROM `admin`";
    $check = mysqli_query($conn, $sql);

    foreach ($check as $checkcat) {
        if ($checkcat["email"] == $email) {
            $ok = 1;
        } else {
            $ok = 0;
        }
    }

    if ($ok == 1) {
?>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script type="text/javascript">
            swal({
                title: "Email Already Exist",
                text: "Choose a different email",
                icon: "error",button: "close"
            }).then(function() {
                // Redirect the user
                window.location.href = "add-storeadmin.php";
                //console.log('The Ok Button was clicked.');
            });
        </script>
    

<?php
    }
    else
    {

   // $password = password_hash("1234", PASSWORD_DEFAULT);

    $insertSQL = "INSERT INTO `admin` (`name`, `email`, `password`)
        VALUES ('{$storeName}', '{$email}', '{$password}')";
    $inserted = mysqli_query($conn, $insertSQL);



     echo <<<_SCRIPT_
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <script type="text/javascript">
                swal({
                    title: "Successfully Created",
                    icon: "success",
                    button: "close"
                }).then(function() {
                    window.location.href = "storeadmin.php";
                });
            </script>
_SCRIPT_;
    // if ($inserted) {
    //     return mysqli_insert_id($conn);
    // }

    // return 0;
}
}
//}
// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//    $created = createStoreUser($conn);

//     if ($created) {
//         echo <<<_SCRIPT_
//             <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
//             <script type="text/javascript">
//                 swal({
//                     title: "Successfully Created",
//                     icon: "success",
//                     button: "close"
//                 }).then(function() {
//                     window.location.href = "storeadmin.php";
//                 });
//             </script>
// _SCRIPT_;
//     } else {
//         echo <<<_SCRIPT_
//             <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
//             <script type="text/javascript">
//                 swal({
//                     title: "Failed to Create",
//                     text: "Please try later.",
//                     icon: "error",
//                     button: "close"
//                 }).then(function() {
//                     window.location.href = "storeadmin.php";
//                 });
//             </script>
// _SCRIPT_;
//     }
// }

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

<form role="form" method="POST" action="add-storeadmin.php">
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-user-plus fa-4" aria-hidden="true"></i> Add New Store Admin
            </h3>
        </div>
        <div class="box-body">
            <div class="form-container">
                <div class="form-group">
                    <label>Name:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter name"></i>
                    <input type="text" class="form-control" placeholder="Enter name" name="store_name" required>
                </div>
                <div class="form-group">
                    <label>Email:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter email"></i>
                    <input type="email" class="form-control" placeholder="Enter email" name="storeuser_email" required>
                </div>
                <div class="form-group">
                    <label >Password:</label> <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="top" title="Enter password"></i>
                    <input type="password" class="form-control" placeholder="Enter password" name="store_password" required>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <input type="submit" class="btn btn-primary" value="Add Store Admin" name="add-user-btn" id="postme">
        </div>
    </div>
</form>

<?php require_once("scriptfooter.php"); ?>
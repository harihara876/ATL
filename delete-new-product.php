<?php
require_once("header.php");

/**
 * Deletes new product.
 * @param object $conn DB connection object.
 * @return boolean Status.
 */
function deleteNewProduct($conn)
{
    $newProductID = (isset($_GET["id"])) ? $_GET["id"] : NULL;
    if (!$newProductID) {
        return FALSE;
    }

    $deleteSQL  = "DELETE FROM `products_temp` WHERE `id` = {$newProductID}";
    return mysqli_query($conn, $deleteSQL);
}

$deleted = deleteNewProduct($conn);
if ($deleted) {
    echo <<<_SCRIPT_
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script type="text/javascript">
            swal({
                title: "Successfully Deleted",
                icon: "success",
                button: "close"
            }).then(function() {
                window.location.href = "new-products.php";
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
                window.location.href = "new-products.php";
            });
        </script>
_SCRIPT_;
}
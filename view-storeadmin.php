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

<!-- Modal content-->
<div class="modal-content ">
    <div class="modal-header">
        <h4 class="modal-title">
            <i class="fa fa-user fa-2" aria-hidden="true"></i> User Details
        </h4>
    </div>
    <div class="modal-body">
        <br>
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
        <!-- Main content -->
        <section class="invoice">
            <!-- title row -->
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="page-header">
                        <i class="fa fa-eercast" aria-hidden="true"></i> Details
                    </h2>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-10">
                    <p class="lead">
                        <?php echo "{$userInfo["name"]}"; ?>
                    </p>
                    <div class="table">
                        <table class="table" >
                            <tr>
                                <th>Email:</th>
                                <td><?php echo $userInfo["email"]; ?></td>
                            </tr>
                            <tr>
                                <th>Created On:</th>
                                <td><?php echo $userInfo["created_on"]; ?></td>
                            </tr>
                            <tr>
                                <th>Updated On:</th>
                                <td><?php echo $userInfo["modified_on"]; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="modal-footer">
        <a href="edit-storeadmin.php?id=<?php echo $userInfo['admin_id']; ?>">
            <button type="button" class="btn btn-primary btn-lg  btn-success">
                <i class="fa fa-pencil-square fa-6" aria-hidden="true"></i> EDIT
            </button>
        </a>
      <!--   <a href="delete-user.php?id=<?php // echo $userInfo['admin_id']; ?>" onClick="return checkDelete()" class="btn btn-social-icon btn-google btn-lg">
            <i class="fa fa fa-trash-o"></i>
        </a> -->
    </div>
</div>

<?php require_once("footer.php"); ?>
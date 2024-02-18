<?php
require_once("header.php");
require_once("sidebar.php");
require_once("widget.php");
/**
 * Returns users list.
 * @param object DB connection object.
 * @return array Users.
 */
function getadminUsers($conn)
{
    $users = [];
    $selectSQL = "SELECT
            `id`,
            `first_name`,
            `last_name`,
            `email`,
            `status`,
            `created_on`,
            `storeadmin_id`
        FROM `device_users` 
        ORDER BY `first_name`  ASC";
    $result = mysqli_query($conn, $selectSQL);
//print_r($result);
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $users[] = $row;
    }

    return $users;
}

function getUsers($conn)
{
    $users = [];
    $selectSQL = "SELECT
            `id`,
            `first_name`,
            `last_name`,
            `email`,
            `status`,
            `created_on`
        FROM `device_users` WHERE `storeadmin_id`={$_SESSION['id']}
        ORDER BY `first_name`  ASC";
    $result = mysqli_query($conn, $selectSQL);
//print_r($result);
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $users[] = $row;
    }

    return $users;
}



?>

<!-- Modal content-->
<div class="modal-content ">
    <div class="modal-header">
        <h4 class="modal-title"> <i class="fa  fa-dropbox fa-5" aria-hidden="true"></i>
            All Users
        </h4>
    </div>
    <div class="modal-body">
        <a href="add-user.php">
            <button class="btn btn-primary" class="pull-left">
                <i class="fa fa-plus-circle fa-6" aria-hidden="true"></i> Add User
            </button>
        </a>
        <hr>
        <script language="JavaScript" type="text/javascript">
            function checkDelete(){
                return confirm('Are you sure you want to delete?');
            }
        </script>
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body">
                            <table id="employee_data" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
<?php if($_SESSION['type_app']  == 'ADMIN'){?> <th>Admin</th> <?php } ?>

                                        <th>Status</th>
                                        <th>Action</th>
                                        <th>Manage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php

                                if($_SESSION['type_app']  == 'ADMIN'){ $users = getadminUsers($conn); }
                                else{  $users = getUsers($conn); }

                                    foreach ($users as $user) {
                                        $name = "{$user["first_name"]} {$user["last_name"]}";
                                        $status = ($user["status"]) ? "Active" : "Deactivated";
                                        echo "<tr>
                                                <td>$name</td>
                                                <td>{$user["email"]}</td>";
  if($_SESSION['type_app']  == 'ADMIN'){  
    $a = "SELECT * FROM `admin` WHERE `admin_id`={$user["storeadmin_id"]} "; 
    $r_a = mysqli_query($conn, $a);

 while ($r_u = mysqli_fetch_array($r_a, MYSQLI_ASSOC)) {
                                    $name_u = $r_u['name']; 
    ?> 


    <td><?php echo ucfirst($name_u); ?></td>    
<?php } }

                                        echo   "<td>{$status}</td>
                                                <td>
                                                    <a href='view-user.php?id={$user["id"]}'>
                                                        <button class='btn btn-primary'>
                                                            View <i class='fa fa-eye' aria-hidden='true'></i>
                                                        </button>
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href='edit-user.php?id={$user["id"]}'>
                                                        <button class='btn btn-primary btn-success'>
                                                            <i class='fa fa-pencil' aria-hidden='true'></i>
                                                        </button>
                                                    </a>
                                                    <a href='delete-user.php?id={$user["id"]}' class='btn btn-social-icon btn-google' onClick='return checkDelete()'>
                                                        <i class='fa fa fa-trash-o'></i>
                                                    </a>
                                                </td>
                                            </tr>";
                                    }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script>
$(document).ready(function () {
    $('#employee_data').DataTable({
        "scrollX"       : true,
        "pagingType"    : "numbers",
        "processing"    : true,
        "searching"     : true,
        "ordering"      : true,
        "info"          : true,
        "autoWidth"     : false
    });
});
</script>
<?php require_once("scriptfooter.php"); ?>
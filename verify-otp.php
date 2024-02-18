<?php

require_once("config.php");
require_once("lib/t3storelib.php");

$app = new t3storeLib();

// If form is submitted, authenticate user and redirect to dashboard page.
if (!empty($_POST["otp"])) {
    if (!empty($_SESSION["otp"]) && $_SESSION["otp"] === $_POST["otp"]) {
            $app->Logs($_SESSION['admin_id']);
            header("Location: dashboard.php");
        } else {
            $msg = 'Invalid OTP';
        }
    } else {
        $msg = "Fields cannot be empty";
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Verify OTP</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.7 -->
        <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
        <!-- iCheck -->
        <link rel="stylesheet" href="plugins/iCheck/square/blue.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- Google Font -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    </head>
    <body class="hold-transition login-page">
        <style></style>
        <div class="login-box1">
            <div class="container">
                <div class="col-md-12">
                    <div class="login-box" style="margin-top: 120px;">
                        <div class="login-box-body">
                            <div style="margin-bottom: 30px">
                                <center><img src="logo.png" alt="Plat4m Logo" height="100"></center>
                            </div>
                            <p class="login-box-msg">
                                <font color="red" size="5">
                                <?php
                                if (isset($_POST['submit_super_admin'])) {
                                    print_r($msg);
                                } else {
                                    echo "OTP Verification";
                                }
                                ?>
                                </font>
                            </p>
                            <form method="post">
                                <div class="form-group has-feedback">
                                    <input type="password" class="form-control" placeholder="Otp" name="otp">
                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                </div>
                                <div class="row">
                                    <div class="col-xs-8">
                                        <div class="checkbox icheck"> </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <input type="submit" class="btn btn-primary btn-block btn-flat" name="submit_super_admin" value="Verify" />
                                    </div>
                                </div>
                            </form>
                        <!-- /.login-box-body -->
                        </div>
                    </div>
                </div>
            </div>
        <!-- /.login-box -->
        </div>
        <!-- jQuery 3 -->
        <script src="bower_components/jquery/dist/jquery.min.js"></script>
        <!-- Bootstrap 3.3.7 -->
        <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- iCheck -->
        <script src="plugins/iCheck/icheck.min.js"></script>
        <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
        </script>
    </body>
</html>
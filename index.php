<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';
require_once("config.php");
require_once("lib/t3storelib.php");

$app = new t3storeLib();
$mail = new PHPMailer(true);
// If session is set, redirect to dashboard page.
if (!empty($_SESSION["id"]) && !empty($_SESSION["name"])) {
    header("Location: dashboard.php");
    exit();
}

// If form is submitted, authenticate user and redirect to verify OTP page.
if (isset($_POST["submit_super_admin"])) {
    $user_email = $_POST["email"];
    $user_email = filter_var($user_email, FILTER_SANITIZE_EMAIL);
    $user_pwd   = filter_var($_POST["password"], FILTER_SANITIZE_STRING);

    if (!empty($user_email) && !empty($user_pwd)) {
        $result = $app->adminLogin($db, $user_email, $user_pwd);

        if ($result["type_appstatus"] === "ADMIN") {
            $generator = "1357902468";
            $otp = "";

            for ($i = 1; $i <= 4; $i++) {
                $otp .= substr($generator, (rand() % (strlen($generator))), 1);
            }

            $mail = new PHPMailer(TRUE);
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = "mail.plat4minc.com";
            $mail->SMTPAuth = TRUE;
            $mail->Username = "support@plat4minc.com";
            $mail->Password = ",2=Z4(wAP4,[";
            $mail->SMTPSecure = "tls";
            $mail->Port = "587";
            $mail->From = "support@plat4minc.com";
            $mail->FromName = "Plat4m Inc. - Support";
            $mail->addAddress($result["email"], $result["name"]);
            $mail->isHTML(FALSE);
            $mail->Subject = "Login Verification";
            $mail->Body = "Greeting from Plat4m Inc.\n";
            $mail->Body .= "Here is your OTP: $otp \n";
            $mail->Body .= "Thanks\n";
            $mail->Body .= "Plat4m Inc.";
            $mail->send();

            $_SESSION["otp"]        = $otp;
            $_SESSION["email"]      = $result["email"];
            $_SESSION["id"]         = $result["admin_id"];
            $_SESSION["name"]       = $result["name"];
            $_SESSION["img"]        = $result["user_img"];
            $_SESSION["store_name"] = $result["store_name"];
            $_SESSION["type_app"]   = $result["type_appstatus"];
    
            // Store in logs and redirect to dashboard page.
            $app->Logs($result['admin_id']);
            header("Location: verify-otp.php");
        } else if ($result["type_appstatus"] != "ADMIN") {
            // Set session.
            $_SESSION["id"]         = $result["admin_id"];
            $_SESSION["name"]       = $result["name"];
            $_SESSION["img"]        = $result["user_img"];
            $_SESSION["email"]      = $result["email"];
            $_SESSION["store_name"] = $result["store_name"];
            $_SESSION["type_app"]   = $result["type_appstatus"];

            // Store in logs and redirect to dashboard page.
            $app->Logs($result['admin_id']);
            header("Location: dashboard.php");
        } else {
            $msg = 'Invalid email or password';
        }
    } else {
        $msg = "Fields cannot be empty";
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Admin Portal | Log in</title>
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
                                    echo "Admin Sign In";
                                }
                                ?>
                                </font>
                            </p>
                            <form method="post">
                                <div class="form-group has-feedback">
                                    <input type="email" class="form-control" placeholder="Email" name="email">
                                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                </div>
                                <div class="form-group has-feedback">
                                    <input type="password" class="form-control" placeholder="Password" name="password">
                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                </div>
                                <div class="row">
                                    <div class="col-xs-8">
                                        <div class="checkbox icheck"> </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <input type="submit" class="btn btn-primary btn-block btn-flat" name="submit_super_admin" value="Sign In" />
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
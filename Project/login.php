<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once( "inc/config.php");

$user = null;
if(isset($_SESSION['auth_user']))  {

    $user = DB::getRow("SELECT *FROM  users WHERE id='".$_SESSION["auth_user_id"]."'");
    header(BASE_URL);

}

if (empty($_SESSION['token'])) {
    if (function_exists('mcrypt_create_iv')) {
        $_SESSION['token'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
    } else {
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}
$token = $_SESSION['token'];

if($_SERVER["REQUEST_METHOD"] == "POST") {

        //print_r($_POST);

        $escapedUserName = clean_string($_POST['email']);
        $escapedPW = clean_string($_POST['password']);
        //echo "select * from users where email = '" . $escapedUserName . "' OR username = '" . $escapedUserName . "'";

        $row = DB::getRow("select * from users where email = '" . $escapedUserName . "' OR username = '" . $escapedUserName . "'");

        $salt = $row->salt;
        $saltedPW = $escapedPW . $salt;
        $hashedPW = hash('sha256', $saltedPW);

        $row = DB::getRow("SELECT * FROM users WHERE email = '" . $escapedUserName . "' AND password = '" . $hashedPW . "' AND is_active=1");//sadece admin yetkisi oan kişier girebilir

        // echo "SELECT COUNT(*) FROM users WHERE username = '" . $escapedUserName . "' AND password = '" . $hashedPW . "' AND is_active=1";
        if (DB::getVar("SELECT COUNT(*) FROM users WHERE email = '" . $escapedUserName . "' AND password = '" . $hashedPW . "' AND is_active=1") == 0) {

            $_SESSION["status"] = "error";
            $_SESSION["message"] = "Kullanıcı adı ve ya şifre yanlış.";


        } else {

            $_SESSION['auth_user'] = $row->username;
            $_SESSION["auth_user_id"] = $row->id;
            $_SESSION["status"] = "success";
            $_SESSION["message"] = "Başarılı bir şekilde giriş yaptınız!";

            $date = new DateTime("NOW");
            DB::getRow("UPDATE users SET last_login = '{$date->format('Y-m-d H:i:s')}' WHERE username ='" . $row->username . "'");


            header("location:".BASE_URL);

        }

}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>ART+ | Log in</title>
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
<div class="login-box">
    <?php include "status.php"?>
  <div class="login-logo">
    <a href="<?=BASE_URL."/login"?>"><b>ART</b>+</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Sign in to start your session</p>

    <form  method="post">
      <div class="form-group has-feedback">
        <input type="email" class="form-control" name="email" placeholder="Email">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" name="password" placeholder="Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-md-12 col-xs-12">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    </form>


    <a href="<?=BASE_URL."/forget-password"?>">I forgot my password</a><br>
    <a href="<?=BASE_URL."/register"?>" class="text-center">Register a new membership</a>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

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
      increaseArea: '20%' /* optional */
    });
  });
</script>
<script>
    $(".toggle-password").click(function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });//end of toogle-password
</script>
</body>
</html>

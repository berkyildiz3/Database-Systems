<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once( "inc/config.php");


if (empty($_SESSION['token'])) {
    if (function_exists('mcrypt_create_iv')) {
        $_SESSION['token'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
    } else {
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}
$token = $_SESSION['token'];

print_r($_SESSION);
if(isset($_SESSION['auth_user']))  {

    $user = DB::getRow("SELECT *FROM  users WHERE id='".$_SESSION["auth_user_id"]."'");
    header('Location: '.BASE_URL);

}

if ($_SERVER['REQUEST_METHOD'] === 'POST' and clean_string($_POST['token']) == $_SESSION["token"] )
{
    try{

        //print_r($_POST);
        if(!isset($_POST['terms']) || (isset($_POST['terms']) && clean_string($_POST['terms']) != 'on'))
            throw new Exception('Please read and confirm terms and conditions!.');




        $firstname =clean_string($_POST['firstname']);
        $lastname  =clean_string($_POST['lastname']);
        $gender  =clean_string($_POST['gender']);
        $escapedEmail  = clean_string($_POST['email']);
        $escapedUserName =  (explode("@", $escapedEmail))[0];
        $escapedPW = clean_string($_POST['password']);
        $escapedRePW = clean_string($_POST['repassword']);
        $any_user = DB::getVar("SELECT COUNT(*) FROM users WHERE username='$escapedUserName' OR  email='$escapedUserName'");
        //echo $any_user;
        if($any_user != 0)
            throw new Exception("This email address already is registered by someone else.<br>".
                                         "If its you, you can <a href='".BASE_URL."/login'>login with this link</a>!.");

        if($escapedPW != $escapedRePW)
            throw new Exception("Password and re-password must be same.<br>");

        # generate a random salt to use for this account
        $cstrong = true;
        $salt = bin2hex(openssl_random_pseudo_bytes(32, $cstrong));
        $saltedPW = $escapedPW.$salt;
        $hashedPW = hash('sha256', $saltedPW);
        $request_ip = get_client_ip_env();




        $last_id = DB::insert("INSERT INTO users (firstname,lastname,username,email, password, salt,authority,gender) values ('$firstname','$lastname','$escapedUserName','$escapedEmail', '$hashedPW', '$salt','member','$gender')");
        //echo "Last-->ID".$last_id;

        $activation_token = sha1(uniqid($last_id, true));
        //echo "Activation Token =".$activation_token;
        $server_time =$_SERVER['REQUEST_TIME'];

        DB::insert("INSERT INTO timeline(user_id,title,text,ip) 
                                          VALUES ('$last_id','Signup','Your membership started','$request_ip')");

        DB::exec("INSERT INTO pending_users (user_id, token, tstamp) VALUES ('$last_id','$activation_token','$server_time')");

        $url = BASE_URL."/activate/$activation_token";


        //change settings here
        $your_email = "noreply@berkart.com";
        $your_smtp = "smtp.sendgrid.net";
        $your_smtp_user = "tekintopuz";
        $your_smtp_pass = "1395581Aa*";
        $your_website = "http://berkart.com";


        $mail = new PHPmailer();
        $mail->From = $your_email;
        $mail->FromName = $your_website;
        $mail->Host = $your_smtp;
        $mail->Mailer   = "smtp";
        $mail->Password = $your_smtp_pass;
        $mail->Username = $your_smtp_user;
        $mail->Subject = "$your_website feedback";
        $mail->SMTPAuth  =  "true";
        $mail->Port = 587;
        $mail->Protokol = "mail";
        $mail->CharSet = "UTF-8";


        //Recipients
        $mail->addAddress($escapedEmail);
        $mail->addBCC('berk.gurlek@icloud.com');
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Email Verification';
        $mail->Body    =  "Hello from ART+.<br>".
                          "To verify your email address and ART+ account, click the link below".
            "<a href='$url'>ACTIVATION</a>";



        $_SESSION["status"] = "success";
        $_SESSION["message"] = "Registration is successfull! Please check your email address".
                               " for confirmation email address and activate your ART+ account";

        $mail->send();
    }catch(Exception $e){
        $_SESSION["status"] = "error";
        $_SESSION["message"] = "Something goes wrong...".$e->getMessage(); //;



    }


}



?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>ART+ | Registration Page</title>
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
    <style>
        .field-icon {
            float: right;
            margin-left: -25px;
            margin-top: -30px;
            position: relative;
            z-index: 2;
            color: #37c6f5;
            font-size: x-large;
        }
        .login-box, .register-box {
            width: 450px;
            margin: 7% auto;
        }


    </style>
</head>
<body class="hold-transition register-page">
<div class="register-box">
    <?php
        include "status.php";
    ?>
  <div class="register-logo">
    <a href="<?=BASE_URL?>"><b>ART</b>+</a>
  </div>

  <div class="register-box-body">
    <p class="login-box-msg">Register a new membership</p>

    <form  method="post">
        <input type="text" name="token" value="<?=$token?>" hidden>
      <div class="form-group has-feedback">
        <input type="text" class="form-control" name="firstname" placeholder="First Name">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
        <div class="form-group has-feedback">
            <input type="text" class="form-control" name="lastname" placeholder="Last Name">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
      <div class="form-group has-feedback">
        <input type="email" class="form-control" name="email" placeholder="Email">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
        <div class="form-group has-feedback">
            <select  class="form-control" name="gender">
                <option value="">Your Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
            <i class="fa fa-venus-mars form-control-feedback" style="margin-right: 20px"></i>
        </div>
      <div class="form-group has-feedback">
        <input type="password" id="password" name="password" class="form-control" placeholder="Password">
        <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>


      </div>
      <div class="form-group has-feedback">
        <input type="password" id="repassword" name="repassword"  class="form-control" placeholder="Retype password">
        <span toggle="#repassword" class="fa fa-fw fa-eye field-icon toggle-password"></span>

      </div>
      <div class="row">
        <div class="col-md-12 col-xs-12">
          <div class="checkbox icheck">
            <label>
              <input name="terms" type="checkbox"> I agree to the <a href="<?=BASE_URL?>/terms">terms</a>
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-md-12 col-xs-12">
          <button type="submit" class="btn btn-primary btn-block btn-lg btn-flat">Register</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
    <br>
    <a href="<?=BASE_URL?>/login" class="text-center" style="font-size:larger;line-height: 16px ">I already have a membership</a>
  </div>
  <!-- /.form-box -->
</div>
<!-- /.register-box -->

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

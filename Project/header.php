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

if(isset($_SESSION['auth_user']))  {

    $user = DB::getRow("SELECT *FROM  users WHERE id='".$_SESSION["auth_user_id"]."'");

}
?>



<header class="main-header">
    <!-- Logo -->
    <a href="<?=BASE_URL?>" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>ART</b>+</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>ART</b>+</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <?php
                if(isset($_SESSION["auth_user"]))  {?>
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?php
                        if ($user && $user->avatar) {
                        ?>
                        <img src="<?=BASE_URL."/".$user->avatar?>" class="user-image" alt="User Image">
                        <?php
                        } else {
                        ?>
                        <img src="<?=BASE_URL."/images/no-image.png"?>"  class="user-image" alt="User Image">
                        <?php
                        }?>
                        <span class="hidden-xs"><?= $user->firstname . " " . $user->lastname ?></span>
                    </a>
                    <ul class="dropdown-menu">

                    <!-- User image -->
                    <li class="user-header">
                        <?php
                        if ($user && $user->avatar) {
                            ?>
                            <img src="<?=BASE_URL."/".$user->avatar?>" class="img-circle" alt="User Image">
                            <?php
                        } else {
                            ?>
                            <img src="<?=BASE_URL."/images/no-image.png"?>" class="img-circle" alt="User Image">
                            <?php
                        }
                        ?>

                        <p>
                            <?= $user->firstname . " " . $user->lastname ?>
                            <small><?= $user->username ?></small>
                            <?php
                                if($user->authority == "admin")
                                    echo '<small>Admin since '.$user->register_date.'</small>';
                                else  if($user->authority == "member")
                                    echo '<small>Member since '.$user->register_date.'</small>';
                                else  if($user->authority == "moderator")
                                    echo '<small>Moderator since '.$user->register_date.'</small>';
                                else  if($user->authority == "artist")
                                    echo '<small>Artist since '.$user->register_date.'</small>';
                            ?>

                        </p>
                    </li>
                    <!-- Menu Body -->
                    <li class="user-body">
                        <div class="row">
                            <div class="col-xs-4 text-center">
                                <a href="<?= BASE_URL . "/followers" ?>">Followers</a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="<?= BASE_URL . "/arts" ?>">My-Arts</a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="<?= BASE_URL . "/friends" ?>">Friends</a>
                            </div>
                        </div>
                        <!-- /.row -->
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <div class="pull-left">
                            <a href="<?= BASE_URL . "/profile" ?>" class="btn btn-default btn-flat">Profile</a>
                        </div>
                        <div class="pull-right">
                            <a href="<?= BASE_URL . "/logout" ?>" class="btn btn-default btn-flat">Logout</a>
                        </div>
                    </li>
                    <?php
                }else{

                    ?>
                    <li>
                        <a style="padding: 5px 25px 5px 25px;margin:8px 10px 3px 5px;background-color: transparent;color: white;border: 1px solid white" href="<?= BASE_URL . "/login" ?>" class="btn btn-default btn-flat">Login</a></li>
                    <li>
                        <a style="padding: 5px 25px 5px 25px;margin:8px 10px 3px 5px;background-color: transparent;color: white;border: 1px solid white" href="<?= BASE_URL . "/register" ?>" class="btn btn-default btn-flat">Register</a></li>

                <?php
                }?>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
<?php
/**
 * Created by PhpStorm.
 * User: tekin
 * Date: 18.4.2019
 * Time: 22:02
 */

include_once( "inc/config.php");
session_start();
unset($_SESSION["auth_user"]);
unset($_SESSION["auth_user_id"]);
header("Location:".BASE_URL);

?>
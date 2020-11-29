<?php


ob_start();



if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include( "inc/config.php");

try{

    // 1 day measured in seconds = 60 seconds * 60 minutes * 24 hours
    $delta = 86400;

    // retrieve token
    // Check to see if link has expired
    $date = new DateTime();
    $tstamp = $date->getTimestamp();
    if ($_SERVER["REQUEST_TIME"] - $tstamp > $delta) {
        throw new Exception("Token has expired.");
    }

    if (isset($_GET["activation_token"]) && preg_match('/^[0-9A-Z]{40}$/i', $_GET["activation_token"])) {
        $activation_token = $_GET["activation_token"];
    }
    else {
        throw new Exception("Valid token not provided.");
    }
    //print($activation_token);



    // verify token
    $query = "SELECT * FROM pending_users WHERE token = '$activation_token'";
    $row = DB::getRow("SELECT user_id, tstamp FROM pending_users WHERE token = '$activation_token'");
//    print($query);
//    print("SELECT user_id, tstamp FROM pending_users WHERE token = '$activation_token'");
//    print_r($row);

    if ($row) {
        $query = "UPDATE users SET is_active=1, is_email_activated=1 WHERE id='$row->user_id'";
        DB::exec($query );
      //  print($query);
        // delete token so it can't be used again
        $query = "DELETE FROM pending_users WHERE token = '$activation_token' ";
        //print($query);
        DB::exec($query );


        //print($query);
        DB::exec($query);

        $_SESSION["status"] = "success";
        $_SESSION["message"] = "Congratulation.Activation is successful.";

    }
    else {
        throw new Exception("Valid token not provided.");
    }


}catch (Exception $e) {


    $_SESSION["status"] = "error";
    $_SESSION["message"] = "This link is broken or is consumed in time!Please contact administration.";


}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "head.php"?>
    <title>ART+ | Account Activation</title>
</head>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <?php include "header.php"?>
        <?php include "main_left_sidebar.php"?>
    <!-- end header div -->

    <!-- start wrap div -->
        <div class="content-wrapper">

            <?php
                include "status.php";

//                print_r($_SESSION);
            ?>

        </div>
        <?php
        include "footer.php";
        ?>
    </div>
    <?php
    include "js.php";
    ?>
</body>
</html>

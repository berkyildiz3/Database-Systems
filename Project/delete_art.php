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

if(!isset($_SESSION['auth_user']))  {


    $_SESSION["status"] = "error";
    $_SESSION["message"] = "You have to login to delete item!";
   header("Location:".BASE_URL."/login");

}else {


    $user = DB::getRow("SELECT *FROM  users WHERE id='" . $_SESSION["auth_user_id"] . "'");

    try {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' &&  ($user->authority =='admin' || $user->authority =='artist' ||
                $user->authority =='moderator'  ) ) {




            $art_id = clean_string($_GET['art_id']);
            $client_ip = get_client_ip_env();


            DB::exec("DELETE FROM art WHERE id='$art_id'");

            //echo "DELETE FROM art WHERE id='$art_id'";
            $_SESSION["query"] = "DELETE FROM art WHERE id='$art_id'";
            $_SESSION["status"] = "success";
            $_SESSION["message"] = "Art is deleted from table art with id $id";
            DB::insert("INSERT INTO timeline(user_id,title,text,ip)
                                          VALUES ('$user->id','Delete','Item is deleted from table art with id $id by $user->username','$client_ip')");

            print_r($_SESSION);
           header("Location:".BASE_URL."/view-all-arts");
        }
        else{
            $_SESSION["status"] = "error";
            $_SESSION["message"] = "Something goes wrong...Please be sure you are admin and send suitable request method." ; //;
           header("Location:".BASE_URL."/view-all-arts");
        }
    } catch (Exception $e) {
        $_SESSION["status"] = "error";
        $_SESSION["message"] = "Something goes wrong..." . $e->getMessage(); //;
       header("Location:".BASE_URL."/view-all-arts");
    }}

header("Location:".BASE_URL."/view-all-arts");
?>
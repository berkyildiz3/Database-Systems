<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once( "inc/config.php");

$response = array();
if (empty($_SESSION['token'])) {
    if (function_exists('mcrypt_create_iv')) {
        $_SESSION['token'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
    } else {
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}
$token = $_SESSION['token'];

if(!isset($_SESSION['auth_user']))  {


    $response["status"] = "error";
    $response["message"] = "You have to login to delete item!";

}else {


    $user = DB::getRow("SELECT *FROM  users WHERE id='" . $_SESSION["auth_user_id"] . "'");

    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  $user->authority =='admin' && $_POST['user_id'] == $_SESSION['auth_user_id']) {




            $id = clean_string($_POST['id']);
            $table = clean_string($_POST['table']);
            $client_ip = get_client_ip_env();


            DB::exec("DELETE FROM $table WHERE id='$id'");

            $response["query"] = "DELETE FROM $table WHERE id='$id'";
            $response["status"] = "success";
            $response["message"] = "Item is deleted from table $table with id $id";
            DB::insert("INSERT INTO timeline(user_id,title,text,ip) 
                                          VALUES ('$user->id','Delete','Item is deleted from table $table with id $id by $user->username','$client_ip')");



        }
        else{
            $response["status"] = "error";
            $response["message"] = "Something goes wrong...Please be sure you are admin and send suitable request method." ; //;
        }
    } catch (Exception $e) {
        $response["status"] = "error";
        $response["message"] = "Something goes wrong..." . $e->getMessage(); //;
    }

}
echo json_encode($response)
?>
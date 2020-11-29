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
    $response["message"] = "You have to login to make comment, like or dislike on an art!";

}else {


    $user = DB::getRow("SELECT *FROM  users WHERE id='" . $_SESSION["auth_user_id"] . "'");

    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {


            $mytype = clean_string($_POST['mytype']);
            $art_id = clean_string($_POST['id']);
            $art = DB::getRow("SELECT *FROM art WHERE id='$art_id'");
            $client_ip = get_client_ip_env();
            $response["POST"] = $_POST;

            if ($mytype == "liked" && DB::getVar("SELECT COUNT(*) FROM liked WHERE art_id='$art_id' AND liker='$user->id'") == 0) {
                $response["query"] = "INSERT INTO liked (art_id,liker,ip) VALUES('$art_id','$user->id','$client_ip')";
                $last_id = DB::insert("INSERT INTO liked (art_id,liker,ip) VALUES('$art_id','$user->id','$client_ip')");

                $response["status"] = "success";
                $response["message"] = "Like is successfull";

                DB::insert("INSERT INTO timeline(user_id,title,text,ip) 
                                          VALUES ('$user->id','Like','Like is successfull on art with id $art_id','$client_ip')");

            } else if ($mytype == "disliked" && DB::getVar("SELECT COUNT(*) FROM disliked WHERE art_id='$art_id' AND disliker='$user->id'") == 0) {
                $response["query"] = "INSERT INTO disliked (art_id,disliker,ip) VALUES('$art_id','$user->id','$client_ip')";
                $response["query2"] = "INSERT INTO timeline(user_id,title,text,ip) 
                                          VALUES ('$user->id','Dislike','Dislike is successfull on art with id '.$art_id,'$client_ip')";
                $response["count"] = DB::getVar("SELECT COUNT(*) FROM disliked WHERE art_id='$art_id' AND liker='$user->id'");
                $last_id = DB::insert("INSERT INTO disliked (art_id,disliker,ip) VALUES('$art_id','$user->id','$client_ip')");

                $response["status"] = "success";
                $response["message"] = "Dislike is successfull";
                DB::insert("INSERT INTO timeline(user_id,title,text,ip) 
                                          VALUES ('$user->id','Dislike','Dislike is successfull on art with id $art_id','$client_ip')");
            } else if ($mytype == "liked" && DB::getVar("SELECT COUNT(*) FROM liked WHERE art_id='$art_id' AND liker='$user->id'") > 0) {
                $response["query"] = "DELETE FROM liked WHERE art_id='$art_id' AND liker ='$user->id'";
                $last_id = DB::exec("DELETE FROM liked WHERE art_id='$art_id'AND liker ='$user->id'");

                $response["status"] = "success";
                $response["message"] = "You give up liking";
                DB::insert("INSERT INTO timeline(user_id,title,text,ip) 
                                          VALUES ('$user->id','Un-Like','Un-Like is successfull on art with id $art_id'','$client_ip')");


            } else if ($mytype == "disliked" && DB::getVar("SELECT COUNT(*) FROM disliked WHERE art_id='$art_id' AND disliker='$user->id'") > 0) {
                $response["query"] = "DELETE FROM disliked WHERE art_id='$art_id' AND disliker ='$user->id'";
                $response["query2"] = "INSERT INTO timeline(user_id,title,text,ip) 
                                          VALUES ('$user->id','Dislike','Dislike is successfull on art with id $art_id','$client_ip')";
                $last_id = DB::exec("DELETE FROM disliked WHERE art_id='$art_id'AND disliker ='$user->id'");

                $response["status"] = "success";
                $response["message"] = "You give up disliking";
                DB::insert("INSERT INTO timeline(user_id,title,text,ip) 
                                          VALUES ('$user->id','Un-Dislike','Un-Dislike is successfull on art with id $art_id ','$client_ip')");

            }
            else if ($mytype == "share" && $art->share == 0) {
                $response["query"] = "UPDATE art SET share = 1 WHERE id='$art_id' AND user_id='$user->id'";
                DB::exec("UPDATE art SET share = 1 WHERE id='$art_id' AND user_id='$user->id'");

                $response["status"] = "success";
                $response["message"] = "You share your art";
                DB::insert("INSERT INTO timeline(user_id,title,text,ip) 
                                          VALUES ('$user->id','Share','$user->username share its own art with id $art_id ','$client_ip')");

            }
            else if ($mytype == "share" && $art->share == 1) {
                $response["query"] = "UPDATE art SET share = 0 WHERE id='$art_id' AND user_id='$user->id'";
                DB::exec("UPDATE art SET share = 0 WHERE id='$art_id' AND user_id='$user->id'");

                $response["status"] = "success";
                $response["message"] = "You un-share your art";
                DB::insert("INSERT INTO timeline(user_id,title,text,ip) 
                                          VALUES ('$user->id','Un-Share','$user->username un-share its own art with id $art_id ','$client_ip')");

            }
            else {
                $response["status"] = "error";
                $response["message"] = "Do Nothing";

            }

        }
    } catch (Exception $e) {
        $response["status"] = "error";
        $response["message"] = "Something goes wrong..." . $e->getMessage(); //;
    }

}
    echo json_encode($response)
?>
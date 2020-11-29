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

if(!isset($_GET["art_id"]))
{
    header('Location: '.BASE_URL);
}
$token = $_SESSION['token'];
$_SESSION['menu_open'] = '';
$_SESSION['active'] = '#art_detail_li';





if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['auth_user']) )
{
    try{

        //print_r($_POST);
        $art = DB::getRow("SELECT *FROM art WHERE id='".$_GET["art_id"]."' ");

        $message =clean_string($_POST['message']);
        $target_art_id =clean_string($_POST['art_id']);
        $client_ip =get_client_ip_env();
        $user_id = $_SESSION['auth_user_id'];




        //echo "INSERT INTO comment(created_by,art_id,ip,message) VALUES('$user_id','$target_art_id','$client_ip','$message')";
        $response = DB::insert("INSERT INTO comment(created_by,art_id,ip,message) VALUES('$user_id','$target_art_id','$client_ip','$message')");




        $_SESSION["status"] = "success";
        $_SESSION["message"] = "Comment is created successfully"; //;

    }catch(Exception $e){
        $_SESSION["status"] = "error";
        $_SESSION["message"] = "Something goes wrong...".$e->getMessage(); //;



    }


}
$art = DB::getRow("SELECT *FROM art WHERE id='".$_GET["art_id"]."' ");
$owner = DB::getRow("SELECT *FROM users WHERE id='$art->user_id'");
?>

<!DOCTYPE html>
<html>
<head>
    <?php include "head.php"?>

    <style>


        .myinfo {
            display: none;
        }

        .myactive_art_info {
            display: block!important;
            z-index: 100;
            position: absolute;
            bottom: 10px;
            width: 250px;
            background-color: #f9f2f4;
        }
        .user-image {
            float: left;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            margin-right: 10px;
            margin-top: -2px;
        }
        .item{

            padding: 10px;
            border:1px solid #3c8dbc;
        }
    </style>

</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <?php include "header.php"?>
    <?php include "main_left_sidebar.php"?>

    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <?php include "status.php"?>
            <div class="box box-success" style="cursor: move;min-height: 600px">
                <div class="box-header ui-sortable-handle" style="cursor: move;">
                    <i class="fa fa-comments-o"></i>

                    <h3 class="box-title">Art Detail <span style="color: red"> <?=$art->title."</span> by ".$owner->username?></h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <?php
                        if(startsWith($art->mime,'image')) {
                            echo '<div class="col-md-12" >
                         <div style="border: 1px solid white;text-align:center;margin: 5px;padding: 10px;" class="my_art">
                           <a href="'.BASE_URL.'/art-detail/'.$art->id.'"><img class="image" src="' . BASE_URL."/".$art->url . '" style="width:100%;padding-bottom: 10px;"></a>
                            <br>
                             <button class="btn btn-primary" style="float:right;margin: 3px"> <i class="fa fa-thumbs-o-up" data-type="liked" data-id="'.$art->id.'"></i>'.$art->liked.'</span> </button></a>
                         
                            <button class="btn btn-danger" style="float:right;margin: 3px"><i class="fa fa-thumbs-o-down" data-type="disliked"  data-id="'.$art->id.'"></i>'.$art->disliked.'</span></button></a>
                            <button class="btn btn-warning" style="float:right;margin: 3px">
                                                            <i class="fa fa-comments bg-yellow"></i>'.$art->comment.'</span></button>
    
    
                    </div>';
                        }
                        else if(startsWith($art->mime,'video')) {
                            echo '<div class="col-md-12">
                        <div style="border: 1px solid white;text-align:center;margin: 5px;padding: 10px;min-height: 300px;max-height: 300px" class="my_art">
                            <video  style="max-height:400px" controls>
                                <source src="'.BASE_URL."/".$art->url.'" type="'.$art->mime.'">
                                Your browser does not support HTML5 video.
                            </video>
                            
                            <br>
                            <button class="btn btn-primary" style="float:right;margin: 3px"> <i class="fa fa-thumbs-o-up" data-type="liked" data-id="'.$art->id.'"></i>'.$art->liked.'</span> </button></a>
                         
                            <button class="btn btn-danger" style="float:right;margin: 3px"><i class="fa fa-thumbs-o-down" data-type="disliked"  data-id="'.$art->id.'"></i>'.$art->disliked.'</span></button></a>
                            <button class="btn btn-warning" style="float:right;margin: 3px">
                                                            <i class="fa fa-comments bg-yellow"></i>'.$art->comment.'</span></button>
    
                        </div>
                    </div>';


                        }
                        ?>
                    </div>




                    <div class="row" style="margin-top: 10px">
                        <div class="col-md-3"  style="padding-left: 20px">

                            <table class="table table-bordered" >
                                <tr>
                                    <td>
                                        Art Title
                                    </td>
                                    <td>
                                        <?=$art->title?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Created By
                                    </td>
                                    <td>
                                        <?=$owner->username?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Created At
                                    </td>
                                    <td>
                                        <?=$art->created_at?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Size
                                    </td>
                                    <td>
                                        <?=$art->size?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Liked
                                    </td>
                                    <td>
                                        <?=$art->liked?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Disliked
                                    </td>
                                    <td>
                                        <?=$art->disliked?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Comment
                                    </td>
                                    <td>
                                        <?=$art->comment?>
                                    </td>
                                </tr>
                            </table>

                        </div>
                        <div class="col-md-9">
                            <?php
                            if(isset($_SESSION['auth_user'])) {
                                ?>
                                <div class="col-md-10" style="margin-bottom: 3px">
                                    <form method="post">
                                        <div class="input-group">
                                            <input name="art_id" value="<?= $art->id ?>" hidden>
                                            <input class="form-control" name="message" placeholder="Type message...">

                                            <div class="input-group-btn">
                                                <button type="submit" class="btn btn-success"><i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-2" style="margin-bottom: 3px"></div>
                                <?php
                            }
                    $all_comments = DB::get("SELECT *FROM comment WHERE art_id='$art->id' ORDER BY created_at ASC");
                    foreach($all_comments as $comment){

                        $created_by =   DB::getRow("SELECT *FROM users WHERE id='$comment->created_by'");

                        echo '<div class="row" style="margin:2px 0px 2px 0px"><div class="col-md-10"><div class="item">';
                        if($created_by->avatar)
                        {
                            echo '<img  class="user-image" src="'.BASE_URL.'/'.$created_by->avatar.'" alt="user image" >';
                        }
                        else
                        {
                            echo '<img class="user-image"  src="'.BASE_URL.'/images/no-image.png" alt="user image" >';

                        }

                        echo '<p class="message" >
                                        <a href="'.BASE_URL.'/member-profile/'.$created_by->username.'" class="name">
                                        <small class="text-muted pull-right"><i class="fa fa-clock-o"></i>'.$comment->created_at.'</small>
                                        '.$created_by->username.'
                                    </a></p><p style="margin-left: 50px">
                                    '.$comment->message.'
                                </p>                                
                            </div></div><div class="col-md-2"></div></div>';
                    }
                    ?>
                        </div></div>
                </div>
            </div>
        </section>
    </div>
    <?php
    include "footer.php";
    ?>
</div>
<?php
include "js.php";


?>
<script>



    $(".my_art").hover(function(){

            $(this).find(".art").addClass("myactive_art_info");


        }, function () {

            $(this).find(".art").removeClass("myactive_art_info");
        }

    );
    $(".image").hover(function(){

            $(this).parent().find(".art").addClass("myactive_art_info");


        }, function () {

            $(this).parent().find(".art").removeClass("myactive_art_info");
        }

    );



    $("body").on("click",".fa-thumbs-o-up,.fa-thumbs-o-down",function (e) {

        var mytype = $(this).data("type");
        var id = $(this).data("id");

        e.preventDefault();
        $.ajax({
            url : "/berk/like-dislike-art", // the endpoint
            type :"POST", // http method
            data :{

                "mytype":mytype,
                "id":id

            } ,

            // handle a successful response
            success : function(response) {
                console.log(response);
                response = JSON.parse(response);
                console.log(response);
                if(response.status == 'success') {


                    Swal.fire(
                        {
                            text: response.message,
                            type: 'success',
                            width: 200,


                        }).then(function () {
                        location.reload();
                    });
                }
                else  if(response.status == 'error'){
                    Swal.fire(
                        {
                            text:response.message,
                            type:'error',
                            width: 200,
                        });
                }

            },
            // handle a non-successful response
            error : function(response) {
                console.log(response);
            }
        });


    });


</script>

</body>
</html>



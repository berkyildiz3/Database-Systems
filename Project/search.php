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
if(!(isset($_GET['keyword'])))  {

    header("location:".BASE_URL);

}
$keyword = $_GET['keyword'];

$token = $_SESSION['token'];
$_SESSION['menu_open'] = '';
$_SESSION['active'] = '#index_li';
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
            width: 320px;
            background-color: #f9f2f4;
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
            <?php
            include "status.php";
            ?>

            <div class="row">
                <?php

//                echo "SELECT a.id,a.liked,a.disliked,a.comment,a.title,a.user_id,a.url,a.mime,a.size FROM art as a,
//                                      category_art_relation as b,
//                                      category c,users d
//
//                                      WHERE b.art_id=a.id AND b.category_id=c.id AND d.id = a.user_id AND (a.title LIKE '%$keyword%'
//                                      OR c.name LIKE '%$keyword%'
//                                      OR a.title LIKE '%$keyword%'
//                                      OR d.username LIKE '%$keyword%' )";
                $all_arts = DB::get("SELECT a.id,a.liked,a.disliked,a.comment,a.title,a.user_id,a.url,a.mime,a.size FROM art as a, 
                                      category_art_relation as b,
                                      category c,users d
                                      
                                      WHERE a.share=1 AND b.art_id=a.id AND b.category_id=c.id AND d.id = a.user_id AND (a.title LIKE '%$keyword%' 
                                      OR c.name LIKE '%$keyword%' 
                                      OR a.title LIKE '%$keyword%'
                                      OR d.username LIKE '%$keyword%' )");

                foreach($all_arts as $art) {

                    $owner = DB::getRow("SELECT *FROM users WHERE id='$art->user_id'");


                    if(startsWith($art->mime,'image')) {
                        echo '<div class="col-md-4 my_art" style=";max-heght:400px;min-height: 400px;margin-bottom: 5px">
                                        <div class="my_art" style="padding: 10px;border: 1px solid white;max-heght:400px;min-height: 400px;">
                                            <a href="'.BASE_URL.'/art-detail/'.BASE_URL.'/'.$art->id.'"><img class="image" src="' . $art->url . '" style="width:100%;max-height:350px;padding-bottom: 10px;"></a>
                                           
                                            <div class="art" style="display:none">
                                           
                                                <div class="row">
                                                    <div class="col-md-12 col-xs-12 col-sm-12 ">
                                                        <span style="float: left;font-size:x-large;color:black;">'.$art->title.'</span>
                                                    </div>
                                                    <div class="col-md-6 col-xs-6 col-sm-6 " >
                                                        <span style="float: left;font-size:x-large;color:black;">'.$owner->username.'
                                                         <i class="fa fa-user"></i></span>
                                                    </div>
                                                    <div class="col-md-6 col-xs-6 col-sm-6">
                                                        <span style="color:#f39c12;font-size:x-large;">
                                                        <i class="fa fa-comments bg-yellow"></i>'.$art->comment.'</span>
                                                        <span style="color:red;margin-right: 5px;font-size:x-large;">
                                                        <i class="fa fa-thumbs-o-down"  data-type="disliked" data-id="'.$art->id.'"></i>'.$art->disliked.'</span>
                                                        <span style="color:green;font-size:x-large;">
                                                        <i class="fa fa-thumbs-o-up" data-type="liked"  data-id="'.$art->id.'"></i>'.$art->liked.'</span>
                                                        
                                                    </div>
                                                </div>                                         
                                             </div>   
                                            
                                        </div>
                                    </div>';
                    }
                    else if(startsWith($art->mime,'video')) {
                        echo '<div class="col-md-4">
                                        <div style="border: 1px solid white;padding: 10px;min-height: 400px;max-height: 400px" class="my_art">
                                           <video  width="100%" controls>
                                                <source src="'.BASE_URL.'/'.$art->url.'" type="'.$art->mime.'">
                                                Your browser does not support HTML5 video.
                                            </video>
                                             <a href="'.BASE_URL.'/art-detail/'.$art->id.'"><button class="btn btn-primary">Detail</button></a>
                                             <div class="art" style="display:none">
                                               
                                                <div class="row">
                                                    <div class="col-md-12 col-xs-12 col-sm-12 ">
                                                        <span style="float: left;font-size:x-large;color:black;">'.$art->title.'</span>
                                                    </div>
                                                    <div class="col-md-6 col-xs-6 col-sm-6 ">
                                                        <span style="float: left;font-size:x-large;color:black;">'.$owner->username.' <i class="fa fa-user"></i></span>
                                                    </div>
                                                    <div class="col-md-6 col-xs-6 col-sm-6">
                                                         <span style="color:#f39c12;font-size:x-large;">
                                                        <i class="fa fa-comments bg-yellow"></i>'.$art->comment.'</span>
                                                        <span style="color:red;margin-right: 5px;font-size:x-large;">
                                                        <i class="fa fa-thumbs-o-down"  data-type="disliked" data-id="'.$art->id.'"></i>'.$art->disliked.'</span>
                                                        <span style="color:green;font-size:x-large;">
                                                        <i class="fa fa-thumbs-o-up" data-type="liked"  data-id="'.$art->id.'"></i>'.$art->liked.'</span>
                                                    </div>
                                                </div>                                         
                                             </div>   
                                            
                                        </div>
                                    </div>';
                    }

                }
                ?>
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

    <?php
    if(isset($_SESSION["auth_user"]))
    {

    ?>
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
    <?php
    }?>

</script>
</body>
</html>

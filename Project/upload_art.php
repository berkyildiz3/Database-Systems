<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once( "inc/config.php");



if(!(isset($_SESSION['auth_user'])))  {

    header("location:".BASE_URL."/login");

}

$user = DB::getRow("SELECT *FROM  users WHERE id='".$_SESSION["auth_user_id"]."'");
if($user && $user->authority == 'member')
{
    $_SESSION["status"] = "error";
    $_SESSION["message"] = "To create an art, your authority should be at least artist. Please contct system administrator to be an artist."; //;



}


if (empty($_SESSION['token'])) {
    if (function_exists('mcrypt_create_iv')) {
        $_SESSION['token'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
    } else {
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}
$token = $_SESSION['token'];

$_SESSION['menu_open'] = '';
$_SESSION['active'] = '#upload_art_li';

?>
<?php

try{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($user->authority == 'admin' || $user->authority == 'moderator'||  $user->authority == 'artist'))
    {

        $my_art_id = (int) $_POST['art_id'];
        $my_art = DB::getRow("SELECT *FROM art WHERE id ='$my_art_id'");
        $title =clean_string($_POST['title']);
        $mytags =clean_string($_POST['mytags']);
        $share = 0;

        if(isset($_POST['share']) && isset($_POST['share'])== 'on' )
            $share = 1;

        if(isset($_POST['mycategory']))
            $mycategories =$_POST['mycategory'];
        else
            $mycategories = null;
        $client_ip =get_client_ip_env();

        if($user->authority != 'artist' && $user->authority != 'admin'  && $user->id != $my_art->user_id)
            throw new Exception('No Authority');
        if($my_art_id > 0 )
        {

            //echo "UPDATE  art  SET title ='$title',user_id='$user->id',tags='$mytags'  WHERE id='$my_art_id'";
             DB::exec("UPDATE  art SET title ='$title',user_id='$user->id',tags='$mytags',share='$share' WHERE id='$my_art_id'");
            $last_id = $my_art_id;
            DB::insert("INSERT INTO timeline(user_id,title,text,ip) 
                                          VALUES ('$last_id','Edited Art','Art with id=$my_art_id , edited created by $user->username','$client_ip')");
            DB::insert("DELETE FROM category_art_relation WHERE art_id='$my_art_id'");

            $_SESSION["status"] = "success";
            $_SESSION["message"] = "Art is edited successfully";
        }
            else{

                //echo "INSERT INTO art (title,user_id,tags,created_ip) VALUES('$title','$user->id','$mytags','$client_ip')";
                $last_id = DB::insert("INSERT INTO art (title,user_id,tags,share,created_ip) VALUES('$title','$user->id','$mytags','$share','$client_ip')");


                DB::insert("INSERT INTO timeline(user_id,title,text,ip) 
                                          VALUES ('$last_id','New Art Created','New art created by $user->username','$client_ip')");


                $_SESSION["status"] = "success";
                $_SESSION["message"] = "New art is created successfully";







            }









            if($mycategories) {
                foreach ($mycategories as $mycategory) {
                    $mycategory_id = (int)$mycategory;
                    DB::insert("INSERT INTO category_art_relation (category_id,art_id) VALUES('$mycategory_id','$last_id')");
                }
            }


        $uploaddir = 'images/arts/';


        $myart = $_FILES["myart"];





        if ($myart['tmp_name']) {

            $mydate = date("Y_m_d_H_i_s");
            $uploadfile = $uploaddir.$mydate."_".$myart["name"];
            move_uploaded_file($myart['tmp_name'], $uploadfile);


            $idx = explode( '.', $uploadfile );
            $count_explode = count($idx);
            $idx = strtolower($idx[$count_explode-1]);
            $mime = get_mime_type($uploadfile);

            DB::exec("UPDATE art SET url='$uploadfile',size='".$myart['size']."',mime='$mime',extension='$idx'  WHERE id='".$last_id."'");
             //echo "UPDATE art SET url='$uploadfile',size='".$myart['size']."',mime='$mime',extension='$idx'  WHERE id='".$last_id."'";
        }

        DB::insert("INSERT INTO timeline(user_id,title,text,ip) 
                                          VALUES ('$last_id','New Art Created','New art created by $user->username','$client_ip')");






    }else  if ($_SERVER['REQUEST_METHOD'] === 'POST'){

//            echo "<br>";
//            echo $_SERVER['REQUEST_METHOD'] === 'POST'."<br>";
//            echo $user->authority."<br>";;
//            echo ($user->authority == 'admin')."<br>";;
//            echo $user->authority == 'moderator'|| $user->id == (int) $_GET['art_id']."<br>";

        $_SESSION["status"] = "error";
        $_SESSION["message"] = "No authority";

    }
}catch(Exception $e){
    $_SESSION["status"] = "error";
    $_SESSION["message"] = "Something goes wrong...".$e->getMessage(); //;



}

$art_id = -1;
$art = null;

if(isset($_GET['art_id']))
{
    $art_id = $_GET['art_id'];
    $art = DB::getRow("SELECT *FROM art WHERE id='$art_id'");
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include ("head.php");?>

<style>
    checkbox  {
        width: 20px;
        height: 20px;
        display: block;
        /*background: url("link_to_image");*/
    }
</style>
</head>
<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">
    <?php include "header.php"?>
    <?php include "main_left_sidebar.php"?>

    <div class="content-wrapper">
        <section class="content">
            <?php include "status.php"?>

            <?php
             if($user && ($user->authority == 'admin' || $user->authority == 'artist')){
            ?>
            <form id="new_art_form_1" enctype="multipart/form-data" method="post">
                <input type="text" name="token"  value="<?php echo $token; ?>" hidden="" />
                <input type="number" name="art_id" value="<?php echo $art_id; ?>" hidden/>
                <div class="box box-danger">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="box-header">
                                <?php
                                if($art)
                                    echo  '<h3 style="text-align: center">Edit Art</h3>';
                                else
                                    echo  '<h3 style="text-align: center">Create New Art</h3>';
                                ?>

                            </div>
                            <div class="box-body">
                                <div class="col-md-2"></div>
                                <div class="col-md-8">

                                    <div class="row" id="myphoto" >
                                        <div class="form-group has-danger">
                                            <?php
                                                if($art && startsWith($art->mime,'video'))
                                                {
                                                    echo '<video  width="100%" controls>
                                                        <source src="'.BASE_URL.'/'.$art->url.'" type="'.$art->mime.'">
                                                            Your browser does not support HTML5 video.
                                                    </video>';

                                                }
                                                else if($art)
                                                    echo '<img id="blah" src="'.BASE_URL.'/'.$art->url.'" alt="your image" class="center"  style="max-height: 600px"/>';
                                                else
                                                    echo '<img id="blah" src="#" alt="your image" class="center" style="max-height: 600px"/>';
                                            ?>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group has-danger">
                                            <label class="control-label">Select Your Art</label>
                                            <input onchange="ResizeImage()" id="imgInp" type="file" name="myart" class="form-control form-control-danger">
                                        </div>
                                    </div>
                                    <div class="row" >
                                        <div class="form-group has-danger">
                                            <label class="control-label">Title</label>
                                            <input id="title" type="text" name="title" value="<?php if($art) echo $art->title;?>" class="form-control form-control-danger">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-left: 0px;margin-right: 0px;padding: 0px" >
                                            <div class="form-group has-danger">
                                                <label class="control-label">Share</label><br>
                                        <?php
                                        if($art && $art->share)
                                            echo '<input type="checkbox" name="share" checked>&nbsp&nbsp<i class="fa fa-share-alt bg-blue"></i><br>';
                                        else
                                            echo '<input type="checkbox" name="share"  >&nbsp&nbsp<i class="fa fa-share-alt bg-blue" "></i><br>';
                                        ?>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <label class="control-label">Categories</label><br>

                                                <?php
                                                foreach ($categories as $mycategory)
                                                {
                                                    $checked=false;
                                                    if($art && DB::getVar("SELECT COUNT(*) FROM category_art_relation WHERE art_id='$art_id' AND category_id='$mycategory->id'"))
                                                        $checked = true;
                                                    echo '<div class="col-md-4" style="margin-left: 0px;margin-right: 0px;padding: 0px" ><div class="form-group has-danger"><br>';
                                                    if($checked)
                                                        echo '<input type="checkbox" name="mycategory[]" value="'.$mycategory->id.'" checked>&nbsp&nbsp'.$mycategory->name.'<br>';
                                                    else
                                                        echo '<input type="checkbox" name="mycategory[]" value="'.$mycategory->id.'" >&nbsp&nbsp'.$mycategory->name.'<br>';
                                                    echo  '</div></div>';
                                                }
                                                ?>


                                    </div>
                                    <div class="row" >
                                        <div class="form-group has-danger">
                                            <label class="control-label">Tags</label>
                                            <textarea id="mytags" name="mytags" class="form-control form-control-danger"><?php if($art) echo $art->tags;?></textarea>
                                        </div>
                                    </div>
                                    <div class="row">

                                            <div class="form-group has-danger">
                                                <?php
                                                    if($art)
                                                       {
                                                           echo  '<button type="submit" class="btn btn-success btn-lg btn-block">Edit My Art</button>';
                                                           echo  '<a href="'.BASE_URL.'/delete-art/'.$art->id.'" class="btn btn-danger btn-lg btn-block">DELETE My Art</a>';
                                                       }
                                                    else
                                                        echo  '<button type="submit" class="btn btn-success btn-lg btn-block">Create My Art</button>';
                                                ?>

                                            </div>


                                    </div>



                                </div>
                                <div class="col-md-2"></div>





                            </div>

                            </div>
                        </div>

                    </div>
                </div>
            </form>
    <?php
             }?>
        </section>
    </div>
</div>

<?php
include "footer.php";
?>
</div>
<?php
include "js.php";
?>

<script>

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#blah').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function ResizeImage() {
        var filesToUpload = document.getElementById('imgInp').files;
        var file = filesToUpload[0];

        // Create an image
        var img = document.createElement("img");
        // Create a file reader
        var reader = new FileReader();
        // Set the image once loaded into file reader
        reader.onload = function(e) {
            img.src = e.target.result;

            var canvas = document.createElement("canvas");
            //var canvas = $("<canvas>", {"id":"testing"})[0];
            var ctx = canvas.getContext("2d");
            ctx.drawImage(img, 0, 0);

            var width = img.width;
            var height = img.height;


            canvas.width = width;
            canvas.height = height;
            ctx = canvas.getContext("2d");
            ctx.drawImage(img, 0, 0, width, height);

            dataurl = canvas.toDataURL("image/png");
            document.getElementById('blah').src = dataurl;
        };
        // Load files into file reader
        reader.readAsDataURL(file);
    }

    $(document).ready(function () {

        $("#imgInp").change(function () {
            readURL(this);
            if (!this) {
                $("#myphoto").hide();
                $("#myhr").hide();
            } else {
                $("#myphoto").show();
                $("#myhr").show();
            }

        });
    });


</script>
</body>
</html>
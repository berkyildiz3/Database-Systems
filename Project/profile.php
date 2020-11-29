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

    header('Location: '.BASE_URL."/login");

}


$user = DB::getRow("SELECT *FROM  users WHERE id='".$_SESSION["auth_user_id"]."'");
$followers_number = DB::getVar("SELECT COUNT(*) FROM  follower WHERE follower='".$user->id."'");
$following_number = DB::getVar("SELECT COUNT(*) FROM  follower WHERE followed='".$user->id."'");
$friends_number = DB::getVar("SELECT COUNT(*) FROM  friendship WHERE owner='".$user->id."' OR friend='".$user->id."'");


if ($_SERVER['REQUEST_METHOD'] === 'POST' and clean_string($_POST['token']) == $_SESSION["token"] )
{
    try{

        //print_r($_POST);

        $firstname =clean_string($_POST['firstname']);
        $lastname  =clean_string($_POST['lastname']);
        $phone  =clean_string($_POST['phone']);
        $address  =clean_string($_POST['address']);
        $city  =clean_string($_POST['city']);
        $country  =clean_string($_POST['country']);
        $highschool  =clean_string($_POST['highschool']);
        $university_bsc =clean_string($_POST['university_bsc']);
        $master_ms  =clean_string($_POST['master_ms']);
        $doctorate_phd  =clean_string($_POST['doctorate_phd']);
        $notes  =clean_string($_POST['notes']);


        $query = "UPDATE users SET firstname='".$firstname."',lastname='".$lastname."',phone='".$phone."',address='".$address."',                                        
                                               city='".$city."',country='".$country."',
                                               highschool='".$highschool."',
                                               university_bsc='".$university_bsc."',
                                               master_ms='".$master_ms."',doctorate_phd='".$doctorate_phd."',
                                               notes='".$notes."' WHERE id='".$user->id."'";
        //echo $query;
        $response = DB::exec($query);

        $uploaddir = 'images/users/'.$user->username.'/avatar';
        if (!file_exists("images/users/")) {
            mkdir("images/users/", 0777, true);
        }
        if (!file_exists("images/users/".$user->username)) {
            mkdir("images/users/".$user->username, 0777, true);
        }
        if (!file_exists($uploaddir)) {
            mkdir($uploaddir, 0777, true);
        }


        $avatar = $_FILES["avatar"];


        if ($avatar['tmp_name']) {

            $mydate = date("Y_m_d_H_i_s");
            $uploadfile = $uploaddir."/".$mydate."_".$avatar["name"];
            move_uploaded_file($avatar['tmp_name'], $uploadfile);
            DB::exec("UPDATE users SET avatar='".$uploadfile."'  WHERE id='".$user->id."'");
            //echo "File is valid, and was successfully uploaded.\n";
        }

        $_SESSION["status"] = "success";
        $_SESSION["message"] = "Profile is updated successfully"; //;

    }catch(Exception $e){
        $_SESSION["status"] = "error";
        $_SESSION["message"] = "Something goes wrong...".$e->getMessage(); //;



    }


}



?>

<!DOCTYPE html>
<html>
<head>
    <?php include "head.php"?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <?php include "header.php"?>
    <?php include "main_left_sidebar.php"?>

    <div class="content-wrapper">

    <?php include "status.php"?>

    <section class="content">
      <div class="row">
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
                <?php
                    if($user->avatar) {
                        ?>
                        <img class="profile-user-img img-responsive img-circle" src="<?= $user->avatar ?>"
                             alt="User profile picture">
                        <?php
                    }else {
                        ?>
                        <img class="profile-user-img img-responsive img-circle" src="images/no-image.png"
                             alt="User profile picture">
                        <?php
                    }
                ?>

              <h3 class="profile-username text-center"><?=$user->firstname." ".$user->lastname?></h3>

              <p class="text-muted text-center"></p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Followers</b> <a class="pull-right"><?=$followers_number?></a>
                </li>
                <li class="list-group-item">
                  <b>Following</b> <a class="pull-right"><?=$following_number?></a>
                </li>
                <li class="list-group-item">
                  <b>Friends</b> <a class="pull-right"><?=$friends_number?></a>
                </li>
              </ul>

<!--              <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>-->
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- About Me Box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">About Me</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <strong><i class="fa fa-book margin-r-5"></i> Education</strong>

                <?php
                if($user->doctorate_phd)
                    {
                        echo '<p class="text-muted">'.$user->doctorate_phd.'</p>';
                    }
                if($user->master_ms)
                {
                    echo '<p class="text-muted">'.$user->master_ms.'</p>';
                }
                if($user->university_bsc)
                {
                    echo '<p class="text-muted">'.$user->university_bsc.'</p>';
                }
                if($user->highschool)
                {
                    echo '<p class="text-muted">'.$user->highschool.'</p>';
                }


                ?>




              <hr>

              <strong><i class="fa fa-map-marker margin-r-5"></i> Location</strong>

              <p class="text-muted"><?=$user->city.",".$user->province.",".$user->country?></p>

              <hr>

              <strong><i class="fa fa-file-text-o margin-r-5"></i> Notes</strong>

              <p><?=$user->notes?></p>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#timeline" data-toggle="tab">Timeline</a></li>
              <li><a href="#settings" data-toggle="tab">Settings</a></li>
            </ul>
            <div class="tab-content">
              <!-- /.tab-pane -->
              <div class="tab-pane active" id="timeline">
                <!-- The timeline -->
                <ul class="timeline timeline-inverse">
                  <!-- timeline time label -->
                    <?php

                        $days = DB::get("SELECT created_at,DATE_FORMAT(created_at, '%d %M.%Y') AS mydate FROM timeline WHERE user_id ='$user->id'
                                OR user_id IN (SELECT owner FROM friendship WHERE friend = '$user->id') 
                                OR user_id IN (SELECT friend FROM friendship WHERE owner = '$user->id') 
                                GROUP BY YEAR(created_at),MONTH(created_at),DAY(created_at)
                                ORDER BY created_at DESC");



                        foreach ($days  as $day) {


                            $items = DB::get("SELECT *FROM timeline  WHERE (user_id ='$user->id'
                                OR user_id IN (SELECT owner FROM friendship WHERE friend = '$user->id') 
                                OR user_id IN (SELECT friend FROM friendship WHERE owner = '$user->id'))
                                AND DATE_FORMAT('$day->created_at', '%Y-%m-%d') = DATE_FORMAT(created_at, '%Y-%m-%d') 
                                ORDER BY created_at DESC");

//                            echo "SELECT *FROM timeline  WHERE (user_id ='$user->id'
//                                OR user_id IN (SELECT owner FROM friendship WHERE friend = '$user->id')
//                                OR user_id IN (SELECT friend FROM friendship WHERE owner = '$user->id'))
//                                AND DATE_FORMAT('$day->created_at', '%Y-%m-%d') = DATE_FORMAT(created_at, '%Y-%m-%d') ";




                            echo '<li class="time-label">
                                <span class="bg-red">' .
                                $day->mydate .
                                '</span>
                                </li>';


                                foreach ($items as $item) {


                                    $created_by = DB::getRow("SELECT *FROM users WHERE id='$item->user_id'");
                                    $title = ''.$item->title;




                                    if  ( $title =='Signup')  {

                                        ?>
                                        <li>
                                            <i class="fa fa-address-book bg-blue"></i>

                                            <div class="timeline-item">
                                            <span class="time"><i
                                                        class="fa fa-clock-o"></i><?= $item->created_at ?></span>

                                                <h3 class="timeline-header"><?= $item->title ?></h3>

                                                <div class="timeline-body">
                                                    <?= $item->text ?>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    else if($title =='New Art Created')
                                    {

                                        $photos =explode(";", $item->text);
                                        ?>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fa fa-clock-o"></i><?= $item->created_at ?></span>

                                            <h3 class="timeline-header"><a href="#"><?=$created_by->firstname." ".$created_by->lastname?></a> uploaded new photos</h3>

                                            <div class="timeline-body">
                                                <?php
                                                foreach ($photos as $photo)
                                                    echo '<img src="'.$photo.'" alt="new_photo" class="margin">';
                                                ?>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    else if($title =='Like')
                                    {

                                        $photos =explode(";", $item->text);
                                        ?>
                                        <li>
                                            <i class="fa fa-thumbs-up"></i>

                                            <div class="timeline-item">
                                            <span class="time"><i
                                                        class="fa fa-clock-o"></i><?= $item->created_at ?></span>

                                                <h3 class="timeline-header"><?= $item->title ?></h3>

                                                <div class="timeline-body">
                                                    <?= $item->text ?>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    else if($title =='Dislike')
                                    {

                                        $photos =explode(";", $item->text);
                                        ?>
                                        <li>
                                            <i class="fa fa-thumbs-down"></i>

                                            <div class="timeline-item">
                                            <span class="time"><i
                                                        class="fa fa-clock-o"></i><?= $item->created_at ?></span>

                                                <h3 class="timeline-header"><?= $item->title ?></h3>

                                                <div class="timeline-body">
                                                    <?= $item->text ?>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                    }

                                    else if($title =='Share')
                                    {

                                        $photos =explode(";", $item->text);
                                        ?>
                                        <li>
                                            <i class="fa fa-share-alt bg-blue"></i>

                                            <div class="timeline-item">
                                            <span class="time"><i
                                                        class="fa fa-clock-o"></i><?= $item->created_at ?></span>

                                                <h3 class="timeline-header"><?= $item->title ?></h3>

                                                <div class="timeline-body">
                                                    <?= $item->text ?>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    else if($title =='Un-Share')
                                    {

                                        $photos =explode(";", $item->text);
                                        ?>
                                        <li>
                                            <i class="fa fa-share-alt bg-red"></i>

                                            <div class="timeline-item">
                                            <span class="time"><i
                                                        class="fa fa-clock-o"></i><?= $item->created_at ?></span>

                                                <h3 class="timeline-header"><?= $item->title ?></h3>

                                                <div class="timeline-body">
                                                    <?= $item->text ?>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    else if ( strpos( $title,'friend') !== false){

                                        ?>
                                        <li>
                                            <i class="fa fa-user bg-aqua"></i>

                                            <div class="timeline-item">
                                            <span class="time"><i
                                                        class="fa fa-clock-o"></i><?= $item->created_at ?></span>

                                                <h3 class="timeline-header"><?= $item->title ?></h3>

                                                <div class="timeline-body">
                                                    <?= $item->text ?>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                    else {

                                        ?>
                                        <li>
                                            <i class="fa fa-comments bg-yellow"></i>

                                            <div class="timeline-item">
                                            <span class="time"><i
                                                        class="fa fa-clock-o"></i><?= $item->created_at ?></span>

                                                <h3 class="timeline-header"><?= $item->title ?></h3>

                                                <div class="timeline-body">
                                                    <?= $item->text ?>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                }

                        }
                    ?>
                    <li>
                        <i class="fa fa-clock-o bg-gray"></i>
                    </li>
                  <!-- END timeline item -->
                  <!-- timeline item -->

                </ul>
              </div>
              <!-- /.tab-pane -->

              <div class="tab-pane" id="settings">
                <form class="form-horizontal" method="post" enctype="multipart/form-data">
                    <input type="text"  id="token" name="token" value="<?=$_SESSION["token"]?>" hidden>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="inputEmail" class="col-sm-3 control-label">Email</label>
                                    <div class="col-sm-9" style="margin-left: -10px; ">
                                        <input type="email" class="form-control" id="email" placeholder="Email" value="<?=$user->email?>" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputName" class="col-sm-3 control-label">FirstName</label>
                                    <div class="col-sm-9"  style="margin-left: -10px; ">
                                        <input type="text" class="form-control" id="firstname" name="firstname" placeholder="FirstName" value="<?=$user->firstname?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputName" class="col-sm-3 control-label">LastName</label>
                                    <div class="col-sm-9"  style="margin-left: -10px; ">
                                        <input type="text" class="form-control" id="lastname" name="lastname" placeholder="LastName" value="<?=$user->lastname?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputName" class="col-sm-3 control-label">Phone</label>

                                    <div class="col-sm-9"  style="margin-left: -10px; ">
                                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" value="<?=$user->phone?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row" style="text-align: center;">
                                    <div class="col-md-6">
                                        <div class="form-group has-danger">
                                            <?php
                                            if($user->avatar)
                                                echo '<img style="margin: auto" src="'.$user->avatar.'" class="center" width="100">';
                                            else
                                                echo '<img style="margin: auto" src="images/no-image.png" class="center" width="100">';
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row" id="myphoto" style="display: none;text-align: center">
                                            <div class="form-group has-danger">
                                                <img id="blah" src="#" alt="your image" class="center" width="100" />
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row" style="margin-left:5%;margin-right:5%">
                                    <div class="form-group has-danger"> <label class="control-label">Avatarı Değiştir</label>
                                        <input onchange="ResizeImage()" id="imgInp" type="file" name="avatar" class="form-control form-control-danger">

                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputName" class="col-sm-2 control-label">Address</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="<?=$user->address?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputName" class="col-sm-2 control-label">City</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="city" name="city" placeholder="City" value="<?=$user->city?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputName" class="col-sm-2 control-label">Country</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="country" name="country" placeholder="Country" value="<?=$user->country?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputName" class="col-sm-2 control-label">HighSchool</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="highschool" name="highschool" placeholder="Highschool" value="<?=$user->highschool?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputName" class="col-sm-2 control-label">Univesity B.S.C.</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="university_bsc" name="university_bsc" placeholder="Univesity B.S.C." value="<?=$user->university_bsc?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputName" class="col-sm-2 control-label">Master</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="master_ms" name="master_ms" placeholder="Master" value="<?=$user->master_ms?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputName" class="col-sm-2 control-label">Doctorate</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="doctorate_phd" name="doctorate_phd" placeholder="Doctorate PHD" value="<?=$user->doctorate_phd?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputName" class="col-sm-2 control-label">Notes</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="notes" name="notes" placeholder="Notes" value="<?=$user->notes?>">
                                </div>
                            </div>

                        </div>

                    </div>


                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label"></label>
                    <div class="col-md-10 col-sm-10">
                      <button type="submit" class="btn btn-success btn-block btn-lg">Submit</button>
                    </div>
                  </div>
                </form>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php include "footer.php"?>

</div>
<!-- ./wrapper -->

<?php include "js.php"?>
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

            var MAX_WIDTH = 350;
            var MAX_HEIGHT = 350;
            var width = img.width;
            var height = img.height;

            if (width > height) {
                if (width > MAX_WIDTH) {
                    height *= MAX_WIDTH / width;
                    width = MAX_WIDTH;
                }
            } else {
                if (height > MAX_HEIGHT) {
                    width *= MAX_HEIGHT / height;
                    height = MAX_HEIGHT;
                }
            }
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

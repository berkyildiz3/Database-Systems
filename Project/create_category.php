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
if(!isset($_SESSION['auth_user'])  )  {

    header(BASE_URL."/login");

}else
{
    $count_user = DB::getVar("SELECT COUNT(*) FROM users WHERE id= '".$_SESSION["auth_user_id"]."' AND authority='admin'");
    if($count_user == 0)
        header(BASE_URL."/login");
}
$user = DB::getRow("SELECT *FROM users WHERE id= '".$_SESSION["auth_user_id"]."' AND authority='admin'");

if ($_SERVER['REQUEST_METHOD'] === 'POST' and clean_string($_POST['token']) == $_SESSION["token"] )
{
    try{

//        print_r($_POST);
//        echo (int)$_POST['category_id'] > 0;

        $category_id =(int)$_POST['category_id'];
        $name =clean_string($_POST['name']);
        $seo =sef($name);
        $parent  =(int)$_POST['parent'];
        $tags  =strip_tags(clean_string($_POST['tags']));
        $request_ip = get_client_ip_env();

        if($category_id > 0)//Edit
        {
            $query = "UPDATE category SET name='$name',tags='$tags',created_by='$user->id',seo='$seo' WHERE id='$category_id'";
            DB::exec($query);
            DB::insert("INSERT INTO timeline(user_id,title,text,ip) 
                                          VALUES ('$user->id','Edit Category','Category '.$name.' is edited by admin $user->username','$request_ip')");
            $_SESSION["status"] = "success";
            $_SESSION["message"] = "Category is edited successfully"; //;
        }

        else if($parent <=0)//No parent
        {
            $query = "INSERT INTO category(name,tags,created_by,created_ip,seo) 
                                          VALUES ('$name','$tags','$user->id','$request_ip','$seo')";
            $response = DB::insert($query);
            DB::insert("INSERT INTO timeline(user_id,title,text,ip) 
                                          VALUES ('$user->id','New Category','New Category is creted by admin $user->username','$request_ip')");
            $_SESSION["status"] = "success";
            $_SESSION["message"] = "New category is created successfully"; //;

        }
        else {
            $query = "INSERT INTO category(name,parent,tags,created_by,created_ip,seo) 
                                          VALUES ('$name','$parent','$tags','$user->id','$request_ip','$seo')";
            $response = DB::insert($query);
            DB::insert("INSERT INTO timeline(user_id,title,text,ip) 
                                          VALUES ('$user->id','New Category','New Category is creted by admin $user->username','$request_ip')");
            $_SESSION["status"] = "success";
            $_SESSION["message"] = "New category is created successfully"; //;

        }
        //echo $query;




    }catch(Exception $e){
        $_SESSION["status"] = "error";
        $_SESSION["message"] = "Something goes wrong...".$e->getMessage(); //;



    }


}

$_SESSION['menu_open'] = '';
$_SESSION['active'] = '#create_category_li';

$category_id= -1;
$category= null;
$category_name= "";
$category_parent_id= -1;
$category_tags= "";
if(isset($_GET["category_id"]))
{
    $category_id=$_GET["category_id"];
    $category = DB::getRow("SELECT *FROM category WHERE id ='$category_id'");
    $category_name= $category->name;
    $category_parent_id= $category->parent_id;
    $category_tags= $category->tags;
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

        <!-- Main content -->
        <section class="content">
            <?php include "status.php"?>
            <div class="row">
                <!-- left column -->
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border"  style="text-align: center">
                            <?php
                                if(isset($_GET["category_id"]))
                                    echo "<h3 class=\"box-title\">Edit Category</h3>";
                                else
                                    echo "<h3 class='box-title'>Create Category</h3>";
                            ?>

                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" id="create_category_form" method="post">
                            <input name="token" value="<?=$token?>" hidden>
                            <input name="category_id" value="<?=$category_id?>" hidden>
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Category Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?=$category_name?>" placeholder="Enter Category Name">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Parent</label>
                                    <select  class="form-control" id="paret" name="parent" placeholder="Password">
                                        <option value="0">No Parent</option>
                                        <?php
                                            foreach ($categories as $mycategory)
                                            {
                                                if($mycategory->id != $category_id && $mycategory->id == $category_parent_id)
                                                    echo "<option value='".$mycategory->id."' selected>".$mycategory->name."</option>";
                                                else  if($mycategory->id != $category_id)
                                                    echo "<option value='".$mycategory->id."'>".$mycategory->name."</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Tags</label>
                                    <textarea class="form-control" id="tags" name="tags">
                                    <?=$category_tags?>
                                    </textarea>
                                </div>

                            </div>
                            <!-- /.box-body -->

                            <div class="box-footer">
                                <button type="submit" class="btn btn-success btn-block btn-lg">Submit</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.box -->


                    <!-- /.box -->

                </div>
                <div class="col-md-3"></div>

            </div>
            <!-- /.row -->
        </section>
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

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
$_SESSION['menu_open'] = '';
$_SESSION['active'] = '#view_edit_category_li';
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
        <section class="content">
            <div class="box">
                <div class="box-header">
                    All Categories
                </div>
                <div class="box-body">
                    <table id="category_table" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Name</th>
                            <th>Parent</th>
                            <th>ByAdmin</th>
                            <th>Seo</th>
                            <th>CretedAt</th>
                            <th>CreateIp</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>#ID</th>
                            <th>Name</th>
                            <th>Parent</th>
                            <th>ByAdmin</th>
                            <th>Seo</th>
                            <th>CretedAt</th>
                            <th>CreateIp</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php

                        $counter = 0;
                        foreach($categories as $category){
                            ++$counter;
                            ?>
                            <tr>
                                <td><?=$category->id?></td>
                                <td><?=$category->name?></td>
                                <td><?php
                                    $parent = DB::getRow("SELECT *FROM category WHERE id='$category->parent_id'");
                                    if($parent)
                                    echo "<a href='".BASE_URL."/view-edit-category/".$parent->id."'>".$parent->name."</a>";
                                    else
                                        echo "No Parent";
                                    ?></td>
                                <td><?php
                                    $by_user = DB::getRow("SELECT *FROM users WHERE id='$category->created_by'");
                                    echo "<a href='".BASE_URL."/member-profile/".$by_user->id."'>".$by_user->username."</a>";
                                    ?></td>
                                <td><?=$category->seo?></td>
                                <td><?=$category->created_at?></td>
                                <td><?=$category->created_ip?></td>
                                <td>
                                    <a href="<?=BASE_URL?>/view-edit-category/<?=$category->id?>"> <button class="btn btn-success btn-block">Edit</button>
                                    </a>
                                </td>
                                <td>
                                    <button  data-table="category_table" data-category-id="<?=$category->id?>" class="btn btn-danger btn-block mydelete" >Delete</button>
                                </td>
                            </tr>

                        <?php   } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
    <?php include "footer.php";?>

</div>
<?php include "js.php";?>
<script>

    $("body").on("click",".mydelete",function () {
        var mytable = 'category';
        var id = $(this).data("category-id");

        $.ajax({
            type: 'POST',
            url: "delete-item",
            data: {


                "user_id":'<?=$user->id?>',
                "secret":'<?=$user->salt?>',
                "table":mytable,
                'id':id,
            },

            success: function(response) {
                console.log(response);
                response = JSON.parse(response);
                if (response.status == "success") {
                    Swal.fire(
                        {
                            text: response.message,
                            type: "success",


                        }
                    ).then(function () {
                        location.reload();
                    });

                } else {
                    Swal.fire(
                        {
                            html: response.message,
                            type: "error",


                        }
                    );


                }
            }//end success
        });//end of ajax
    });
    $(document).ready(function (e) {


        $('#category_table').DataTable({
            dom: 'Bfrtip',
            pageLength: 7,
            columnDefs: [

                {
                    render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-90'>" + data + "</div>";
                    },
                    targets: [0,1,2,3,4,5,6,7,8]
                }


            ],
            responsive:true,
            fixedColumns: false,
            autoWidth: false,
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ],
        });

    });


</script>

</body>
</html>


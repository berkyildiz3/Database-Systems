<?php
if(isset($_SESSION["status"]))
{
    ?>
    <section class="content" style="min-height: 50px;margin-bottom: 0px;padding-bottom: 0px">
        <div class="row">
            <div class="col-md-12">

                <?php
                    if($_SESSION["status"] == "success") {   ?>
                        <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-check"></i> Congratulations!</h4>
                        <?=$_SESSION["message"]?>
                    </div>
                <?php

                    unset($_SESSION["status"]);
                    unset($_SESSION["message"]);
                    }else if($_SESSION["status"] == "error") {
                ?>
                    <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa fa-close"></i> Error!</h4>
            <?=$_SESSION["message"]?>
        </div>
                <?php

                    unset($_SESSION["status"]);
                    unset($_SESSION["message"]);
                }?>
            </div>
        </div>
    </section>
<?php
}
?>
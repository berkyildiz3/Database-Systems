<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->

        <!-- search form -->
        <form action="<?=BASE_URL."/search"?>" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="keyword" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                <button type="submit" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li id="index_li">
                <a href="<?=BASE_URL?>">
                    <i class="fa fa-th"></i> <span>Home</span>
                    <small class="label pull-right">
                        <i class="fa fa-angle-right pull-left"></i>
                    </small>
                </a>
            </li>
            <?php
                if (isset($_SESSION["auth_user"]) && $user->authority == 'admin') {
                    ?>
                    <li class="header" style="font-size: larger;color: white">Admin Authority</li>

                    <li id="create_category_li">
                        <a href="<?=BASE_URL?>/create-category">
                            <i class="fa fa-th"></i> <span>Create Category</span>
                            <span class="pull-right-container">
                                <small class="label pull-right">
                                    <i class="fa fa-angle-right pull-left"></i>
                                </small>
                            </span>
                        </a>
                    </li>
                    <li id="view_edit_category_li">
                        <a href="<?=BASE_URL?>/view-edit-category">
                            <i class="fa fa-th"></i> <span>View-Edit Category</span>
                            <small class="label pull-right">
                                <i class="fa fa-angle-right pull-left"></i>
                            </small>
                        </a>
                    </li>
                    <?php
                        if(isset($_GET["category_id"])){
                    ?>
                            <li id="edit_category_li" class="active">
                                <a href="<?=BASE_URL?>/view-edit-category">
                                    <i class="fa fa-th"></i> <span>Edit Category Special</span>
                                    <small class="label pull-right">
                                        <i class="fa fa-angle-right pull-left"></i>
                                    </small>
                                </a>
                            </li>
                            <?php
                        }?>

                    <li id="view_all_users_li">
                        <a href="<?=BASE_URL?>/view-all-users">
                            <i class="fa fa-th"></i> <span>View All Users</span>
                            <small class="label pull-right">
                                <i class="fa fa-angle-right pull-left"></i>
                            </small>
                        </a>
                    </li>
                    <li id="view_all_arts">
                        <a href="<?=BASE_URL?>/view-all-arts">
                            <i class="fa fa-th"></i> <span>View All Arts</span>
                            <small class="label pull-right">
                                <i class="fa fa-angle-right pull-left"></i>
                            </small>
                        </a>
                    </li>

                    <li id="view-all-authority">
                        <a href="<?=BASE_URL?>/view-all-authority">
                            <i class="fa fa-th"></i> <span>View All Authority</span>
                            <small class="label pull-right">
                                <i class="fa fa-angle-right pull-left"></i>
                            </small>
                        </a>
                    </li>


                    <?php
                }
            ?>
            <li class="header" style="font-size: larger;color: white">Upload</li>
            <li id="upload_art_li">
                <a href="<?=BASE_URL?>/upload-art">
                    <i class="fa fa-th"></i> <span>Upload Art</span>
                    <small class="label pull-right">
                        <i class="fa fa-angle-right pull-left"></i>
                    </small>
                </a>
            </li>
            <?php
                if(isset($_SESSION["auth_user"])) {
                    ?>
                    <li id="upload_art_li">
                        <a href="<?=BASE_URL?>/my-arts">
                            <i class="fa fa-th"></i> <span>My Arts</span>
                            <small class="label pull-right">
                                <i class="fa fa-angle-right pull-left"></i>
                            </small>
                        </a>
                    </li>
                    <?php
                }
            ?>
            <li class="header" style="font-size: larger;color: white">All Categories</li>
            <li class="treeview" id="categories_li">
                <a href="#">
                    <i class="fa fa-pie-chart"></i>
                    <span>Categories</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu" style="display: none;">
                    <?php
                    if($categories)
                        foreach ($categories as $category){
                    ?>
                    <li id="<?=$category->seo?>_li">
                        <a href="<?=BASE_URL?>/art-category/<?=$category->seo?>">
                            <i class="fa fa-th"></i> <span><?=$category->name?></span>
                            <span class="pull-right-container">
                                 <i class="fa fa-angle-right pull-left"></i>
                            </span>
                        </a>
                    </li>
                    <?php
                        }?>
                </ul>
            </li>


            <li class="header" style="font-size: larger;color: white">Documentation</li>

            <li>
                <a href="<?=BASE_URL."/documentation"?>">
                    <i class="fa fa-book"></i>
                    <span>ER Paper</span>
                </a>
            </li>
            <li>
                <a href="<?=BASE_URL."/documentation"?>">
                    <i class="fa fa-book"></i>
                    <span>Project Report</span>
                </a>
            </li>

        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
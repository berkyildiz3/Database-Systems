<!-- jQuery 3 -->
<script src="<?=BASE_URL?>/bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?=BASE_URL?>/bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?=BASE_URL?>/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="<?=BASE_URL?>/bower_components/raphael/raphael.min.js"></script>
<script src="<?=BASE_URL?>/bower_components/morris.js/morris.min.js"></script>
<!-- Sparkline -->
<script src="<?=BASE_URL?>/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="<?=BASE_URL?>/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?=BASE_URL?>/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="<?=BASE_URL?>/bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?=BASE_URL?>/bower_components/moment/min/moment.min.js"></script>
<script src="<?=BASE_URL?>/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="<?=BASE_URL?>/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?=BASE_URL?>/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="<?=BASE_URL?>/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?=BASE_URL?>/bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?=BASE_URL?>/dist/js/adminlte.min.js"></script>


<!-- SweetAlert2 -->
<script type="text/javascript" src="<?=BASE_URL?>/plugins/sweetalert/sweetalert2.min.js"></script>

<script src="<?=BASE_URL?>/plugins/modal-video/js/jquery-modal-video.min.js"></script>


<!-- DataTables -->
<script src="<?=BASE_URL?>/plugins/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?=BASE_URL?>/plugins/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?=BASE_URL?>/plugins/datatables.net-bs/js/dataTables.responsive.min.js"></script>
<script src="<?=BASE_URL?>/plugins/datatables.net-bs/js/dataTables.buttons.min.js"></script>
<script src="<?=BASE_URL?>/plugins/datatables.net-bs/js/dataTables.checkboxes.min.js"></script>
<script src="<?=BASE_URL?>/plugins/datatables.net-bs/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>

<!-- Dropzone App -->
<script src="<?=BASE_URL?>/plugins/dropzone/dropzone.js"></script>

<script>
    $(document).ready(function(){

        //$("li").each(function(){
        //    $(this).removeClass("active");
        //    $(this).removeClass("menu-open");
        //    $(this).children("ul").css("display","none");
        //});
        <?php
        //
        //if(isset($_SESSION['menu_open']))
        //{
        //?>
        //$('<?//=$_SESSION["menu_open"]?>//').addClass('active menu-open');
        //$('<?//=$_SESSION["menu_open"]?>//').children("ul").css("display","block");
        <?php
        //}
        //if(isset($_SESSION['active']))
        //{
        //?>
        //$('<?//=$_SESSION["active"]?>//').addClass('active');
        <?php
        //}
        //?>



    });



</script>
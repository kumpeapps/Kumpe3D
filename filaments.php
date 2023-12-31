<?php
require_once("./mysql_params.php");
$user = $user."_RO";

$connection = mysqli_connect($host,$user,$pass,'Web_3dprints') or die ("Couldn't connect to server.");

$strength = 0;
$strengthCondition = ">=";
$typeFilter = "%";

if (isset($_GET['strength'])) {
    $strength = $_GET['strength'] ?: 0;
}

if (isset($_GET['strengthCondition'])) {
    $strengthCondition = $_GET['strengthCondition'] ?: ">=";
}

if (isset($_GET['typeFilter'])) {
    $typeFilter = $_GET['typeFilter'] ?: "%";
}

$strengthFilter = "AND strength ".$strengthCondition." ".$strength;

//Get Filaments
$SQL = "
    SELECT 
        *
    FROM
        Web_3dprints.vw__filaments
    WHERE
        1 = 1
        AND type LIKE '$typeFilter'
        $strengthFilter;
    ";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="./images/favicon.png">
    <title>Kumpe 3D</title>
    <!-- Custom CSS -->
    <link href="https://khome.kumpeapps.com/portal/dist/css/style.min.css" rel="stylesheet">
    <link href="https://khome.kumpeapps.com/portal/dist/css/pages/ribbon-page.css" rel="stylesheet">
    <link href="https://khome.kumpeapps.com/portal/dist/css/pages/ui-bootstrap-page.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body class="skin-green-dark fix-header single-column card-no-border fix-sidebar">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">Kumpe 3D by KumpeApps LLC</p>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <?php 
            include './dist/php/header.php';
        ?>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
         <?php
            include './dist/php/left-sidebar.php';
        ?>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->
                <div class="row page-titles">
                    <div class="col-md-5 align-self-center">
                        <h4 class="text-themecolor">Kumpe 3d Available Colors</h4><br>
                        <h7 class="text-themecolor">
                            If you wish to have a color not listed feel free to email us at helpdesk@kumpeapps.com. Chances are we can order the color you want.
                        </h7>
                    </div>
                    <div class="col-md-7 align-self-center text-right">
                        <div class="d-flex justify-content-end align-items-center">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                                <li class="breadcrumb-item active">Filaments</li>
                            </ol>
                             
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <!-- .row -->
                <div class="row">

                <?php
                    if ($Query = mysqli_query($connection, $SQL)){
                        // Loop through each row in the result set
                        while($filament = mysqli_fetch_array($Query))
                        {
                            if ($filament['coming_soon'] == 1) {
                                $leftribbon = '<div data-toggle="tooltip" data-placement="bottom" title="This filament is either on the way or has arrived and is being quality tested. Orders may experience a minimal delay." class="ribbon ribbon-bookmark  ribbon-warning">Coming Soon</div>';
                            } elseif ($filament['discontinued'] == 1) {
                                $leftribbon = '<div data-toggle="tooltip" data-placement="bottom" title="This filament is no longer available." class="ribbon ribbon-left ribbon-danger">Discontinued</div>';
                            } elseif ($filament['special_order'] == 1) {
                                $leftribbon = '<div data-toggle="tooltip" data-placement="bottom" title="This is a special order filament that requires custom pricing. Please email helpdesk@kumpeapps.com for a quote." class="ribbon ribbon-left ribbon-warning">Special Order</div>';
                            } else {
                                $leftribbon = '<div class="ribbon ribbon-left ribbon-success">'.$filament['cost_category'].'</div>';
                            }

                            if ($filament['backorder'] == 1) {
                                $rightribbon = '<div data-toggle="tooltip" data-placement="bottom" title="This filament has been ordered but shipping is delayed. Expect extended delay on this filament." class="ribbon ribbon-right ribbon-danger">Backordered</div>';
                            } elseif ($filament['instock'] == 0) {
                                $rightribbon = '<div data-toggle="tooltip" data-placement="bottom" title="This filament is currently out of stock. Depending on order volumes for this filament, we may not order any more until an order is placed for this filament. Orders with this filament may experience a shipping delay." class="ribbon ribbon-right ribbon-warning">Out of Stock</div>';
                            } else {
                                $rightribbon = '';
                            }
                            $swatchid = $filament['swatch_id'];
                            $photolink = "https://images.kumpeapps.com/filament_swatch?swatch=$swatchid";

                            $tpubadge = ($filament['type'] == 'TPU') ? ' <span class="label label-rounded label-danger">NOTE: TPU filament is flexible similar to rubber!</span>' : '' ;
                            $multiColorBadge = ($filament['multi_color'] == 1) ? ' <span class="label label-rounded label-warning">NOTE: This is a multi-color filament. Multi-Color filaments may see little to no color change on smaller products.</span>' : '' ;
                            
                    echo '
                    <!-- .col -->
                    <div class="col-md-6 col-lg-6 col-xlg-4">
                    <div class="ribbon-wrapper card">
                                    '.$leftribbon.'
                                    '.$rightribbon.'
                        <div class="card card-body">
                            <div class="row align-items-center">
                                <div class="col-md-4 col-lg-3 text-center">
                                    <a href="https://3d.kumpeapps.com/filament-detail.php?swatch_id='.$filament['swatch_id'].'"><img src="'.$photolink.'" alt="user" class="img-circle img-fluid"></a>
                                </div>
                                <div class="col-md-8 col-lg-9">
                                    <h3 class="box-title m-b-0">'.$filament['name'].'</h3> <small>Filament #: '.$filament['swatch_id'].'</small>
                                    <address>
                                        Color: '.$filament['color_name'].$multiColorBadge.'<br>
                                        Type: '.$filament['type'].$tpubadge.'<br>
                                        Notes: '.$filament['notes'].'
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <!-- /.col -->';
                }
            }
                    ?>
                </div>
                <!-- /.row -->
                <!-- ============================================================== -->
                <!-- End PAge Content -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Right sidebar -->
                <!-- ============================================================== -->
                <!-- .right-sidebar -->
                <?php
                    include 'https://khome.kumpeapps.com/portal/dist/php/right-sidebar.php';
                ?>
                <!-- ============================================================== -->
                <!-- End Right sidebar -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- footer -->
        <!-- ============================================================== -->
         <?php
            include 'https://khome.kumpeapps.com/portal/dist/php/footer.php';
        ?>
        <!-- ============================================================== -->
        <!-- End footer -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="https://khome.kumpeapps.com/portal/assets/node_modules/jquery/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="https://khome.kumpeapps.com/portal/assets/node_modules/popper/popper.min.js"></script>
    <script src="https://khome.kumpeapps.com/portal/assets/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="https://khome.kumpeapps.com/portal/dist/js/perfect-scrollbar.jquery.min.js"></script>
    <!--Wave Effects -->
    <script src="https://khome.kumpeapps.com/portal/dist/js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="https://khome.kumpeapps.com/portal/dist/js/sidebarmenu.js"></script>
    <!--stickey kit -->
    <script src="https://khome.kumpeapps.com/portal/assets/node_modules/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <script src="https://khome.kumpeapps.com/portal/assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
    <!--Custom JavaScript -->
    <script src="https://khome.kumpeapps.com/portal/dist/js/custom.min.js"></script>
</body>

</html>
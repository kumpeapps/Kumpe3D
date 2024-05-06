<?php
require_once 'includes/site_params.php';
$connection = mysqli_connect(
    'sqlreadonly.kumpedns.us',
    $_ENV['mysql_user'],
    $_ENV['mysql_pass'],
    'Web_3dprints'
) or die("Couldn't connect to server.");

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

$strengthFilter = "AND strength " . $strengthCondition . " " . $strength;

//Get Filaments
$SQL = "
    SELECT 
        *
    FROM
        Web_3dprints.vw__filaments
    WHERE
        1 = 1
        AND type LIKE '$typeFilter'
        AND `type` <> 'Non-Filament'
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
            <p class="loader__label">Kumpe3D by KumpeApps LLC</p>
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
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Bread crumb and right sidebar toggle -->
                <!-- ============================================================== -->
                <div class="row page-titles">
                    <div class="col-md-5 align-self-center">
                        <h4 class="text-themecolor">Kumpe3D Available Colors</h4><br>
                        <h7 class="text-themecolor">
                            If you wish to have a color not listed feel free to email us at sales@kumpe3d.com. Chances
                            are we can order the color you want.
                        </h7>
                    </div>
                    <div class="col-md-7 align-self-center text-right">
                        <div class="d-flex justify-content-end align-items-center">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="https://www.kumpe3d.com">Home</a></li>
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
                    if ($Query = mysqli_query($connection, $SQL)) {
                        // Loop through each row in the result set
                        while ($filament = mysqli_fetch_array($Query)) {
                            if ($filament['coming_soon'] == 1) {
                                $leftribbon = '<div data-toggle="tooltip" data-placement="bottom" title="This filament is either on the way or has arrived and is being quality tested. Orders may experience a minimal delay." class="ribbon ribbon-bookmark  ribbon-info">Coming Soon</div>';
                            } else if ($filament['discontinued'] == 1) {
                                $leftribbon = '<div data-toggle="tooltip" data-placement="bottom" title="This filament is no longer available." class="ribbon ribbon-left ribbon-danger">In Stock but Discontinued</div>';
                            } else if ($filament['special_order'] == 1) {
                                $leftribbon = '<div data-toggle="tooltip" data-placement="bottom" title="This is a special order filament that requires custom pricing. Please email helpdesk@kumpeapps.com for a quote." class="ribbon ribbon-left ribbon-warning">Special Order</div>';
                            } else if (intval($filament['full_rolls_instock']) > 0) {
                                $leftribbon = '<div class="ribbon ribbon-left ribbon-success">In Stock</div>';
                            } else if (intval($filament['partial_rolls_instock']) > 0) {
                                $leftribbon = '<div class="ribbon ribbon-left ribbon-warning">Low Stock</div>';
                            } else {
                                $leftribbon = '<div class="ribbon ribbon-left ribbon-danger">Out of Stock</div>';
                            }

                            if ($filament['backorder'] === 1) {
                                $rightribbon = '<div data-toggle="tooltip" data-placement="bottom" title="This filament has been ordered but shipping is delayed. Expect extended delay on this filament." class="ribbon ribbon-right ribbon-danger">Backordered</div>';
                            } else {
                                $rightribbon = '';
                            }
                            $swatchid = $filament['swatch_id'];
                            $photolink = "https://images.kumpeapps.com/filament?swatch=$swatchid";

                            $tpubadge = ($filament['type'] === 'TPU') ? ' <span class="label label-rounded label-danger">NOTE: TPU filament is flexible similar to rubber!</span>' : '';
                            if ($filament['multi_color'] === '1') {
                                $multiColorBadge = ' <span class="label label-rounded label-warning">NOTE: This is a Color Change filament. Color Change filaments may see little to no color change on smaller products.</span>';
                            } else if ($filament['dual_color'] === '1') {
                                $multiColorBadge = ' <span class="label label-rounded label-warning">NOTE: This is a Multi-Color Filament. The colors listed are mixed depending on nozzle travel so the color/pattern may differ in multiple prints.</span>';
                            } else {
                                $multiColorBadge = '';
                            }

                            echo '
                    <!-- .col -->
                    <div class="col-md-6 col-lg-6 col-xlg-4">
                    <div class="ribbon-wrapper card">
                                    ' . $leftribbon . '
                                    ' . $rightribbon . '
                        <div class="card card-body">
                            <div class="row align-items-center">
                                <div class="col-md-4 col-lg-3 text-center">
                                    <img src="' . $photolink . '" alt="user" class="img-circle img-fluid"></a>
                                </div>
                                <div class="col-md-8 col-lg-9">
                                    <h3 class="box-title m-b-0">' . $filament['name'] . '</h3> <small>Color ID: ' . $filament['swatch_id'] . '</small>
                                    <address>
                                        Color: ' . $filament['color_name'] . $multiColorBadge . '<br>
                                        Type: ' . $filament['type'] . $tpubadge . '<br>
                                        Notes: ' . $filament['notes'] . '
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

            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->

        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
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
    <script
        src="https://khome.kumpeapps.com/portal/assets/node_modules/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <script src="https://khome.kumpeapps.com/portal/assets/node_modules/sparkline/jquery.sparkline.min.js"></script>
    <!--Custom JavaScript -->
    <script src="https://khome.kumpeapps.com/portal/dist/js/custom.min.js"></script>
</body>

</html>
<?php
mysqli_close($connection);

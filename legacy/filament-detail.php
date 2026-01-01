<?php
require_once('/home4/angela70/public_html/kumpeapps.com/khome/portal/mysqluser.php');

$connection = mysqli_connect($host,$user,$pass,'Web_3dprints') or die ("Couldn't connect to server.");
$swatch_id = $_GET['swatch_id'];

//Get Filaments
$filament_data_sql = "
    SELECT 
        *
    FROM
        Web_3dprints.filament
    WHERE swatch_id = $swatch_id;
    ";

//Get Prints
$print_data_sql = "
    SELECT 
        *
    FROM
        Web_3dprints.vw__print_log
    WHERE swatch_id = $swatch_id;
    ";

//Get Filament Detail
if ($filament_data_query = mysqli_query($connection, $filament_data_sql)){
    $filament = mysqli_fetch_array($filament_data_query);

    $comingSoonRibbon = ($filament['coming_soon'] == 1) ? ' <div class="label label-rounded ribbon-warning">Coming Soon</div>' : '';
    $discontinuedRibbon = ($filament['discontinued'] == 1) ? ' <div class="label label-rounded ribbon-danger">Discontinued</div>' : '';
    $costRibbon = ($filament['special_order'] == 1) ? ' <div class="label label-rounded ribbon-warning">Special Order</div>' : '<div class="label label-rounded ribbon-success">'.$filament['cost_category'].'</div>';
    $costCategoryRibbon = ($filament['special_order'] == 1) ? ' <div class="ribbon ribbon-left ribbon-warning">Special Order</div>' : '<div class="ribbon ribbon-left ribbon-success">'.$filament['cost_category'].'</div>';

    $backorderRibbon = ($filament['backorder'] == 1) ? ' <div class="label label-rounded ribbon-danger">Backordered</div>' : '';
    $oosRibbon = ($filament['instock'] == 0) ? ' <div class="label label-rounded ribbon-warning">Out of Stock</div>' : '';

    if ($filament['photo_link'] == 'benchy') {
        $photolink = 'pcloud/Benchy/'.$filament['swatch_id'].'.jpeg';
    } elseif (is_null($filament['photo_link'])) {
        $photolink = 'https://img.icons8.com/ios/50/null/image--v1.png';
    } else {
        $photolink = $filament['photo_link'];
    }

    $tpubadge = ($filament['type'] == 'TPU') ? ' <span class="label label-rounded label-danger">NOTE: TPU filament is flexible similar to rubber!</span>' : '' ;
    $multiColorBadge = ($filament['multi_color'] == 1) ? ' <span class="label label-rounded label-warning">NOTE: This is a multi-color filament. Multi-Color filaments may see little to no color change on smaller products.</span>' : '' ;
}

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
    <link rel="icon" type="image/png" sizes="16x16" href="https://khome.kumpeapps.com/portal/dist/images/favicon.png">
    <title>Kumpe 3D</title>
    <!-- Page CSS -->
    <link href="https://khome.kumpeapps.com/portal/dist/css/pages/contact-app-page.css" rel="stylesheet">
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

<body class="skin-green-dark single-column fixed-layout">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">Kumpe 3D</p>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
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
                        <h4 class="text-themecolor">Filament Details</h4>
                    </div>
                    <div class="col-md-7 align-self-center text-right">
                        <div class="d-flex justify-content-end align-items-center">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                                <li class="breadcrumb-item"><a href="https://3d.kumpeapps.com/filaments.php">Filaments</a></li>
                                <li class="breadcrumb-item active">Filament Details</li>
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
                <!-- Row -->
                <div class="row">
                    <!-- Column -->
                    <div class="col-lg-4 col-xlg-3 col-md-5">
                        <div class="ribbon-wrapper card">
                        <?php echo $costCategoryRibbon; ?>
                        <div class="card"> <img class="card-img" src="<?php echo $photolink; ?>" height="456" alt="Card image">
                            <div class="card-img-overlay card-inverse text-white social-profile d-flex justify-content-center">
                                <div class="align-self-center"> <img src="<?php echo $photolink; ?>" class="img-circle" width="100">
                                    <h4 class="card-title"><?php echo $filament['name']; ?></h4>
                                    <h6 class="card-subtitle"><?php echo $filament['type']; ?></h6>
                                </div>
                            </div>
                        </div>
                        <?php echo $multiColorBadge; ?><br>
                        <?php echo $tpubadge; ?>
                    </div>
                        <div class="card">
                            <div class="card-body"> <small class="text-muted">Tags</small>
                                <h6><?php echo $costRibbon.$comingSoonRibbon.$discontinuedRibbon.$backorderRibbon.$oosRibbon; ?></h6> 
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="col-lg-8 col-xlg-9 col-md-7">
                        <div class="card">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs profile-tab" role="tablist">
                                <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#home" role="tab">Recent Prints</a> </li>
                                <li class="nav-item"> <a class="nav-link"  href="<?php echo $filament['3dprintlog_link']; ?>" role="tab">Profile</a> </li>
                                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="tab-pane active" id="home" role="tabpanel">
                                    <div class="card-body">
                                        <div class="profiletimeline">
                                            <?php
                                            if ($Query = mysqli_query($connection, $print_data_sql)){
                                            // Loop through each row in the result set
                                            while($print = mysqli_fetch_array($Query))
                                            { echo '
                                            <div class="sl-item">
                                                <div class="sl-left"> <img src="https://img.icons8.com/color/48/null/3d-printer.png" alt="" class="img-circle" /> </div>
                                                <div class="sl-right">
                                                    <div><a href="javascript:void(0)"class="link">'.$print['item_name'].'</a> <span class="sl-date">'.$print['days_ago'].' days ago</span>
                                                        <p>sold thru '.$print['sold_via'].'. Price: $'.$print['price'].'</a></p>
                                                        <div class="row">
                                                            <div class="col-lg-3 col-md-6 m-b-20"><img src="'.$print['photo_link'].'" class="img-responsive radius" /></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>';
                                        }}
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <!--second tab-->
                                <div class="tab-pane" id="profile" role="tabpanel">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 col-xs-6 b-r"> <strong>Full Name</strong>
                                                <br>
                                                <p class="text-muted">Johnathan Deo</p>
                                            </div>
                                            <div class="col-md-3 col-xs-6 b-r"> <strong>Mobile</strong>
                                                <br>
                                                <p class="text-muted">(123) 456 7890</p>
                                            </div>
                                            <div class="col-md-3 col-xs-6 b-r"> <strong>Email</strong>
                                                <br>
                                                <p class="text-muted">johnathan@admin.com</p>
                                            </div>
                                            <div class="col-md-3 col-xs-6"> <strong>Location</strong>
                                                <br>
                                                <p class="text-muted">London</p>
                                            </div>
                                        </div>
                                        <hr>
                                        <p class="m-t-30">Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.</p>
                                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries </p>
                                        <p>It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
                                        <h4 class="font-medium m-t-30">Skill Set</h4>
                                        <hr>
                                        <h5 class="m-t-30">Wordpress <span class="pull-right">80%</span></h5>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width:80%; height:6px;"> <span class="sr-only">50% Complete</span> </div>
                                        </div>
                                        <h5 class="m-t-30">HTML 5 <span class="pull-right">90%</span></h5>
                                        <div class="progress">
                                            <div class="progress-bar bg-info" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" style="width:90%; height:6px;"> <span class="sr-only">50% Complete</span> </div>
                                        </div>
                                        <h5 class="m-t-30">jQuery <span class="pull-right">50%</span></h5>
                                        <div class="progress">
                                            <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width:50%; height:6px;"> <span class="sr-only">50% Complete</span> </div>
                                        </div>
                                        <h5 class="m-t-30">Photoshop <span class="pull-right">70%</span></h5>
                                        <div class="progress">
                                            <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:70%; height:6px;"> <span class="sr-only">50% Complete</span> </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="settings" role="tabpanel">
                                    <div class="card-body">
                                        <h4><b>Nozzle Temp:</b> <?php echo $filament['hotend_temp']; ?></h4>
                                        <h4><b>Bed Temp:</b> <?php echo $filament['bed_temp']; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                </div>
                <!-- Row -->
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
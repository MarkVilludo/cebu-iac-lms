<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>iACADEMY-Cebu</title>
    <!-----CSS----------------------------------------------->
    <link rel="stylesheet"
        href="<?php echo base_url(); ?>assets/lib/adminlte/css/jQueryUI/jquery-ui-1.10.3.custom.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/lib/adminlte/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/datatables.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/lib/adminlte/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/lib/adminlte/css/select2/select2.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/lib/adminlte/css/datepicker/datepicker3.css">
    <link rel="stylesheet"
        href="<?php echo base_url(); ?>assets/lib/adminlte/css/daterangepicker/daterangepicker-bs3.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/lib/adminlte/css/ionicons.min.css">
    <link href="<?php echo base_url(); ?>assets/lib/adminlte/css/iCheck/all.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/lib/adminlte/css/AdminLTE.min.css">
    <link rel="icon" href="https://iacademy.edu.ph/assets/img/fav_new.png">

    <!-- <script type="text/javascript" src="<?php echo base_url(); ?>assets/themes/default/js/script.js"></script> -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.6.2/sweetalert2.min.css"
        integrity="sha512-5aabpGaXyIfdaHgByM7ZCtgSoqg51OAt8XWR2FHr/wZpTCea7ByokXbMX2WSvosioKvCfAGDQLlGDzuU6Nm37Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"
        integrity="sha512-WFN04846sdKMIP5LKNphMaWzU7YpMyCU245etK3g/2ARYbPK9Ub18eG+ljU96qKRCWh+quCY7yefSmlkQw1ANQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>

    <style>
    .swal2-popup {
        font-size: 1.6rem !important;
    }
    </style>

    <?php 
    switch($user['intUserLevel'] ){
        case 0:
        $skin = 'skin-black-light';
        break;
        case 1:
        $skin = 'skin-red-light';
        break;
        case 2:
        $skin = 'skin-black';
        break;
        case 3:
        $skin = 'skin-green-light';
        break;
        case 4:
        $skin = 'skin-purple-light';
        break;
        case 5:
        $skin = 'skin-black';
        break;
        case 6:
        $skin = 'skin-yellow-light';
        break;
        default: 
        $skin = 'skin-blue';
    } 
?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/lib/adminlte/css/skins/<?php echo $skin; ?>.css">
    <link rel="stylesheet"
        href="<?php echo base_url(); ?>assets/lib/adminlte/css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" />
    <link href="<?php echo $css_dir; ?>token-input.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $css_dir; ?>token-input-facebook.css" rel="stylesheet" type="text/css" />


    <link rel="stylesheet" href="<?php echo $css_dir; ?>style.css">
    <!-----END CSS------------------------------------------->
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->    
</head>

<body class="layout-top-nav <?php echo $skin; ?>">    
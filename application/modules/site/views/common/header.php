<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/themes/site/css/style.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/themes/site/css/owl.carousel.min.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/themes/site/css/owl.theme.default.css" />
    <link rel="icon" type="image/png" href="<?php echo base_url() ?>assets/themes/site/images/fav.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.6.2/sweetalert2.min.css"
        integrity="sha512-5aabpGaXyIfdaHgByM7ZCtgSoqg51OAt8XWR2FHr/wZpTCea7ByokXbMX2WSvosioKvCfAGDQLlGDzuU6Nm37Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script type="text/javascript" src="<?php echo base_url(); ?>assets/themes/default/js/script.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js"
        integrity="sha512-WFN04846sdKMIP5LKNphMaWzU7YpMyCU245etK3g/2ARYbPK9Ub18eG+ljU96qKRCWh+quCY7yefSmlkQw1ANQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>

    <title>iACADEMY Cebu</title>
</head>

<body>

    <div class="body-container" id="bodycontainer"></div>



    <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <ul>
            <li>
                <a class="hover-line white inline-block" href="<?php echo base_url(); ?>site">Home</a>
            </li>

            <li>
                <a class="hover-line inline-block" target="_blank" href="https://iacademy.edu.ph/homev4/about">About
                    iACADEMY</a>
            </li>
        </ul>
    </div>

    <!-- Use any element to open the sidenav -->

    <div class="horizontal-nav h-[80px] flex items-center fixed w-full top-0 px-14 py-2 justify-between z-40">
        <a onclick="openNav()" class="md:w-[170px] cursor-pointer">
            <img src="<?php echo $img_dir; ?>menu.svg" />
        </a>
        <a href="<?php echo base_url(); ?>site/applicant_first_step">
            <img src="<?php echo $img_dir; ?>btn-apply.png" class="w-[170px] img-btn" alt="" />
        </a>
        <a href="<?php echo base_url(); ?>"> <img src=" <?php echo $img_dir; ?> logo.png"
                class="h-[50px] hidden md:block" alt="" /></a>

    </div>
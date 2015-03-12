<?php
require 'connect.php';
require 'getSession.php';
require 'html_functions.php';
require 'mediaPath.php';
require 'findURL.php';
require 'model_functions.php';

get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];

?>

<style>

    iframe {
        max-width: 100%;
        max-height: 500px;
    }

    img {
        max-width: 100%;
        max-height:500px;
    }

    video {
        max-width: 100%;
        max-height: 500px;
    }

    embed {
        max-width: 100%;
        max-height: 500px;
    }

    script {
        max-width: 100%;
        max-height: 500px;
    }

    .btnApprove {
        background: url("/images/gray_check.png") no-repeat;
        width: 30px;
        height: 30px;
        border: none;
    }

    .btnDisapprove {
        background: url("/images/red_check.png") no-repeat;
        width: 30px;
        height: 30px;
        border: none;
    }
</style>



<body>

<div class="container" >
    <div class="row">
        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call ">


          <!------------->
            </div>
        </div>
    </div>


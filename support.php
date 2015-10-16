<?php
require 'connect.php';
require 'model_functions.php';
require 'mediaPath.php';
require 'getSession_public.php';
require 'html_functions.php';


get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];
?>




<body style="background: url('/images/office.jpg');opacity: 1">

<?php

?>

<div class="container" >


    <div class="col-xs-12 col-md-8 col-lg-8 col-md-offset-2" style="color:yellow;font-size:30px">

        <div style="padding-top:10px">
        <?php if (empty($_SESSION['ID']) || !isset($_SESSION['ID'])) { ?>
            <a href="/index.php" ><h4><span style="color:white">Login or Sign Up</span></h4></a></h4>
            <?php } else { ?>
            <a href="javascript:history.go(-1)">Go Back</a>
        <?php } ?>
        </div>

        <h4>
            Direct questions to:<br/>
            <a href="mailto:info@rapportbook.com" style="color:red;font-weight: bold;font-size:20px;text-shadow: -1px 0 white, 0 1px white, 1px 0 white, 0 -1px white;">info@rapportbook.com</a>
        </h4>

        <span style="color:white;font-weight: bold;text-shadow: -1px 0 blue, 0 1px blue, 1px 0 blue, 0 -1px black;">Office Information:</span>
        <br/>
            <span style="font-weight:bold;color:deepskyblue;text-shadow: -1px 0 white, 0 1px white, 1px 0 white, 0 -1px white;">
                <span style="font-size: 30px;color:red;">
                    Headquarters
                </span>
            <br/>
                <span style="font-size:25px">
            911 Washington Ave
            <br/>
            Suite 501
            <br/>
            St.Louis,MO 63101
            <br>
            USA
                    </span>
</span>

    </div>
</div>


<?php get_footer_files() ?>
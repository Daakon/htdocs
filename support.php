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




<body>

<?php

?>

<div class="container" >


    <div class="col-xs-12 col-md-8 col-lg-8 col-md-offset-2 roll-call">

        <div style="padding-top:10px">
        <?php if (empty($_SESSION['ID']) || !isset($_SESSION['ID'])) { ?>
            <a href="/index.php" ><h4>Login or Sign Up</h4></a></h4>
            <?php } else { ?>
            <a href="/home.php">Back to Service Requests</a>
        <?php } ?>
        </div>

        <h5>
            Please direct all questions & concerns to: <a href="mailto:info@rapportbook.com">info@rapportbook.com</a>
        </h5>

    </div>
</div>


<?php get_footer_files() ?>
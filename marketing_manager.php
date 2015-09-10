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

    <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 ">
        <ul class="list-inline">

            <?php require 'profile_menu.php'; ?>
        </ul>
    </div>

    <div class="col-xs-12 col-md-8 col-lg-8 col-md-offset-2 roll-call">

        <h2>Marketing Manager</h2>
        <?php
        $sql = "SELECT Count(*) As Total FROM Members ";
        $result = mysql_query($sql) or die(mysql_error());
        $rows = mysql_fetch_assoc($result);
        ?>
        <span style="color:green;font-weight:bold">Current Sign Ups</span> : <?php echo $rows['Total']; ?>

    </div>
</div>



<?php
require 'imports.php';


get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];
?>




<body>

<?php

?>

<div class="container" style = "margin-top:-50px">




    <div class="col-xs-12 col-md-8 col-lg-8 col-md-offset-2 roll-call">
        <?php require 'profile_menu.php'; ?>
        <h4>Marketing Manager</h4>
        <?php
        $sql = "SELECT Count(*) As Total FROM Members ";
        $result = mysql_query($sql) or die(mysql_error());
        $rows = mysql_fetch_assoc($result);
        ?>
        <span style="color:#8899a6;font-weight:bold">Current Sign Ups</span> : <?php echo $rows['Total']; ?>

    </div>
</div>


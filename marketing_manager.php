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

<div class="container" style="margin-top:-50px;">

    <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 ">
        <ul class="list-inline">

            <?php require 'profile_menu.php'; ?>
        </ul>
    </div>

    <?php if ($username == get_username($ID)) { ?>
        <style>
            .list-inline {
                margin-top:-20px;
                padding-top: 20px;
            }
        </style>
        <?php
    }
    else { ?>
        <style>
            .list-inline {
                margin-top:-120px;
                padding-top: 15px;
            }
        </style>

        <?php
    }
    ?>

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



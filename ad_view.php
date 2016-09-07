<?php
require 'connect.php';
require 'model_functions.php';
require 'mediaPath.php';
require 'getSession.php';
require 'html_functions.php';
require 'findURL.php';
require 'email.php';
require 'category.php';
require 'getState.php';
get_head_files();
get_header();
$ID = $_SESSION['ID'];
?>

<?php
$adID = $_GET['adID'];
?>

<div class="container">

    <div class="col-xs-12 col-md-10 col-lg-10 col-md-offset-2 roll-call">

        <br/>

        <a href="javascript:history.back();">Go Back</a>

<div style="border:1px solid black;margin-top:10px;padding:10px">


    <?php
        $sql = "SELECT * FROM DisplayAds WHERE ID = $adID ";
        $result = mysql_query($sql) or die(mysql_error());
        $row = mysql_fetch_assoc($result);
        echo $row['Post'];
?>
</div>

        </div>
    </div>
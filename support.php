<?php
require 'imports.php';
get_head_files();
get_header();
$ID = $_SESSION['ID'];
?>




<body class="support">

<?php

?>

<div class="container" >


    <div class="col-xs-12 col-md-8 col-lg-8 col-md-offset-2" class="support-font">

        <div class="padding-top-10">
        <?php if (empty($_SESSION['ID']) || !isset($_SESSION['ID'])) { ?>
            <a href="../" ><h4><span class="white-text">Login or Sign Up</span></h4></a></h4>
            <?php } else { ?>
            <a href="javascript:history.go(-1)">Go Back</a>
        <?php } ?>
        </div>

        <h4>
            Direct questions to:<br/>
            <a href="mailto:info@rapportbook.com" class="support-email">info@rapportbook.com</a>
        </h4>

        <span class="support-header">Office Information:</span>
        <br/>
            <span class="support-header-2">
                <span class="support-header-3">
                    Headquarters
                </span>
            <br/>
                <span class="support-header-4">
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
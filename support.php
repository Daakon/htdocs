<?php
require 'imports.php';
get_head_files();
get_header();
$ID = $_SESSION['ID'];
?>




<body class="index">

<?php

?>

<div class="container" >

    <div class="row" style="background:#e3e3e3;padding:0px;">

    <div class="col-xs-12 col-md-6 col-lg-6" class="support-font ">
        <img src="/images/helpicon.png" width="50" height="50"/>

        <span class="lead bold">Support</span>
        <br/>

        <h4>
            <span>Direct questions to:</span><br/>
            <a href="mailto:info@rapportbook.com">info@rapportbook.com</a>
        </h4>

        <h3>Office Information:</h3>
           Corporate Headquarters
            <br/>
            911 Washington Ave
            <br/>
            Suite 501
            <br/>
            St.Louis,MO 63101
            <br>
            USA

        <div class="padding-top-10">
            <?php if (empty($_SESSION['ID']) || !isset($_SESSION['ID'])) { ?>
                <a href="../" >Login or Sign Up</a>
            <?php } else { ?>
                <a href="javascript:history.go(-1)">Go Back</a>
            <?php } ?>
        </div>

    </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 padding-0">
            <img src="/images/cust-service.jpg" style="height:100%;width:100%;"/>
        </div>


    </div>
</div>


<?php get_footer_files() ?>
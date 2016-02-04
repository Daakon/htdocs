<?php
require 'imports.php';
get_head_files();
get_header();
$ID = $_SESSION['ID'];
?>



<body class="index" style="font-family:Georgia, serif;">

<?php

?>

<div class="container" style="margin-top:-40px;
            padding-top: 0px;">

    <?php if (empty($_SESSION['ID']) || !isset($_SESSION['ID'])) { ?>
        <div class="visible-lg visible-md">
            <a href="../" >Login or Sign Up</a>
        </div>
        <div class="visible-sm visible-xs">
            <a href="/login-mobile.php" >Login or Sign Up</a>
        </div>
    <?php } else { ?>
        <a href="javascript:history.go(-1)">Go Back</a>
    <?php } ?>
</div>

    <div class="row" style="padding:0px;">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 padding-0">
            <img src="/images/support-person.jpg" style="height:100%;width:100%;"/>
        </div>


        <div class="col-xs-12 col-md-6 col-lg-6" style="background:#f1f1f1;border:2px #e3e3e3 solid">
            <img src="/images/helpicon.png" width="40" height="40"/>

            <span class="lead bold">Support</span>
            <br/>

            <h4>
                <span>For questions or help creating content:</span><br/>
                <a href="mailto:info@rapportbook.com">info@rapportbook.com</a>
            </h4>

            <h4>Watch the video to learn more.</h4>
            <video width="320" height="240" poster="/poster/how-it-works-poster.png" autoplay="autoplay" muted controls >
                <source src="/images/Rapportbook-How-It-Works-Lo-Res.mp4" />
            </video>
<br/>
            <b>Corporate Headquarters</b>
            <br/>
            911 Washington Ave
            <br/>
            Suite 501
            <br/>
            St.Louis,MO 63101
            <br>
            USA

    </div>
</div>


<?php get_footer_files() ?>
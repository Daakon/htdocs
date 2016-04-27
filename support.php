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
            padding-top: 0px;padding-left:10px;">

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
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <img src="/images/rules.jpg" style="height:50%;width:50%;"/>
        </div>


        <div class="col-xs-12 col-md-6 col-lg-6" style="padding-left:30px;">


                <h5> Share the coolest photo or video you have on your phone.</h5>
                <h5> Each week, the post with the most likes wins <b>$100</b>.</h5>

            <hr class="hr-line" />


            <br/>

            <h5>Cash payouts are deposited in either your PayPal or Venmo account.</h5>

<hr class="hr-line"/>

            <h4>
                <span>Direct any questions or concerns to:</span><br/>
                <a href="mailto:info@rapportbook.com">info@playdoe.com</a>
            </h4>

            <b>Corporate Headquarters</b>
            <br/>
            911 Washington Ave
            <br/>
            Suite 501
            <br/>
            St.Louis,MO 63101
            <br>
            USA

            <br/><br/>
            <a href="javascript:history.go(-1)">Back</a>
    </div>
</div>


<?php get_footer_files() ?>
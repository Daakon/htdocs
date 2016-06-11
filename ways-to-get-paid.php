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
        <a style="padding-left:20px;" href="javascript:history.go(-1)">Go Back</a>
    <?php } ?>
</div>

<div class="row" style="padding:0px;">


    <div class="col-xs-12 col-md-6 col-lg-6" style="padding-left:25px;padding-right:10px;padding-top:10px;">

        <h4><div style="color:blue;"  onclick="document.getElementById('rules').style.display = 'block';"><img src="/images/money.jpg" height="110" width="150" />Ways To Get Paid</div></h4>

        <div id="rules" style="display:none;">
            <h3>Referrals</h3>
            <li class="display-block" style="padding-bottom:10px;">1. Your money increase based sign up referrals who validate their accounts and share content.</li>
            <li class="display-block" style="padding-bottom:10px;">2. Your referral money is always visible on your home page.</li>
            <li class="display-block" style="padding-bottom:10px;">3. Once you reach 10 referrals, to redeem your money, <a href="/view_messages/redeem">direct message Playdoe Redemption by clicking here.</a></li>

            <h3>100 Likes</h3>
            <li class="display-block" style="padding-bottom:10px;">1. Get 100 Likes on a post and earn $100.</li>
            <li class="display-block" style="padding-bottom:10px;">2. Once you get 100 Likes, go to your profile, share the post and send the link in a message to
                <a href="/view_messages/redeem">Playdoe Redemption.</a></li>

            <h3>Likes</h3>
            <li class="display-block" style="padding-bottom:10px;">1. Earn ¢1 for every like.</li>

            <h3>Comments</h3>
            <li class="display-block" style="padding-bottom:10px;">1. Earn ¢1 for every like and comment.</li>

            <h5 style="font-style: italic">*Minimum $10 accrual needed to redeem payout.</h5>
            <h5 style="font-style: italic">*Cash payouts are deposited in either your PayPal or Venmo account.</h5>

            <lead>*Rewards processing can take 24 to 48 hours.</lead>

            <br/><br/>

            <a href="/terms">Click here to view our terms and policy</a>

            <div style="margin-bottom:20px;margin-top:10px;"  onclick="document.getElementById('rules').style.display = 'none';"><img src="/images/close.png" height="25" width="25"/>
                <!-- adding onclick to hide this element when you click it -->
                Close
            </div>

        </div>

        <hr class="hr-line"/>

        <h4>
            <span>Direct any questions or concerns to:</span><br/>
            <a href="mailto:info@playdoe.com">info@playdoe.com</a>
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

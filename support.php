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
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <img src="/images/rules.jpg" style="height:50%;width:50%;"/>
        </div>


        <div class="col-xs-12 col-md-6 col-lg-6" style="padding-left:50px;">

            <h4><div style="color:blue;"  onclick="document.getElementById('rules').style.display = 'block';">How It Works</div></h4>

            <div id="rules" style="display:none;">
                <li class="display-block" style="padding-bottom:10px;">1. Each status you post and each follower you gain earns you one point each.</li>
                <li class="display-block" style="padding-bottom:10px;">2. Each point is worth ¢25.</li>
                <li class="display-block" style="padding-bottom:10px;">3. You must collect a minimum of 10 points before redeeming your points for cash or a gift card.</i>.</li>
                <li class="display-block" style="padding-bottom:10px;">4. Once you redeem your points, your points are reset to 0.</li>
                <li class="display-block" style="padding-bottom:10px;">3. <b>To redeem your points, direct message '<span style="color:red">Playdoe</span>' and let us know you would like to redeem your points.
                Tell us what you would like in exchange for your points, rather it's cash or a gift card. If you want a gift card, tell us the
                business you want the gift card from. If the gift card is available, we will send the claim code to your Playdoe inbox. If the
                gift card is not available, we will work to provide an ancillary gift card option.</b></li>

                <h5 style="font-style: italic">*Cash payouts are deposited in either your PayPal or Venmo account.</h5>

                <lead>*Rewards processing can take up to 24 hours.</lead>

                <hr class="hr-line"/>
                <h4 style="color:red;">Not Allowed</h4>

                <li class="display-block" style="padding-bottom:10px;">1. Posting of duplicate photos and videos. These posts will be flagged.</li>

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

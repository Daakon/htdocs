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
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <img src="/images/rules.jpg" style="height:50%;width:50%;"/>
        </div>


        <div class="col-xs-12 col-md-6 col-lg-6" style="padding-left:50px;">

            <h4><div style="color:blue;"  onclick="document.getElementById('rules').style.display = 'block';">How It Works</div></h4>

            <div id="rules" style="display:none;">
                <li class="display-block" style="padding-bottom:10px;">1. Your points increase from post likes and sign up referrals.</li>
                <li class="display-block" style="padding-bottom:10px;">2. Your points are always visible on your home page.</li>
                <li class="display-block" style="padding-bottom:10px;">3. The maximum posts you can share a day is 10.</li>
                <li class="display-block" style="padding-bottom:10px;">4. To redeem your points, <a href="/view_messages/redeem">direct message Playdoe Redemption by clicking here.</a></li>
                <li class="display-block" style="padding-bottom:10px;">5. Once you redeem your points, they are reset to 0.</li>

                <h5 style="font-style: italic">*Cash payouts are deposited in either your PayPal or Venmo account.</h5>

                <lead>*Rewards processing can take 24 to 48 hours.</lead>

                <h4><div style="color:blue;"  onclick="document.getElementById('points').style.display = 'block';">View Point System</div></h4>

                <br/>

                <div id="points" style="display:none;padding-right:10px;">
                    <li class="list-group-item list-group-item-danger display-block">1500 Points = $3</li>
                    <li class="list-group-item list-group-item-danger display-block">2500 Points = $5</li>
                    <li class="list-group-item list-group-item-danger display-block">4000 Points = $10</li>
                    <li class="list-group-item list-group-item-danger display-block">9000 Points = $25</li>
                    <li class="list-group-item list-group-item-danger display-block">17000 Points = $50</li>
                    </div>

                <br/>
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

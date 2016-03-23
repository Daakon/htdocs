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
            <br/><br/>

            <h4><div style="color:blue;"  onclick="document.getElementById('hashtag').style.display = 'block';">Hashtag</div></h4>

            <div id="hashtag" style="display:none;">
                <li class="display-block">1. You must post content related to the current hashtag.</li>
                <li class="display-block">2. Posts must contain a photo and or video.</li>
                <li class="display-block">3. The game locks after 100 people have posted content.
                    <i>If you miss the game, you are encouraged to vote for those who made the cut.</i></li>
                <li class="display-block">4. You can only make one post per contest.
                    If you want to post something again you must delete your current post.</li>
                <li class="display-block">5. There are several variables that go into choosing a winner including: <i>originality, votes/likes and overall popularity.</i>.</li>
                <li class="display-block">6. The amount of winners and gift card amounts can vary from game to game.</li>

                <div style="margin-bottom:20px;margin-top:10px;"  onclick="document.getElementById('hashtag').style.display = 'none';"><img src="/images/close.png" height="25" width="25"/>
                    <!-- adding onclick to hide this element when you click it -->
                    Close
                </div>

            </div>

            <h4><div style="color:blue;"  onclick="document.getElementById('piedpiper').style.display = 'block';">Pied Piper</div></h4>

            <div id="piedpiper" style="display:none;">
                <li class="display-block">1. Get 10 followers and receive a $50 Gift Card.</li>

                <div style="margin-bottom:20px;margin-top:10px;"  onclick="document.getElementById('piedpiper').style.display = 'none';"><img src="/images/close.png" height="25" width="25"/>
                    <!-- adding onclick to hide this element when you click it -->
                    Close
                </div>

            </div>


<br/>

            <h4>
                <span>Direct any questions or concerns to:</span><br/>
                <a href="mailto:info@rapportbook.com">info@rapportbook.com</a>
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
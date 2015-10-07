<?php
require 'connect.php';
require 'html_functions.php';
require 'mediaPath.php';
require 'getSession_public.php';
get_head_files();
get_header()
?>


<body>


<div class="container">


    <div class="col-xs-12 col-md-8 col-lg-8 col-md-offset-2 roll-call">
        <div align="left">
            <?php if (empty($_SESSION['ID']) || !isset($_SESSION['ID'])) { ?>
            <a href="/index.php" ><h4>Login or Sign Up</h4></a></h4>
            <?php } else { ?>
            <a href="/home.php">Back to Roll Call</a>
            <?php } ?>

            <h5 style="color:red;">Network And Promote Your Interests.</h5>

                <img src="<?php echo $imagesPath ?>interests-lg.JPG" />


                <p>
                    Rapportbook is a platform that allows people in the same local area
                    to network, promote and discover their interests.
                    You can share information, events, tips, pointers, resources, etc.
                    Expand your social circle by meeting new people with similar commonalities.
                    It's simple, it's easy, it's fun. Share your interests through
                    public posts, comments, photos, videos and private direct messages.
                </p>
            <!--<ul>
                <li style="display:block;font-weight: bold" >&bull; SMS & Email Notification when Service Posts are made related to your primary service</li>
                <li style="display:block;font-weight: bold">&bull; Photo & Video Attachments in Direct Messages</li>
                <li style="display:block;font-weight: bold">&bull; Ability to search other Service Request Categories if you offer multiple services</li>
                <li style="display:block;font-weight: bold">&bull; Profile Photo and Video uploads to Market Your Service</li>
            </ul>-->


            <h4><span style="font-weight:bold;">Our Service is 100% <font color="red">FREE!</font></span></h4>

    </div>
</div>

    <?php get_footer_files() ?>
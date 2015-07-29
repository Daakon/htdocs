<?php
require 'connect.php';
require 'model_functions.php';
require 'mediaPath.php';
require 'getSession_public.php';
require 'html_functions.php';


get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];
?>




<body>

<?php

?>

<div class="container" >


    <div class="col-xs-12 col-md-8 col-lg-8 col-md-offset-2 roll-call">

        <div style="padding-top:10px">
        <?php if (empty($_SESSION['ID']) || !isset($_SESSION['ID'])) { ?>
            <a href="/index.php" ><h4>Login or Sign Up</h4></a></h4>
            <?php } else { ?>
            <a href="/home.php">Back to Roll Call</a>
        <?php } ?>
        </div>
        
        <img src="<?php echo $imagesPath ?>faq.jpg" class="img-responsive" />

        <br/>

<h4>How long can my video be?</h4>
        <small>
            Videos typically can be up to 10 minutes long.
        </small>


        <h4>What type of videos can I post</h4>
        <small>
            Anything you believe shows who you are as a person.
        </small>

        <h4>Does the video have to be mine?</h4>
        <small>
            You have to have rights to the video.
            Typically, it should be something you recorded.
        </small>

        <h4>How do I report content that is offensive, abusive or illegal</h4>
        <small>
            Contact us immediately at <a href="mailto:info@rapportbook.com">info@rapportbook.com</a> and include
            your username as well as any details you can provide concerning the issue at hand.
        </small>

        <h4>I'm experiencing technical difficulties</h4>
        <small>
            Contact us at <a href="mailto:info@rapportbook.com">info@rapportbook.com</a> and include
            your user name and the issue that you are experiencing.
        </small>

        <h4>Media Requests</h4>
        <small>
            Contact us at <a href="mailto:marketing@rapportbook.com">marketing@rapportbook.com</a>
        </small>

    </div>
</div>


<?php get_footer_files() ?>
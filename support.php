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
        
        <img src="<?php echo $images ?>faq.jpg" class="img-responsive" />

        <br/>

<h4>How long can my video be?</h4>
        <small>
            Videos typically can be up to 10 minutes long.
        </small>

        <h4>How private are my photos/videos</h4>
        <small>
             Photos & videos that are marked as private indicate that you have not uploaded
        them in Roll Call so the general public cannot see them. If you text your profile to
        someone, then they will be able to see those photos and/or video.
        </small>

        <h4>What type of content can I post</h4>
        <small>
            Anything you believe is a photo or video that depicts talent.
        </small>

        <h4>Does the video have to be mine?</h4>
        <small>
            No, the video does not have to be yours but you do have to have permission from
            the lawful owner of the video to post it.
        </small>

        <h4>What type of content can I not post?</h4>
            <small>
                Here is a list of content that is under no means tolerable:
                <ul>
                    <li style="display:block" class="list-group-item list-group-item-danger">Pornographic material of any nature</li>
                    <li style="display:block" class="list-group-item list-group-item-danger"><font color="red">*</font>Nude art will be at the discretion of our content team.</li>
                    <li style="display:block" class="list-group-item list-group-item-danger">Any video committing a crime</li>
                </ul>
                Any of these offenses can result in account suspension and/or termination as well as contacting proper authorities.
            </small>

        <h4>How do I report content that is offensive, abusive or illegal</h4>
        <small>
            Contact us immediately at <a href="mailto:info@rapportbook.com">info@rapportbook.com</a> and include
            your username as well as the full name of the person causing the issue along with
            the description of the issue.
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
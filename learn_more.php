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

            <h4>Rapportbook helps people find the servies they need.</h4>



                <img src="<?php echo $imagesPath ?>services.jpg" />


                <p>
                    <b>Service Seekers</b>
                    When you sign up, you select "I am looking for a service".
                    Once you sign up, you simply post what you are looking for from
                    the drop down along with text that describes what you are looking for.
                    You can also add a photo or video to better describe your problem if it helps.

                    <br/>
                    Once you hit post, a notification via email and SMS text will got out to all service providers
                    who focus on the service you need. The service providers will then come back
                    to Rapportbook and return your message. From there, you and the service provider
                    will be able to discuss the problem at hand. You can choose to talk to as many
                    services providers as possible before making your decision on who you will work with.

                    <br/>
                    All of your service requests will be stored in your profile and be viewable by service
                    providers once they start a conversation with you. You can delete any of these requests at any time.
                </p>

                <p>
                    <b>Service Providers</b>
                    When you sign up, you select "I am providing a service".
                    Once you sign up, you can scroll through all service requests,
                    even the ones you don't specialize in. But you will receive notifications
                    whenever their is a post related to your primary focus.
                    You can then respond to the request. Service providers can also post
                    a profile video along with about us text to better explain what your service does.
                </p>

            <h4><span style="font-weight:bold;">Our Service is 100% <font color="red">FREE!</font></span></h4>

    </div>
</div>

    <?php get_footer_files() ?>
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

            <h4>Rapportbook allows people to find the services they are looking for.</h4>



                <img src="<?php echo $imagesPath ?>services.jpg" class="how-it-works-img"/>


                <p>
                    <b>Service Seekers</b>
                    When you sign up, you select "I am looking for a service".
                    Once you sign up, you simply post what you are looking for from
                    the drop down along with text that describes what you are looking for.
                    You can also add a photo or video to better describe you problem if it helps.

                    <br/>
                    Once you hit post, a notification via email and text will got out to all service providers
                    who focus on the service you need. The service providers will then come back
                    to Rapportbook and return you message. From there, you and the service provider
                    will be able to discuss the problem at hand. You can choose to talk to as many
                    services providers as possible before making your decision on who you will work with.

                    <br/>
                    All of your service requests will be stored in your profile a viewable by service
                    providers once they start a conversation with you. You can delete any of these at any time.
                </p>

                <p>
                    <b>Service Providers</b>
                    When you sign up, you select "I am providing a service".
                    Once you sign up, you can scroll through all service requests,
                    even the ones you don't specialize in. But you will receive notifications
                    whenever their is a post related to you primary focus.
                    You can then respond to the request. Service providers can also post
                    a profile video to better explain what their service does. You will also have
                    a library of videos for service seekers to explore.
                </p>

    </div>
</div>

    <?php get_footer_files() ?>
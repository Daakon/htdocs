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

            <h4>Rapportbook allows people to find services they are looking for.</h4>



                <img src="<?php echo $imagesPath ?>services.jpg" class="how-it-works-img"/>


                <p>
                    <b>We have the real time feed you're accustomed to in Social Networks.
                    But we removed all the photos and replaced them with video...why?</b>
                    Video is the now and the future, plus...
                    the best way to build rapport with someone is by being able
                    to see all of their mannerisms, see what they look like today.
                    It helps avoid fraudulent representation. Video is more telling and descriptive.
                    You don't even have a profile photo, you have a profile video.
                </p>

            <p>
                <b>So how do you engage?</b>
                When you go through the real time feed, if you see a video you like,
                you can approve(like) their post and or you can direct message
                them to get the conversation going. You can also check out their profile
                and see what other videos a person has posted as well as other posts they made.
            </p>

            <p>
                <b>You can search the real time feed by age as well as post topic.</b>
                Basically, each post is tagged by an interest topic,
                so you can filter by topics that interest you and see if someone
                shares similar interests as you.
            </p>

    </div>
</div>

    <?php get_footer_files() ?>
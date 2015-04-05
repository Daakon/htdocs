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

        <div class="row how-it-works-row">

            <div class="col-xs-12 col-md-5 col-md-offset-1">

                <img src="<?php echo $images ?>female-singer.jpg" class="how-it-works-img"/>
            </div>
            <div class="col-xs-12 col-md-6">
                <span class="how-it-works-header">Videos</span>

                <p>
                    Rapportbook allows you to post videos showcasing your talent in Roll Call.
                    Video is the ultimate way to display your talent to the world.
                    Upload up to 5 minutes of entertaining video showing everyone the talent you've been given.
                    Let our community engage with your content by approving it, commenting on it,
                    and direct messaging you to give you more props!
                </p>

            </div>
        </div>

        <!---------------------------------------------------------------->

        <div class="row ">
            <div class="col-xs-12 col-md-5 col-md-offset-1"> 
                <img src="<?php echo $images ?>dance.jpg" class="how-it-works-img"/> 
            </div>
             
            <div class="col-xs-12 col-md-6"> 
                <span class="how-it-works-header">Photos</span> 
                <p>
                    There is nothing better than enjoying a great photo from
                    a phenomenal performance. Still shots  can be just as
                    climactic as video. When you get that great shot of a
                    fabulous performance, post it  with a caption in Roll Call so everyone
                    can get a sense and feel of what it was like to  be there.

                </p>
                 
            </div>
             
        </div>

        <!-------------------------------------------------------------->

        <div class="row">

            <div class="col-xs-12 col-md-5 col-md-offset-1"> 
                <img src="<?php echo $images ?>ad-pic.jpg" class="how-it-works-img" align="left"/> 
            </div>
              
            <div class="col-xs-12 col-md-6"> 
                <span class="how-it-works-header">Advertise</span> 
                <p>
                    Do you need to get the word out about an upcoming event or an artistic service that the talented members on
                    Rapportbook may be interested in? Well advertise with us.
                    Simply post an ad in Roll Call to let everyone know about your event
                    or service. Post a video/commercial or picture to help paint the picture
                    of what you have going on.
                </p>
            </div>
        </div>

        <!--------------------------------------------------------------------->


        <div class="row">

            <div class="col-xs-12 col-md-5 col-md-offset-1"> 
                <img src="<?php echo $images ?>watching-video.jpeg" class="how-it-works-img" align="left"/> 
            </div>
              
            <div class="col-xs-12 col-md-6"> 
                <span class="how-it-works-header">Engagement</span>
                <p>
                    At the end of the day, when you share your talent, whether through photo or video,
                    you want  someone to engage with your content. This is what our community
                    is all about. Once you post  in Roll Call, our community will be delighted
                    to comment on your work, approve it and  direct message you. You can
                    always take it a step further and text your profile  to someone,
                    so they can check out your entire body of work; and the cool thing is,
                    they don't even have to be signed  up on our site to see it.
                    With the tools we've built, you will be able to build  rapport fast
                    with people, and after that, who knows.
                </p>
            </div>
            <div align="center"><a href="/index.php" ><h4>Login or Sign Up</h4></a></div>
        </div>
        <!--------------------------------------------------------------------->

    </div>
</div>
</body>
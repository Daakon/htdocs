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

                <img src="<?php echo $images ?>video.png" class="how-it-works-img"/>
            </div>
            <div class="col-xs-12 col-md-6">
                <span class="how-it-works-header">Videos</span>

                <p>
                    Videos are multi-media rich, compelling and extremely engaging.
                    Nothing catches a person's attention more than a video.
                    Rapportbook allows you to post videos up to 10 minutes long.
                </p>

            </div>
        </div>

        <!---------------------------------------------------------------->

        <div class="row ">
            <div class="col-xs-12 col-md-5 col-md-offset-1"> 
                <img src="<?php echo $images ?>camera.png" class="how-it-works-img" align="left"/> 
            </div>
             
            <div class="col-xs-12 col-md-6"> 
                <span class="how-it-works-header">Photos</span> 
                <p>
                    A great photo is one that shares your talent or interests.
                    Tell your story today by uploading a pic!
                </p>
                 
            </div>
             
        </div>

        <!-------------------------------------------------------------->

        <div class="row ">
            <div class="col-xs-12 col-md-5 col-md-offset-1"> 
                <img src="<?php echo $images ?>audio.png" class="how-it-works-img" align="left"/> 
            </div>
             
            <div class="col-xs-12 col-md-6"> 
                <span class="how-it-works-header">Audio</span> 
                <p>
                    Have any original audio files you would like to share?
                    Feel free to upload them.
                    Music, inspirational talks, sound bites, etc.
                </p>
                 
            </div>
             
        </div>

            <!-------------------------------------------------------------->

        <div class="row">

            <div class="col-xs-12 col-md-5 col-md-offset-1"> 
                <img src="<?php echo $images ?>promotion.png" class="how-it-works-img" align="left"/> 
            </div>
              
            <div class="col-xs-12 col-md-6"> 
                <span class="how-it-works-header">Promote</span> 
                <p>
                    Do you need to get the word out about something?
                    Simply post your promotion in Roll Call to let everyone know.
                    Add a video or picture to make your post even better.
                </p>
            </div>
        </div>

        <!--------------------------------------------------------------------->

    </div>
</div>

    <?php get_footer_files() ?>
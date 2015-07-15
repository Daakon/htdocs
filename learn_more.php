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

            <h4>Rapportbook is a multi-media rich one stop web shop that you can use for anything.</h4>

        <div class="row how-it-works-row">

            <div class="col-xs-12 col-md-5 col-md-offset-1">

                <img src="<?php echo $imagesPath ?>video.png" class="how-it-works-img"/>
            </div>
            <div class="col-xs-12 col-md-6">
                <span class="how-it-works-header">Videos</span>

                <p>
                   Watch and Upload Videos
                </p>

            </div>
        </div>

        <!---------------------------------------------------------------->

        <div class="row ">
            <div class="col-xs-12 col-md-5 col-md-offset-1"> 
                <img src="<?php echo $imagesPath ?>camera.png" class="how-it-works-img" align="left"/> 
            </div>
             
            <div class="col-xs-12 col-md-6"> 
                <span class="how-it-works-header">Photos</span> 
                <p>
                    View & Upload Photos
                </p>
                 
            </div>
             
        </div>

        <!-------------------------------------------------------------->

            <div class="row ">
                <div class="col-xs-12 col-md-5 col-md-offset-1"> 
                    <img src="<?php echo $imagesPath ?>video-game.png" class="how-it-works-img" align="left"/> 
                </div>
                 
                <div class="col-xs-12 col-md-6"> 
                    <span class="how-it-works-header">Games</span> 
                    <p>
                        Play Games
                    </p>
                     
                </div>
                 
            </div>

            <!-------------------------------------------------------------->

        <div class="row ">
            <div class="col-xs-12 col-md-5 col-md-offset-1"> 
                <img src="<?php echo $imagesPath ?>audio.png" class="how-it-works-img" align="left"/> 
            </div>
             
            <div class="col-xs-12 col-md-6"> 
                <span class="how-it-works-header">Music</span> 
                <p>
                    Listen to and Upload Music
                </p>
                 
            </div>
             
        </div>

            <!-------------------------------------------------------------->

        <div class="row">

            <div class="col-xs-12 col-md-5 col-md-offset-1"> 
                <img src="<?php echo $imagesPath ?>promotion.png" class="how-it-works-img" align="left"/> 
            </div>
              
            <div class="col-xs-12 col-md-6"> 
                <span class="how-it-works-header">Promote</span> 
                <p>
                    Discover and Post new Events, Services and more.
                </p>
            </div>
        </div>

        <!--------------------------------------------------------------------->

            <div class="row">

                <div class="col-xs-12 col-md-5 col-md-offset-1"> 
                    <img src="<?php echo $imagesPath ?>help.png" class="how-it-works-img" align="left"/> 
                </div>
                  
                <div class="col-xs-12 col-md-6"> 
                    <span class="how-it-works-header">Jobs</span> 
                    <p>
                        Find Jobs
                    </p>
                </div>
            </div>

            <!--------------------------------------------------------------------->

            <div class="row">

                <div class="col-xs-12 col-md-5 col-md-offset-1"> 
                    <img src="<?php echo $imagesPath ?>sale.png" class="how-it-works-img" align="left"/> 
                </div>
                  
                <div class="col-xs-12 col-md-6"> 
                    <span class="how-it-works-header">Sale Stuff</span> 
                    <p>
                        Post an AD to sell just about anything you want.
                    </p>
                </div>
            </div>

            <!--------------------------------------------------------------------->

            <div class="row">

                <div class="col-xs-12 col-md-5 col-md-offset-1"> 
                    <img src="<?php echo $imagesPath ?>question-mark.jpeg" class="how-it-works-img" align="left"/> 
                </div>
                  
                <div class="col-xs-12 col-md-6"> 
                    <span class="how-it-works-header">Ask Questions</span> 
                    <p>
                        Get an answers to questions you might have.
                    </p>
                </div>
            </div>

            <!--------------------------------------------------------------------->

            <div class="row">

                <div class="col-xs-12 col-md-5 col-md-offset-1"> 
                    <img src="<?php echo $imagesPath ?>private.jpg" class="how-it-works-img" align="left"/> 
                </div>
                  
                <div class="col-xs-12 col-md-6"> 
                    <span class="how-it-works-header">Share Private Content</span> 
                    <p>
                        Share Content with only the people you wish to.
                    </p>
                </div>
            </div>

            <!--------------------------------------------------------------------->
    </div>
</div>

    <?php get_footer_files() ?>
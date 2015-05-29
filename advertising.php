<?php
require 'connect.php';
require 'model_functions.php';
require 'mediaPath.php';
require 'getSession.php';
require 'html_functions.php';

require 'findURL.php';

require 'email.php';
require 'category.php';

get_head_files();
get_header();

?>

    <body>


<div class="container">


    <div class="col-xs-12 col-md-12 col-lg-12 roll-call" >
        <div class="row" style="padding:10px;">

                <a href="/home.php">Back to Roll Call</a>

                    <img src="<?php echo $images ?>ad-pic.jpg" style="border-bottom:1px solid black;" />

            <br/>
</div>
        <div class="row" style="padding:10px;">
                    <span class="how-it-works-header"><h2>Advertise</h2></span>

                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <img src="<?php echo $images ?>Placing-ads.gif" height="auto" width="100%" style="max-width:200px;max-height:200px" class="img-responsive" />
                        <h3>1. Place An Ad</h3>
                    </div>

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <img src="<?php echo $images ?>demographics.jpg" height="auto" width="100%" style="max-width:200px;max-height:400px" class="img-responsive" />
                <h3>2. Choose demographics</h3>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <img src="<?php echo $images ?>publish.jpg" height="auto" width="100%" style="max-width:200px;max-height:400px" class="img-responsive" />
                <h3>3. Publish your AD.</h3>
            </div>
    </div>

        <div class="row">
            <a href="/ad-manager.php">
            <div align = "center" style="background-color:red;border-radius:10px;width:100px;color:white;padding:10px;margin-left:50px;">Manage Ads</div>
            </a>
</div>

<?php get_footer_files() ?>
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

<style>

    body {
        background: url(images/revenue-image.jpg) no-repeat fixed;
        -webkit-background-size: 110% 100%;
        -moz-background-size: 110% 100%;
        -o-background-size: 110% 100%;
        background-size: 110% 100%;
    }
</style>

    <body>

    <div class="container-fluid" >

        <div class="row" >


            <div class="col-lg-12 col-md-12 " >


               <h4>Create a Display Ad</h4>

                <a href="/manage_ad"><h4 style="color:red;">Start Here</h4></a>

            </div>
<?php get_footer_files() ?>
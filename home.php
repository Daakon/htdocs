<?php
require 'html_functions.php';
get_head_files();
get_header();
?>

<div class="container" style="background-color:red;padding:40px;">
    <div class="row">
        <div class="col-xs-12 roll-call">
            <h2>Roll Call</h2>

            <form  method= "post" enctype ="multipart/form-data" action = "" >
                Attach Photo/Video To Your Post &nbsp;
                <input type= "file" name = "flBulletinPhoto" id = "flBulletinPhoto"  />
            <input type="text" name="post" id="post" class="input-style" placeholder="Share Your Talent"/>

            </form>
        </div>
    </div>


</div>
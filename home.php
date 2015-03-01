<?php
require 'connect.php';
require 'html_functions.php';
get_head_files();
get_header();

// handle roll call post
$post = mysql_real_escape_string($_POST['post']);
if (issert($_POST['post'])) {

}
?>

<div class="container" style="background-color:red;padding:40px;">
    <div class="row">
        <div class="col-xs-12 roll-call">
            <img src="images/roll-call.gif" height="150px" width="150px" alt="Roll Call" />
            <br/>
            <form  method= "post" enctype ="multipart/form-data" action = "" >
                <img src="images/image-icon.png" height="30px" width="30px" alt="Photos/Video" />
                <strong>Attach Photo/Video To Your Post</strong> &nbsp;
                <input type= "file" name = "flPostPhoto" id = "flPostPhoto"  />
            <input type="text" name="post" id="post" class="input-style" placeholder="Share Your Talent"/>

            </form>
        </div>
    </div>


</div>
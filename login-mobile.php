<?php
require 'connect.php';
require 'html_functions.php';
require 'mediaPath.php';
require 'findURL.php';
require 'model_functions.php';

get_head_files();
get_header();

?>

<div class="container">

    <div class="col-xs-12 hidden-sm hidden-md hidden-lg">
 <h2>Login</h2>

        <form method="post" action="login.php" >
    <label class="sr-only" for="generic">Your Email address or Username</label>
    <input type="text" name="generic" id="generic" placeholder="User Name or Email" class="form-control"/>
    <br/>
    <label class="sr-only" for="login_password">Password</label>
    <input type="password" name="login_password" id="login_password" placeholder="Password"
           class="form-control"/>
    <br/>
    <input type="submit" name="login" id="login" value="Login" class="btn btn-default"/>
            <br/><br/>
            <a href="forgot-password.php" style="color:black;margin-top:10px;">Forgot Your Password?</a>
    <br/>
</form>
</div>

        </div>

</div>
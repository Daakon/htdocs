<?php
require 'imports.php';

get_head_files();
get_header();

?>

<div class="container">

    <div class="col-xs-12">
 <h2>Login</h2>

        <form method="post" action="login.php" >
    <label class="sr-only" for="login_email">Email</label>
    <input type="text" name="login_email" id="login_email" placeholder="Email" class="form-control"/>
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

        <h4><a href="/learn_more">Sign Up</a></h4>
</div>

        </div>

</div>
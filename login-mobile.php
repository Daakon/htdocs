<?php
require 'imports.php';

get_head_files();
get_header();

?>

<body style="background:white;">

<div class="container containerFlush">

    <div class="col-xs-12">

        <img src="/images/Playdoe-Logo.png" height="50" width="50" />
        <span class="lead bold">Log in</span>
        <br/><br/>

        <form method="post" action="login.php" >
            <label class="sr-only" for="login_email">Email</label>
            <input type="text" name="login_email" id="login_email" placeholder="Email" class="form-control" style="border:1px solid #8899a6;" />
            <br/>
            <label class="sr-only" for="login_password">Password</label>
            <input type="password" name="login_password" id="login_password" placeholder="Password" style="border:1px solid #8899a6;"
                   class="form-control"/>
            <br/>
            <input type="submit" name="login" id="login" value="Log in" class="btn btn-default" style="background: #E30022;color:white;"/>
            <br/><br/>
            <a href="forgot-password" style="color:black;margin-top:10px;">Forgot Your Password?</a>
            <br/>
        </form>

        <h4><a href="/learn_more#signup">Sign Up</a></h4>
    </div>

</div>

</div>
</body>
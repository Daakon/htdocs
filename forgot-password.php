<?php
require 'imports.php';
get_head_files();
get_header();
?>




<body>

<div class="container" >

    <?php

    if (isset($_POST['submit']) && ($_POST['submit'] == "Reset Password")) {

        require 'connect.php';
        $email = trim($_POST['email']);

        $sql = "SELECT ID, Email FROM Members WHERE (Email = '$email') ";


        $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting Email from Members"));
        $rows = mysql_fetch_assoc($result);

        $toId = $rows['ID'];
        $pass = $rows['Password'];


        // query results
        if (mysql_num_rows($result) == 0) {

            echo "<script>alert('Your username or email was not found');</script>";
            echo "<script>alert('test');</script>";
        } // if email was found

        else {
            if (build_and_send_email(1, $toId, 5, '', '', '')) {
                echo "<script>alert('Your password reset has been sent to your email on file');location='./'</script>";
            }

        }
    }

    ?>

    <div class="col-xs-12 col-md-8 col-lg-8 col-md-offset-2">

        <form action="" method="post">
            <div class="form-group">
                <img src="/images/Rapportbook-Logo.png" height="50" width="50" />
                <span class="lead bold">Reset Password</span>
                <br/><br/>
                <input type="text" class="form-control" id="email" name="email" placeholder="Email"/>
            </div>

            <input type="submit" id="submit" name="submit" value="Reset Password" class="btn btn-default"/>
        </form>

        <br/>
       <div class="visible-xs">
           <a href="login-mobile">Log in</a>
       </div>
        <div class="hidden-xs">
            <a href="../">Log in</a>
        </div>

    </div>
</div>



</body>
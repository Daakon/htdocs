<?php
require 'imports.php';
get_head_files();
get_header();
?>




<body>

<div class="container" >


    <div class="col-xs-12 col-md-8 col-lg-8 col-md-offset-2">

        <form action="" method="post">
            <div class="form-group">
                <img src="/images/Rapportbook-Logo.png" height="50" width="50" />
                <span class="lead bold">Reset Password</span>
                <br/><br/>
                <label for="email">Email</label>
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

<?php

if (isset($_POST['submit']) && ($_POST['submit'] == "Reset Password")) {

    require 'connect.php';
    $email = trim($_POST['email']);

    $sql = "SELECT * FROM Members WHERE (Email = '$email') ";


    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);


    $pass = $rows['Password'];


    // query results
    if (mysql_numrows($result) == 0) {

        echo "<div align ='center' style='color:red;font-weight:bold;'>Your username or email was not found</div>";
    } // if email was found

    else {

        require 'model_functions.php';
        require 'email.php';

        $toId = $rows['ID'];
        if (build_and_send_email(1, $toId, 5, '')) {
            echo "<script>alert('Your password reset has been sent to your email on file');</script>";
        }

    }
}

?>

</body>
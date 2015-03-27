<?php
require 'connect.php';
require 'html_functions.php';
get_head_files();
?>




<body>

<?php
get_header()
?>

<div class="container" style="background:white;padding:20px;border:1px solid black;border-radius:10px;">
    <div class="col-lg-offset-2 col-md-offset-2">

    </div>

    <div class="col-xs-12 col-md-10 col-lg-10">

        <form action="" method="post">
            <div class="form-group">
                <h2>Recover Your Password</h2>
                <label for="email">Email</label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Email"/>
            </div>

            <input type="submit" id="submit" name="submit" value="Recover Password" class="btn btn-default"/>
        </form>

    </div>
</div>

<?php

if (isset($_POST['submit']) && ($_POST['submit'] == "Recover Password")) {

    require 'connect.php';
    $email = trim($_POST['email']);

    $sql = "SELECT * FROM Members WHERE (Email = '$email' OR Username = '$generic') ";


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
        if (build_and_send_email(1, $toId, 4, '')) {
            echo "<script>alert('Your password has been sent to your email on file');</script>";
        }

    }
}

?>

</body>
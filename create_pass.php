<?php
require 'connect.php';
require 'model_functions.php';
require 'mediaPath.php';
require 'getSession_public.php';
require 'html_functions.php';

require 'findURL.php';

require 'email.php';

get_head_files();
get_header();
?>

<body>


<div class="container" >
    <div class="col-lg-offset-2 col-md-offset-2 col-lg-8 col-md-8 roll-call">


        <h1><div style = "color:red;">Create A New Password</div></h1>

        <br/>
        Enter your new password
        <br/><br/>
        <form method = "post" action = "" >
            <input type = "text" name = "newPass" id = "newPass" style = "width:270px;" />
            <br/>
            <input type = "hidden" name = "email" id = "email" value = "<?php echo $_GET['email'] ?>" />
            <input type = "hidden" name = "acctType" id = "acctType" value = "<?php echo $_GET['aType'] ?>" />
            <br/>
            <input type = "submit" name = "createPass" id = "createPass" value = "Create Password" />
        </form>

        <?php
        if (isset($_POST['createPass']) && $_POST['createPass'] == "Create Password" && strlen($_POST['createPass']) > 2) {
            require 'connect.php';
            $email = $_POST['email'];
            $pass = $_POST['newPass'];


                $sql = "UPDATE Members SET password = '".md5($pass)."' WHERE email = '$email' ";
                mysql_query($sql) or die(mysql_error());
                echo 'Your password has been updated';

        }

        ?>


</div>



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
</div>



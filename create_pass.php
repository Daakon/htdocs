<?php
require 'imports.php';
get_head_files();
get_header();

// do not allow user to tamper with email in query string

if (empty($_SESSION['EMAIL']) && !isset($_SESSION['EMAIL'])) {
    if (empty($count)) {
        $_SESSION['EMAIL'] = $_GET['email'];
    }
}

?>

<body>


<div class="container" >
    <div class="col-lg-offset-2 col-md-offset-2 col-lg-8 col-md-8 roll-call">


        <img src="/images/Rapportbook-Logo.png" height="50" width="50" />
        <span class="lead bold">Enter your new password</span>
        <br/><br/>

        <form method = "post" action = "" >
            <input type = "password" name = "newPass" id = "newPass" style = "width:270px;" />
            <br/>
            <input type = "hidden" name = "email" id = "email" value = "<?php echo $_GET['email'] ?>" />
            <br/>
            <input type = "submit" name = "createPass" id = "createPass" value = "Create Password" />
        </form>

        <?php
        if (isset($_POST['createPass']) && $_POST['createPass'] == "Create Password" && strlen($_POST['createPass']) > 2) {
            require 'connect.php';
            $email = $_POST['email'];
            $pass = $_POST['newPass'];

            if ($_SESSION['EMAIL'] == $email) {

                $sql = "UPDATE Members SET password = '" . md5($pass) . "' WHERE email = '$email' ";
                mysql_query($sql) or die(mysql_error());
                echo '

                Your password has been updated <br/>
                <div class="visible-xs">
                        <a href="/login-mobile.php">Login</a>
                    </div>

                    <div class="hidden-xs">
                        <a href="../">Login</a>
                    </div>

                    ';

            }
            else { echo "Something does not seem right, we could not update your password"; }
        }
        ?>


</div>

    <?php $count = 2; ?>


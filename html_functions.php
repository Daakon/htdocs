<?php

function get_head_files() { ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head lang="<?php echo $str_language; ?>" xml:lang="<?php echo $str_language; ?>">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width-device-width", initial-scale="1">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--JQuery CDN-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

    <!-- Bootstrap Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

    <!--Bootstrap Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

    <!--Bootstrap Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

    <!--Custom css -->
    <link href="site.css" rel="stylesheet" type="text/css" />

    <title>Rapportbook</title>


<?php } ?>


    <?php function get_login_header() { ?>

    <header class="navbar navbar-default navbar-static-top header">
        <img src="images/Rapportbook-Logo-White-Text-Large.png" alt="Rapportbook" height="90px;" width="200px;" style="margin-top:-30px;" />
    <span class="pull-right">
        <form method="post" action="login.php">
            <input type ="text" name="generic" id="generic" placeholder="User Name or Email" style="color:black;" />
            <input type="password" name="password" id="password" placeholder="Password" style="color:black;" />
            <input type="submit" name="login" id="login" value="Login" class="login-button" />
        </form>

        <a href="forgot-passoword.php" style="color:white;font-size:12px;padding-left:100px;">Forgot Your Password</a>
    </span>
    </header>

<?php } ?>

<?php

function get_header() { ?>
    <header class="navbar navbar-default navbar-static-top header">
        <img src="images/Rapportbook-Logo-White-Text-Large.png" alt="Rapportbook" height="90px;" width="200px;" style="margin-top:-30px;" />
    </header>

<?php } ?>
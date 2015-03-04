<?php

function get_head_files() { ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head lang="<?php echo $str_language; ?>" xml:lang="<?php echo $str_language; ?>">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

    <!--Bootstrap Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

    <!-- Custom stylesheet, located in resources/css -->    
    <link rel="stylesheet" href="/resources/css/site.css">

    <title>Rapportbook</title>
</head>

<?php } ?>


    <?php function get_login_header() { ?>

    <header class="navbar navbar-default navbar-static-top header">
        <img src="images/Rapportbook-Logo-White-Text-Large.png" alt="Rapportbook" height="40" width="315" class="logo-image" />
    <span class="pull-right">
        <form method="post" action="login.php" class="form-inline">
            <div class="form-group">
            <label class="sr-only" for="generic">Your Email address or Username</label>
            <input type ="text" name="generic" id="generic" placeholder="User Name or Email" class="form-control" />
            <label class="sr-only" for="login_password">Password</label>
            <input type="password" name="login_password" id="login_password" placeholder="Password" class="form-control" />
            <input type="submit" name="login" id="login" value="Login" class="login-button btn" />
            </div>
            <a href="forgot-password.php" class="forgot-password-link">Forgot Your Password?</a>
        </form>
    </span>




    </header>

<?php } ?>

<?php

function get_header() { ?>
    <header class="navbar navbar-default navbar-static-top header">
        <img src="images/Rapportbook-Logo-White-Text-Large.png" alt="Rapportbook" height="40" width="315" class="logo-image" />



    </header>


<?php } ?>

<?php function get_footer_files() { ?>


    <!--JQuery CDN-->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

    <!--Bootstrap Latest compiled and minified JavaScript-->
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

    <!--Local JS file-->
    <script type="text/javascript" src="/resources/js/site.js"></script>
    </body>
</html>


<?php } ?>
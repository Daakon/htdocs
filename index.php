<?php

?>

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

</head>

<body>

<header class="navbar navbar-default navbar-static-top header">
            <strong>Rapportbook</strong>
   </header>

<div class="container-fluid">


    <div class="row">
        <div class = "col-sm-4 hidden-xs " >
           <img src="images/college-kids-texting.jpg" height="500px" width="500px" alt="" style="padding-right:70px;" />
        </div>

        <div class="col-sm-8 col-xs-12 form ">

            <!--Login div -->
            <div id="login">
                <h2>Login</h2>

                <h3>Click here to see how it works</h3>

            <form>

                    <label class="form-td" for="firstName">First Name </label>

                    <input class="form-td" type="text" name="firstName" id="firstName" />
                    <br/>

                    <label class="form-td" for="lastName">Last Name</label>

                    <input class="form-td" type="text" name="lastName" id="lastName" />

                    <br/>
                    <label class="form-td" for="email">Email</label>

                    <input class="form-td" type="email" name="email" id="email" />

                    <br/>
                    <label class="form-td" for="password">Password</label>

                    <input class="form-td" type="password" name="password" id="password" />

            </form>
        </div><!--end of login div -->

            <!--Sign up area -->
            <div id="signup">
                <h2>Sign Up</h2>

                <h3>Click here to see how it works</h3>
            </div> <!--End of sign up-->

            </div>
    </div>

</div>

</body>
</html>



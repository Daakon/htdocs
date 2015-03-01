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
        <div class = "col-sm-4" style="border:1px solid black;">
            Image
        </div>

        <div class="col-sm-8 ">
            <form>
                <table>
                    <tr>
                        <td>
                            <label for="firstName">First Name </label>
                        </td>
                        <td>
                            <input type="text" name="firstName" id="firstName" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="lastName">Last Name</label>
                        </td>
                        <td>
                            <input type="text" name="lastName" id="lastName" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="email">Email</label>
                        </td>
                        <td>
                            <input type="email" name="email" id="email" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="password">Password</label>
                        </td>
                        <td>
                            <input type="password" name="password" id="password" />
                        </td>
                    </tr>
                </table>



            </form>
        </div>
    </div>
</div>

</body>
</html>
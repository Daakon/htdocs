<?php
session_start();
function get_head_files()
{

?>

    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head lang="<?php echo $str_language; ?>" xml:lang="<?php echo $str_language; ?>">


        <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
        <meta name="description" content="Play Games and Shop on Us!">
        <meta name="keywords" content="<?php echo $keywords ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />


        <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
        <script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <script src="/resources/js/site.js"></script>

        <!-- Bootstrap Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

        <!--Bootstrap Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

        <!-- Custom stylesheet, located in resources/css -->
        <link rel="stylesheet" href="/resources/css/site.css">




<script>jQuery.noConflict();</script>
 <!--JQuery CDN-->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

    <!--Bootstrap Latest compiled and minified JavaScript-->
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>


    <!--Local JS file-->
<!--    <script type="text/javascript" src="/resources/js/site.js"></script>-->

        <?php
        $pageName = $_SERVER['PHP_SELF'];

        if (strstr($pageName, 'index')) {
        $pageName = 'Rapportbook';
        }
            $pageName = str_replace('/',' ',$pageName);
            $pageName = str_replace('_', ' ', $pageName);
            $pageName = str_replace('.', ' ', $pageName);
            $pageName = str_replace('php', ' ', $pageName);
            $pageName = ucwords($pageName);

        if (strstr($pageName, 'Profile')) {
            $pageName = $_SERVER['REQUEST_URI'];
            $pageName = str_replace('/', ' ', $pageName);
            $pageName = ucwords($pageName);
        }

        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if (strstr($url, "groupchat")) {

        }
        else {
        if (strstr($pageName, 'Messages')) {
            $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            preg_match("/[^\/]+$/", $url, $match);
            $username = $match[0];

            $sql = "SELECT ID FROM Members WHERE Username = '$username' ";
            $result = mysql_query($sql) or die(mysql_error());
            if (mysql_num_rows($result) > 0) {
            $row = mysql_fetch_assoc($result);
            $id = $row['ID'];

            $sql2 = "SELECT FirstName FROM Members where ID = $id";
            $result2 = mysql_query($sql2) or die(mysql_error());
            $rows2 = mysql_fetch_assoc($result2);
            $firstName = $rows2['FirstName'];
            $firstName = ucwords($firstName);
            $pageName = $firstName .' - Messages';
        }
        }
        else { // get group name

         }
        }
        ?>
        <title><?php echo $pageName ?></title>
    </head>

<?php } ?>


<?php function get_login_header()
{ ?>

    <form method="post" action="login.php" class="form-inline" >
        <header class="navbar navbar-default navbar-static-top header">
            <a href="/learn_more" title="Login or Sign Up">
            <img src="/images/Playdoe-white.png" alt="Rapportbook" height="40" width="auto"
                 class="logo-image"/>
            </a>

            <div class="form-group hidden-xs" >
                <label class="sr-only" for="login_email">Your Email </label>
                <input type="text" name="login_email" id="login_email" placeholder="Email" class="form-control"/>
                <label class="sr-only" for="login_password">Password</label>
                <input type="password" name="login_password" id="login_password" placeholder="Password"
                       class="form-control"/>
                <input type="submit" name="login" id="login" value="Login" class="btn btn-default"/>


                <a href="forgot-password" class="forgot-password-link hidden-xs">Forgot Your Password?</a>
                <a href="/support" class="hidden-xs">Support</a>
            </div>

        </header>
    </form>

<?php } ?>

<?php
function get_header()
{
    ?>


    <header class="navbar navbar-default navbar-static-top header">
        <div align="center">
        <a href="/homepage.php" style="text-align: center">

            <img src="/images/Playdoe-white.png" alt="Rapportbook" height="40" width="315"
                 class="logo-image"/>
        </a>
        </div>
        <?php $ID = $_SESSION['ID']; ?>



        <!--desktop layout -->





            <div class=" visible-sm visible-md visible-lg pull-right">
                <ul class="list-inline">
<!--                    <li><a href="/advertising.php" style="color:white;">Advertise</a></li>-->
                    <li><a href="/support" style="color:white;">Support</a></li>
                </ul>
            </div>


    </header>

    <!--mobile layout -->
<?php if (!empty($_COOKIE['ID']) && isset($_COOKIE['ID'])) { ?>

    <!--search box -->
    <?php $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
if (strstr($url, "home")) { ?>

<?php } ?>

<br/>

        </ul>

    </div>
<br/>


<?php }
} ?>

<?php function get_footer_files()
{
    ?>




<?php if (!strstr($url, "dev") && !strstr($url, "localhost")) { ?>
    <script>
        // google anayltics
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-59826601-1', 'auto');
        ga('send', 'pageview');
    </script>
<?php } ?>

    </body>
</html>


<?php } ?>
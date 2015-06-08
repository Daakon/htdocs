<?php
session_start();
$username = $_SESSION['Username'];
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// file paths
if (strstr($url, "local")) {
    $howItWorks = "/how_it_works.php";
    $login = "/index.php";
}
elseif (strstr($url, "dev")) {
    $howItWorks = "http://dev.rapportbook.com/how_it_works.php";
    $login = "http://dev.rapportbook.com/";
}
else {
    $howItWorks = "http://rapportbook.com/how_it_works.php/";
    $login = "http://rapportbook.com/";
}

$text = 'login';
if (isset($_SESSION['ID']) && !empty($_SESSION['ID'])) { ?>
    <a href="/index.php"><b>Go To Your Profile</b></a>
<?php }
else { ?>
    <a href="<?php echo $howItWorks ?>"><b>New To Rapportbook?...Click here to find out more</b></a>
    Or  <a href="/index.php"><b>Login</b></a>
<?php } ?>


<br/><br/>

<ul class="list-inline">
    <li><a href="/profile_public.php/<?php echo $username ?>">Profile</a></li>
    <li><a href="/member_videos_public.php/<?php echo $username ?>">Videos</a></li>
    <li><a href="/manage_post_public.php/<?php echo $username ?>"><?php echo $username ?>'s Post</a></li>
</ul>
<?php
session_start();
$username = $_SESSION['Username'];
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$sql = "SELECT FirstName FROM Members WHERE Username = '$username'";
$result = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_assoc($result);
$firstName = $row['FirstName'];

// file paths
if (strstr($url, "local")) {
    $howItWorks = "/learn_more.php";
    $login = "/index.php";
}
elseif (strstr($url, "dev")) {
    $howItWorks = "http://dev.rapportbook.com/learn_more.php";
    $login = "http://dev.rapportbook.com/";
}
else {
    $howItWorks = "http://rapportbook.com/learn_more.php/";
    $login = "http://rapportbook.com/";
}
?>

<div class="profileMenu">

<?php
$text = 'login';
if (isset($_SESSION['ID']) && !empty($_SESSION['ID'])) { ?>
    <a href="/index.php"><b>Home</b></a>
<?php }
else { ?>
    <a href="<?php echo $howItWorks ?>"><b>New To Rapportbook?...Click here to find out more</b></a>
    Or  <a href="/index.php"><b>Login</b></a>
<?php } ?>

</div>
<br/><br/>

<ul class="list-inline profileMenu">
    <li><a href="/profile_public.php/<?php echo $username ?>">Profile</a></li>
    <li><a href="/member_media_public.php/--><?php //echo $username ?><!--">Media</a></li>
    <li><a href="/manage_post_public.php/<?php echo $username ?>"><?php echo $firstName ?>'s Posts</a></li>

</ul>
<?php
session_start();
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$username = get_username_from_url();
$sql = "SELECT FirstName, ID FROM Members WHERE Username = '$username'";
$result = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_assoc($result);
$firstName = $row['FirstName'];
$profileID = $row['ID'];

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

<?php if ($_SESSION['ID'] != $profileID) { ?>
<ul class="list-inline profileMenu">
    <li><a href="/profile_public.php/<?php echo $username ?>">Profile</a></li>
    <li><a href="/member_media.php/<?php echo $username ?>">Media</a></li>
    <li><a href="/manage_post.php/<?php echo $username ?>"><?php echo $firstName ?>'s Posts</a></li>

</ul>

<?php } else { require 'profile_menu.php'; } ?>
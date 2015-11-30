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



<?php
$text = 'login';
if (!isset($_SESSION['ID']) && empty($_SESSION['ID'])) { ?>
    <div class="profileMenu">
    <a href="<?php echo $howItWorks ?>"><b>New To Rapportbook?...Click here to find out more</b></a>
    Or  <a href="/index"><b>Login</b></a>
    </div>
<?php } ?>


<br/><br/>


<?php if ($_SESSION['ID'] != $profileID) { ?>
<ul class="list-inline profileMenu">
    <li><a href="/home"><b>Home</b></a></li>
    <li><a href="/<?php echo $username ?>">Profile</a></li>
    <li><a href="/member_media/<?php echo $username ?>">Media</a></li>
    <li><a href="/manage_post/<?php echo $username ?>"><?php echo $firstName ?>'s Posts</a></li>
    <li><a href="/messages/<?php echo get_username($ID) ?>">Messages</a></li>
    <li><a href="/<?php echo get_username($ID)?>">My Profile</a></li>
</ul>

<?php } else { require 'profile_menu.php'; } ?>
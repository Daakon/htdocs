<?php if ($_SESSION['ID'] == $ID) {

$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
preg_match("/[^\/]+$/",$url ,$match);
$username = $match[0];
?>

<ul class="list-inline profileMenu">
    <li><a href="/home.php">Home</a></li>
    <li><a href="/profile.php/<?php echo $username ?>">Profile</a></li>
    <li><a href="/member_media.php/<?php echo $username ?>">Media</a></li>
    <li><a href="/manage_post.php/<?php echo $username ?>">Manage Posts</a></li>
    <li><a href="/messages.php/<?php echo $username ?>">Messaging <?php require 'getNewMessageCount.php' ?></a></li>


    <?php
    }
    else {
        require 'profile_menu.php';
    }
    ?>

    <br/><br/>

    <?php
$sql = "SELECT Admin FROM Members WHERE ID = $ID ";
$result = mysql_query($sql) or die(mysql_error());
$rows = mysql_fetch_assoc($result);
    if ($rows['Admin'] == 1) { ?>
        <li><a href="/marketing_manager.php">Marketing Manager</a></li>
    <?php }
?>

</ul>
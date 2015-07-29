
<?php if ($_SESSION['ID'] == $ID) { ?>
<ul class="list-inline demoText" style="display:inline-block;background:white;border:1px solid black;border-radius: 10px;">
    <li><a href="/home.php">Roll Call</a></li>
    <li><a href="/profile.php/<?php echo get_username($ID) ?>">Profile</a></li>
    <li><a href="/member_media.php">Video Book</a></li>
    <li><a href="/messages.php">Messaging <?php require 'getNewMessageCount.php' ?></a></li>
    <li><a href="/manage_post.php">Manage Posts</a></li>

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
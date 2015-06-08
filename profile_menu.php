

<ul class="list-inline">
    <li><a href="/home.php">Roll Call</a></li>
    <li><a href="/profile.php/<?php echo get_username($ID) ?>">Profile</a></li>
    <li><a href="/member_videos.php">Videos</a></li>
    <li><a href="/messages.php">Messaging <?php require 'getNewMessageCount.php' ?></a></li>
    <li><a href ="/manage_post.php">Manage Posts</a></li>
    <?php
$sql = "SELECT Admin FROM Members WHERE ID = $ID ";
$result = mysql_query($sql) or die(mysql_error());
$rows = mysql_fetch_assoc($result);
    if ($rows['Admin'] == 1) { ?>
        <li><a href="/marketing_manager.php">Marketing Manager</a></li>
    <?php }
?>

</ul>
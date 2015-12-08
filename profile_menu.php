<?php if ($_SESSION['ID'] == $ID) {

$username = get_username_from_url();
$profileID = get_id_from_username($username);

if ($profileID == $ID) {

?>

<style>
    .dropdown {
        background: transparent;
    }
</style>
<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Profile Menu
        <span class="caret"></span></button>
    <ul class="dropdown-menu">
        <li><a href="/home">Home</a></li>
        <li><a href="/<?php echo $username ?>">Profile</a></li>
        <li><a href="/member_media/<?php echo $username ?>">Media</a></li>
        <li><a href="/manage_post/<?php echo $username ?>">Manage Posts</a></li>
        <li><a href="/messages/<?php echo $username ?>">Messages <?php require 'getNewMessageCount.php' ?></a></li>
        <li><a href="/member_follows/<?php echo get_username($profileID)?>">Followers</a></li>
        <?php

        $username = get_username_from_url();
        $profileID = get_id_from_username($username);

        $sql = "SELECT Admin FROM Members WHERE ID = $ID ";
        $result = mysql_query($sql) or die(mysql_error());
        $rows = mysql_fetch_assoc($result);
        if ($rows['Admin'] == 1) { ?>
            <li><a href="/marketing_manager/<?php echo $username ?>">Marketing Manager</a></li>
        <?php }

        echo "</ul>";
        echo "</div>";
        }
        else {
            require 'profile_menu_public.php';

        }
        ?>


    <?php
    }
    ?>

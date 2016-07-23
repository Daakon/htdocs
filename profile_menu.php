<?php if ($_SESSION['ID'] == $ID) {

$username = get_username_from_url();
$profileID = get_id_from_username($username);


if ($profileID == $ID) {

?>

<style>
    .dropdown {
        background: transparent;
        padding-left:0px;
    }
</style>


<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
        <img src="<?php echo get_users_photo_by_id($ID) ?>" height="30" width="30"/> Profile Menu
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <li><a href="/home"><img src="/images/home.png" height="20" width="20" /> Home</a></li>
        <li><a href="/<?php echo get_username($ID)?>"><span style="color:black;font-weight: 900;"><img src="<?php echo get_users_photo_by_id($ID) ?>" height="20" width="20"/> My Profile</span></a></li>
        <li><a href="/post/<?php echo $username ?>"><img src="/images/post.png" height="20" width="20" /> Manage Posts</a></li>
        <li><a href="/messages/<?php echo $username ?>"><img src = "/images/messages.png" height="20" width="20" /> Messages <?php require 'getNewMessageCount.php' ?></a></li>
        <li><a href="/member_follows/<?php echo get_username($profileID) ?>"><img src = "/images/follows.png" height="20" width="20" /> Followers <?php require 'getNewFollowCount.php' ?></a></li>
        <li><a href="/settings/<?php echo get_username($ID) ?>"><img src="/images/settings.png" height="20" width="20" />Settings</a></li>
        <li><a href ="/view_messages/playdoe" class="visible-xs" ><img src = "/images/support.png" height="20" width="20" />Support</a></li>
        <li><a href ="/logout" ><img src = "/images/logout.png" height="20" width="20" /> Log Out</a></li>
        <?php

        $username = get_username_from_url();
        $profileID = get_id_from_username($username);

        $sql = "SELECT IsAdmin FROM Members WHERE ID = $ID ";
        $result = mysql_query($sql) or die(mysql_error());
        $rows = mysql_fetch_assoc($result);
        if ($rows['IsAdmin'] == 1) { ?>
            <li><a href="/backoffice?<?php echo $username ?>"><img src = "/images/marketing-menu-glyph" height="20" width="20"/> Back Office</a></li>
        <?php }

        echo "</ul>";
        echo "</div>";
        }
        else  {
            require 'profile_menu_public.php';

        }
        ?>


        <?php
        }
        ?>

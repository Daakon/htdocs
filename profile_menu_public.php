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
    $howItWorks = "/learn_more";
    $login = "../";
}
elseif (strstr($url, "dev")) {
    $howItWorks = "http://dev.playdoe.com/learn_more";
    $login = "http://dev.playdoe.com/";
}
else {
    $howItWorks = "http://playdoe.com/learn_more";
    $login = "http://playdoe.com/";
}
?>



<?php
$text = 'login';
if (!isset($_SESSION['ID']) && empty($_SESSION['ID'])) { ?>
    <div class="profileMenu">
        <a href="<?php echo $howItWorks ?>"><b>New To Playdoe?...Click here to find out more</b></a>
        Or  <a href="/learn_more"><b>Login</b></a>
    </div>

    <?php
    if ($_SESSION['IsProfilePage'] == true) {

        require 'publicProfile.php';


    }
}


?>



<?php

if ($_SESSION['ID'] != $profileID) { ?>

    <?php
    $showProfile = "";
    if (strstr($url, "home")) {
        $username = get_username($ID);
        $showProfile = "style='display:none;'";
    }
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
            <li><a href="/home"><img src="/images/home.png" height="20" width="20" /> Home</b></a></li>
            <li <?php echo $showProfile ?>><a href="/<?php echo $username ?>"><img src="/images/profile.png" height="20" width="20" /> Profile</a></li>
            <li><a href="/post/<?php echo $username ?>"><img src="/images/post.png" height="20" width="20" /> View Posts</a></li>
            <li><a href="/messages/<?php echo get_username($ID) ?>"><img src = "/images/messages.png" height="20" width="20" /> Message</a></li>
            <li><a href="/member_follows/<?php echo $username ?>"><img src = "/images/follows.png" height="20" width="20" /> Followers</a></li>
            <li><a href="/<?php echo get_username($ID)?>"><span style="color:black;font-weight: 900;"><img src="<?php echo get_users_photo_by_id($ID) ?>" height="20" width="20"/> My Profile</span></a></li>
            <li><a href="/settings/<?php echo get_username($ID) ?>"><img src="/images/settings.png" height="20" width="20" />Settings</a></li>
            <li><a href ="/view_messages/playdoe" class="visible-xs" ><img src = "/images/support.png" height="20" width="20" /> Support</a></li>
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
            ?>
        </ul>
    </div>

    <br/><br/>

    <?php

}

else { require 'profile_menu.php'; } ?>
<?php
session_start();
$username = $_SESSION['Username'];
?>

<ul class="list-inline">
    <li><a href="/profile_public.php/<?php echo $username ?>">Profile</a></li>
    <li><a href="/member_photos_public.php/<?php echo $username ?>">Photos</a></li>
    <li><a href="/manage_post_public.php/<?php echo $username ?>"><?php echo $username ?>'s Post</a></li>
</ul>
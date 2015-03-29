<ul class="list-inline">
    <li><a href="/home.php">Roll Call</a></li>
    <li><a href="/profile_public.php/<?php echo get_username($ID) ?>">Profile</a></li>
    <li><a href="/member_photos_public.php">Photos</a></li>
    <li><a href="/messages_public.php">Messaging <?php require 'getNewMessageCount.php' ?></a></li>
</ul>
<?php

require 'connect.php';
require 'getSession.php';
require 'html_functions.php';
require 'mediaPath.php';
require 'findURL.php';

get_head_files();
get_header();
require 'memory_settings.php';
$ID = $_SESSION['ID'];

$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
preg_match("/[^\/]+$/",$url ,$match);
$username = $match[0];
$token = $match[1];
?>



<div class="container" >
    <div class="row row-padding">

        <ul class="list-inline">

            <li><a href="/profile_public.php/<?php $username ?>">Profile</a></li>
            <li><a href="/member_photos_public.php/<?php $username ?>">Photos & Videos</a></li>
        </ul>
        <br/><br/>

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call ">

            <h2>Messages</h2>
            <hr/>

            <?php
            $sql = "SELECT * FROM Messages WHERE ThreadOwner_ID = $ID AND InitialMessage = 1 AND IsDeleted = 0 ";
            $result = mysql_query($sql) or die(mysql_error());

            if (mysql_numrows($result) > 0) {
                while ($rows = mysql_fetch_assoc($result)) {

                    $sql = "SELECT ID FROM Members WHERE Username = '$username' ";
                    $result = mysql_query($sql) or die(mysql_error());
                    $rows = mysql_fetch_assoc($result);

                    $receiverID = $rows['ID'];
                    $subject = $rows['Subject'];

                    // get receiver name
                    $sql2 = "SELECT FirstName, LastName, ProfilePhoto
                FROM Members, Profile
                WHERE Profile.Member_ID = $receiverID
                AND Members.ID = $receiverID ";

                    $result2 = mysql_query($sql2) or die(mysql_error());
                    $rows2 = mysql_fetch_assoc($result2);
                    $pic = $rows2['ProfilePhoto'];
                    $name = $rows2['FirstName'] . ' ' . $rows2['LastName'];

                    echo "<a href = '/view_messages_public.php?id=$receiverID'><img src = '$mediaPath$pic' class='profilePhoto-Feed' alt='' /> $name </a>";
                    echo "<br/>";
                    echo "$subject";
                    echo "<hr/>";
                    echo "<br/>";
                }
            }
            else {
                echo "You currently have no thread with $username";
            }
            ?>

            <!-------------------------------------------------------------------->
        </div>
    </div>

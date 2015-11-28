<?php

require 'imports.php';

get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];


?>



<div class="container" style="margin-top:-50px;">
    <div class="row row-padding">

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 ">
        <ul class="list-inline">

            <?php require 'profile_menu.php'; ?>
        </ul>
            </div>


        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call ">

            <h2>Messages</h2>
            <hr/>

            <?php
            $sql = "SELECT DISTINCT * FROM Messages WHERE ThreadOwner_ID = $ID AND (Receiver_ID = $ID Or Sender_ID = $ID) AND (InitialMessage = 1) AND (IsDeleted = 0) Order By ID DESC ";
            $result = mysql_query($sql) or die(mysql_error());

            if (mysql_num_rows($result) > 0) {
                while ($rows = mysql_fetch_assoc($result)) {
                    if ($rows['Sender_ID'] != $ID) {
                        $otherID = $rows['Sender_ID'];
                    }
                    else {
                        $otherID = $rows['Receiver_ID'];
                    }

                    $subject = $rows['Subject'];

                    // get sender name
                    $sql2 = "SELECT FirstName,LastName,Username, ProfilePhoto
                FROM Members, Profile
                WHERE Profile.Member_ID = $otherID
                AND Members.ID = $otherID ";

                    $result2 = mysql_query($sql2) or die(mysql_error());
                    $rows2 = mysql_fetch_assoc($result2);
                    $pic = $rows2['ProfilePhoto'];
                    $name = $rows2['FirstName'].' '.$rows2['LastName'];
                    $username = $rows2['Username'];

                    // get new message

                    $sql3 = "SELECT New FROM Messages WHERE ThreadOwner_ID = $ID And (Sender_ID = $otherID) AND (Receiver_ID = $ID) AND (New = 1)";
                    $result3 = mysql_query($sql3) or die(mysql_error());
                    $row3 = mysql_fetch_assoc($result3);

                    echo "<a href = '/view_messages/$username'><img src = '$mediaPath$pic' class='profilePhoto-Feed' alt='' /> $name </a>";
                    if (mysql_num_rows($result3) > 0) { echo "<span class='red-font bold'>New</font>"; }
                    echo "<br/>";
                    echo "$subject";
                    echo "<hr/>";
                    echo "<br/>";
                }
            }
            else {
                echo "You currently have no messages";
            }
            ?>

            <!-------------------------------------------------------------------->
            </div>
        </div>
    </div>
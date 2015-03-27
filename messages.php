<?php

require 'connect.php';
require 'getSession.php';
require 'html_functions.php';
require 'mediaPath.php';
require 'findURL.php';
require 'model_functions.php';

get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];


?>



<div class="container" >
    <div class="row row-padding">

        <ul class="list-inline">

            <?php require 'profile_menu.php'; ?>
        </ul>
        <br/><br/>

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call ">

            <h2>Messages</h2>
            <hr/>

            <?php
            $sql = "SELECT * FROM Messages WHERE ThreadOwner_ID = $ID AND (Sender_ID != $ID Or Receiver_ID != $ID) AND InitialMessage = 1 AND IsDeleted = 0 ";
            $result = mysql_query($sql) or die(mysql_error());

            if (mysql_numrows($result) > 0) {
                while ($rows = mysql_fetch_assoc($result)) {
                    if ($rows['Sender_ID'] != $ID) {
                        $otherID = $rows['Sender_ID'];
                    }
                    else {
                        $otherID = $rows['Receiver_ID'];
                    }

                    $subject = $rows['Subject'];

                    // get sender name
                    $sql2 = "SELECT FirstName, LastName, ProfilePhoto
                FROM Members, Profile
                WHERE Profile.Member_ID = $otherID
                AND Members.ID = $otherID ";

                    $result2 = mysql_query($sql2) or die(mysql_error());
                    $rows2 = mysql_fetch_assoc($result2);
                    $pic = $rows2['ProfilePhoto'];
                    $name = $rows2['FirstName'] . ' ' . $rows2['LastName'];

                    echo "<a href = 'view_messages.php?id=$otherID'><img src = '$mediaPath$pic' class='profilePhoto-Feed' alt='' /> $name </a>";
                    if ($rows['New'] == 1) { echo "<span style='color:red;font-weight:bold'>New</font>"; }
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
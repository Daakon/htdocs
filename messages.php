<?php

require 'imports.php';

get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];


?>


<div class="container">
    <div class="row row-padding">

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call ">

            <?php require 'profile_menu.php'; ?>

            <h2>Messages</h2>
            <hr/>

            <?php
            $sql = "SELECT DISTINCT * FROM Messages WHERE ThreadOwner_ID = $ID AND (Receiver_ID = $ID Or Sender_ID = $ID) AND (InitialMessage = 1) AND (IsDeleted = 0) Order By New Desc, ID Desc";
            $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting initial message"));

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

                    // check if 2 people have new messages first
                    $sqly = "SELECT ID FROM Messages WHERE ThreadOwner_ID = $ID And Sender_ID = $ID And InitialMessage = 1 And New = 1";
                    $resulty = mysql_query($sqly) or die(logError(mysql_error(), $url, "SqlY Checking if 2 people have an initial message"));

                    $sqlz = "SELECT ID FROM Messages WHERE ThreadOwner_ID = $ID And Receiver_ID = $ID And InitialMessage = 1 And New = 1";
                    $resultz = mysql_query($sqlz) or die(logError(mysql_error(), $url, "SqlZ checking if 2 people have an initial message"));


                    if (mysql_num_rows($resulty) > 0 || mysql_num_rows($resultz) > 0) {

                        // get ALL new messages owned by current session against the other person
                        $sql3 = "SELECT ID FROM Messages WHERE (ThreadOwner_ID = $ID And Sender_ID = $otherID And New =1) Or (ThreadOwner_ID = $ID And Receiver_ID = $otherID AND New = 1) ";
                        $result3 = mysql_query($sql3) or die(logError(mysql_error(), $url, "Getting all new messages owned by current session against other person"));
                        $row3 = mysql_fetch_assoc($result3);

                       // if we are past the first message, subtract the initial message row
                        $messageCount = mysql_num_rows($result3);
                        $firstMessage = $rows['FirstMessage'];
                        if ($firstMessage == 0) {
                            $messageCount = $messageCount -1;
                        }

                    }

                echo "
                <div class='profileImageWrapper-Feed'>
                <a href = '/view_messages/$username'>
                <img src = '$mediaPath$pic' class='profilePhoto-Feed' alt='' />
                </a>
                </div>

                <div class='profileNameWrapper-Feed'>
                <a href = '/view_messages/$username'>
                 <div class=\"profileName-Feed\" >$name

                ";


                    if (mysql_num_rows($result3) > 0) {
                    echo "<span style='color:#E30022;font-weight: bold'>". $messageCount." New</font>";
                }
                    echo "
                    </div>
                    </a>
                    </div>";

                    echo "<hr class='hr-line' style='clear:both'/>";
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
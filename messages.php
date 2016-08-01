<?php

require 'imports.php';

get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];


?>


<div class="container containerFlush">
    <div class="row row-padding">

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call">

            <?php require 'profile_menu.php'; ?>

            <h4>Messages</h4>
            <hr/>
            <a href="/view_messages?groupchat=groupchat">Start Chat</a>
            <hr/>

            <?php
            // get group and individual messages
            $sql = "SELECT DISTINCT * FROM Messages WHERE ThreadOwner_ID = $ID AND (Receiver_ID = $ID Or Sender_ID = $ID) AND (InitialMessage = 1) AND (IsDeleted = 0) And (LENGTH(GroupID) > 5) And (IsDeleted = 0)
                    UNION
                    SELECT DISTINCT * FROM Messages WHERE ThreadOwner_ID = $ID AND (Receiver_ID = $ID Or Sender_ID = $ID) AND (InitialMessage = 1) AND (IsDeleted = 0) And GroupID = ''
            Order By New Desc, ID Desc";
            $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting initial message"));

            if (mysql_num_rows($result) > 0) {
                while ($rows = mysql_fetch_assoc($result)) {

                    if ($rows['Sender_ID'] != $ID) {
                        $otherID = $rows['Sender_ID'];
                    } else {
                        $otherID = $rows['Receiver_ID'];
                    }

                    $subject = $rows['Subject'];
                    $groupName = $rows['GroupName'];
                    $groupID = $rows['GroupID'];


                    // handle GROUP CHAT
                    if (strlen($groupID) > 0 && $groupID == $groupID) {
                        $pic = "group-chat-photo.png";


                        $profilePic = "<div style='float:left; display:inline;padding-right:10px;'>" . getChatProfilePic($groupID, $ID) . "</div>";


                        $name = $groupName;
                        $username = $groupID;

                        // check group thread


                        // get ALL new messages owned by current session against the other person

                        $sql3 = "SELECT ID FROM Messages WHERE ThreadOwner_ID = $ID And (New = 1) And (GroupID = '$groupID') ";
                        $result3 = mysql_query($sql3) or die(logError(mysql_error(), $url, "Getting all new messages owned by current session against other person"));
                        $row3 = mysql_fetch_assoc($result3);
                        $count = mysql_num_rows($result3);

                        // if we are past the first message, subtract the initial message row
                        $messageCount = mysql_num_rows($result3);
                        $firstMessage = $rows['FirstMessage'];
                        if ($firstMessage == 0) {
                            $messageCount = $messageCount - 1;
                        }


                        if ($messageCount > 0) {
                            $notification = "<span style='color:#E30022;font-weight: bold'>" . $messageCount . " New</font>";
                        } else {
                            $notification = '';
                        }

                    }



                    if ($groupID == '') {

                        $sqlBlocked = "SELECT BlockedID,BlockerID FROM Blocks WHERE (BlockerID = $ID Or BlockedID = $ID)";
                        $resultBlocked = mysql_query($sqlBlocked) or die(logError(mysql_error(), $url, "Getting IDs for all post commentors"));

                        $blockIDs = array();

//Iterate over the results and sort out the biz ids from the consumer ones.
                        while ($rowsBlocked = mysql_fetch_assoc($resultBlocked)) {
                            array_push($blockIDs, $rowsBlocked['BlockedID'], $rowsBlocked['BlockerID']);
                        }


                        $sql2 = "SELECT FirstName,LastName,Username, ProfilePhoto
                                 FROM Members, Profile
                                 WHERE (Profile.Member_ID = $otherID)
                                 AND (Members.ID = $otherID )
                                 And (Members.IsActive = 1) ";

                        $result2 = mysql_query($sql2) or die(mysql_error());
                        $rows2 = mysql_fetch_assoc($result2);
                        $profilePhoto = $rows2['ProfilePhoto'];
                        $profilePic = "<img src = '/media/$profilePhoto' class='profilePhoto-Feed' alt='' /> ";

                        $name = $rows2['FirstName'] . ' ' . $rows2['LastName'];
                        $username = $rows2['Username'];
                        $isActive = $rows2['IsActive'];

                        if ($isActive == 0) {
                            $profilePic = "<img src = '/media/default_photo.png' class='profilePhoto-Feed' alt='' />";
                        }

                        // check if 2 people have new messages first

                        $sqly = "SELECT ID FROM Messages WHERE ThreadOwner_ID = $ID And (Sender_ID = $ID or Receiver_ID = $ID) And (InitialMessage = 1) And (New = 1) And (GroupID = '$groupID') ";

                        $resulty = mysql_query($sqly) or die();

                        if (mysql_num_rows($resulty) > 0) {
                            // get ALL new messages owned by current session against the other person

                            $sql4 = "SELECT ID FROM Messages WHERE (ThreadOwner_ID = $ID) And (Sender_ID = $otherID or Receiver_ID = $otherID)  And (New = 1) And (GroupID = '') ";
                            $result4 = mysql_query($sql4) or die(logError(mysql_error(), $url, "Getting all new messages owned by current session against other person"));
                            $row4 = mysql_fetch_assoc($result4);

                            // if we are past the first message, subtract the initial message row

                            $messageCount = mysql_num_rows($result4);

                            $firstMessage = $rows['FirstMessage'];
                            if ($firstMessage == 0) {
                                $messageCount = $messageCount - 1;
                            }
                            if ($messageCount > 0) {

                                $notification = "<span style='color:#E30022;font-weight: bold'>" . $messageCount . " New</font>";
                            } else {

                                $notification = '';
                            }
                        } else {
                            $notification = '';
                        }


                    }




                    echo "

                <div class='profileImageWrapper-Feed'>
                <a href = '/view_messages/$username'>
                $profilePic
                </a>
                </div>

                <div class='profileNameWrapper-Feed'>
                <a href = '/view_messages/$username'>
                 <div class=\"profileName-Feed\" >$name

                ";

                    echo $notification;

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
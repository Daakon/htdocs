<?php

//handle approves
require 'imports.php';

$memberID = $_POST['memberID'];
$ID = $_POST['ID'];

$sql = "INSERT INTO Follows (Followed_ID, Follower_ID, New) Values ($memberID, $ID, 1)";
mysql_query($sql);

// only send email if account & email active
if (checkActive($memberID)) {
    if (checkEmailActive($memberID)) {
        build_and_send_email($ID, $memberID, 12, '', '');
    }
}
?>

<div id="followDiv">
    <table >
        <tr>
            <td >
                <?php
                $sqlFollow = "SELECT * FROM Follows WHERE Follower_ID = $ID And Followed_ID = $memberID ";
                $resultFollow = mysql_query($sqlFollow);

                if (mysql_num_rows($resultFollow) == 0) {
                    echo '<form>';
                    echo '<input type = "hidden" class = "followerID" value = "'.$ID.'" />';
                    echo '<input type = "hidden" class = "followedID" value = "'.$memberID.'">';
                    echo '<input type = "button" class = "btnFollow" value = "Follow" />';
                    echo '</form>';
                }
                else {
                    echo '<form>';
                    echo '<input type = "hidden" class = "followerID" value = "'.$ID.'" />';
                    echo '<input type = "hidden" class = "followedID" value = "'.$memberID.'">';
                    echo '<input type = "button" class = "btnUnfollow" value = "Unfollow" />';
                    echo '</form>';
                }
                ?>
            </td>
        </tr>
    </table>

    <?php
    $sqlFollowCount = "SELECT * FROM Follows WHERE Followed_ID = $memberID ";
    $sqlFollowCountResult = mysql_query($sqlFollowCount);
    echo '<b>'.$count = mysql_num_rows($sqlFollowCountResult).'</b>';
    ?>

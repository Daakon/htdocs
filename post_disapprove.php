<?php

//handle approves
require 'imports.php';

$postID = $_POST['postID'];
$ID = $_POST['ID'];
$memberID = $_POST['memberID'];

$sql = "DELETE FROM PostApprovals WHERE Post_ID = '$postID' AND Member_ID = '$ID' ";
mysql_query($sql) or die(logError(mysql_error(), $url, "Deleting post approval"));

$sql2 = "SELECT ID FROM PostApprovals WHERE Post_ID = '$postID' AND Member_ID = '$ID' ";
$result2 = mysql_query($sql2) or die(logError(mysql_error(), $url, "Checking user ID for post approval"));

// get approvals for each post
$sql3 = "SELECT ID FROM PostApprovals WHERE Post_ID = '$postID' ";
$result3 = mysql_query($sql3) or die(logError(mysql_error(), $url, "Getting all IDs for post approval"));
$rows3 = mysql_fetch_assoc($result3);
$approvals = mysql_numrows($result3);

// show disapprove if members has approved the post

echo "<div id = 'approvals$postID'>";


if (mysql_num_rows($result2) > 0) {

    echo '<form>';

    echo '<input type ="text" class = "postID" id = "postID" value = "' . $postID . '" />';
    echo '<input type ="text" class = "ID" id = "ID" value = "' . $ID . '" />';
    echo '<input type ="hidden" class = "memberID" id = "memberID" value = "' . $memberID . '" />';
    echo '<input type ="button" class = "btnDisapprove" />';

    if ($approvals > 0) {
        //echo '<tr><td>';

        echo '&nbsp;<span>' . $approvals . '</font>';
    }
    echo '</form>';
} else {
    echo '<form>';

    echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';
    echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
    echo '<input type ="hidden" class = "memberID" id = "memberID" value = "' . $memberID . '" />';
    echo '<input type ="button" class = "btnApprove" />';

    if ($approvals > 0) {
        //echo '<tr><td>';

        echo '&nbsp;<span>' . $approvals . '</font>';
    }
    echo '</form>';
}


echo '</div>';

//-------------------------------------------------------------
// End of approvals
//-------------

?>
<?php
session_start();
require 'connect.php';
require 'MediaPath.php';
require 'getSession.php';
require_once 'email.php';
ini_set('memory_limit', '900M');
// handle approves

$mediaName = $_POST['mediaName'];
$type = $_POST['type'];
$postID = $_POST['postID'];
$mediaID = $_POST['mediaID'];
$mediaDate = $_POST['mediaDate'];

$ID = $_POST['ID'];


$sql = "INSERT INTO PostApprovals (Post_ID,  Member_ID) Values
                                  ('$postID', '$ID')";
mysql_query($sql) or die(mysql_error());
//An approval just popped so we should set the notifications
//A comment was just made, we need to send out some notifications.
//The first thing is to identify all of the id's connected with this bulletin
$user_id = $ID;


//Get the ids of all the consumers connected with a bulletin comment
$sql = "SELECT Member_ID FROM PostComments WHERE ID = $postID ";

$result = mysql_query($sql) or die(mysql_error());

$comment_ids = array();

//Iterate over the results and sort out the biz ids from the consumer ones.
while ($rows = mysql_fetch_assoc($result)) {
    array_push($comment_ids, $rows['id']);
}

//Boil the id's down to unique values bc we dont want it send double emails or notifications
$comment_ids = array_unique($consumer_comment_ids);
//Send consumer notifications


foreach ($comment_ids as $item) {

    // only send email if account & email active
    if (checkActive($item, 1)) {
        if (checkEmailActive($item, 1)) {
            build_and_send_email($item, $user_id, 1, $postID);
        }
    }
}


//=========================================================================================================================//
//BELOW IS END OF BULLETIN Approval HANDLING CODE ==========================================================================//

// check if user has approved this post

$sql2 = "SELECT * FROM PostApprovals WHERE ID = '$postID' AND Member_ID = '$ID' ";
$result2 = mysql_query($sql2) or die(mysql_error());
$rows2 = mysql_fetch_assoc($result2);

// get approvals for each bulletin
$sql3 = "SELECT * FROM PostApprovals WHERE ID = '$postID' ";
$result3 = mysql_query($sql3) or die(mysql_error());
$rows3 = mysql_fetch_assoc($result3);
$approvals = mysql_numrows($result3);

echo "<div id = 'approvals'>";

if (mysql_numrows($result2) > 0) {

    echo '<form>';
    echo '<input type ="hidden" class = "postID" value = "' . $postID . '" />';

    echo '<input type ="hidden" class = "id" value="' . $id . '"/>';
    echo '<input type ="hidden" class = "mediaID" value = "' . $mediaID . '" />';

    echo '<input type ="hidden" class = "mediaName" value ="' . $mediaName . '" />';
    echo '<input type ="hidden" class = "type" value = "' . $type . '" />';
    echo '<input type ="hidden" class = "mediaDate" id = "mediaDate" value = "' . $mediaDate . '" />';


    echo '<input type ="button" class = "btnDisapprove" />';

    if ($approvals > 0) {
        //echo '<tr><td>';

        echo '&nbsp;<span style = "color:red;font-weight:bold;font-size:16px">' . $approvals . '</font>';
    }
    echo '</form>';
} else {

    echo '<form>';
    echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';

    echo '<input type ="hidden" class = "ID" value="' . $ID . '"/>';
    echo '<input type ="hidden" class = "mediaID" value = "' . $mediaID . '" />';

    echo '<input type ="hidden" class = "mediaName" id = "mediaName" value ="' . $mediaName . '" />';
    echo '<input type ="hidden" class = "type" id = "type" value = "' . $type . '" />';
    echo '<input type ="hidden" class = "mediaDate" id = "mediaDate" value = "' . $mediaDate . '" />';


    echo '<input type ="button" class = "btnApprove" />';

    if ($approvals > 0) {
        //echo '<tr><td>';

        echo '&nbsp;<span style = "color:red;font-weight:bold;font-size:16px">' . $approvals . '</font>';
    }
    echo '</form>';
}
echo "</div>"; // end of approval div

?>

<?php
//handle approves
require 'connect.php';
require 'getSession.php';

// variables that get sent in post must have identical names every where they exist
$postID = $_POST['postID'];
$ID = $_POST['ID'];

$sql = "INSERT INTO PostApprovals (Post_ID, Member_ID) Values
                                  ('$postID',  '$ID')";
mysql_query($sql) or die(mysql_error());


//An approval just popped so we should set the notifications
//A comment was just made, we need to send out some notifications.
//The first thing is to identify all of the id's connected with this bulletin



//Get the ids of all the consumers connected with a bulletin comment
$sql = "SELECT Member_ID FROM PostComments WHERE Post_ID = $postID ";

$result = mysql_query($sql) or die(mysql_error());

$comment_ids = array();

//Iterate over the results and sort out the biz ids from the consumer ones.
while ($rows = mysql_fetch_assoc($result)) {
    array_push($comment_ids, $rows['ID']);
}

//Boil the id's down to unique values bc we dont want it send double emails or notifications
$comment_ids = array_unique($comment_ids);
//Send consumer notifications

/*
foreach ($comment_ids as $item) {

    // only send email if account & email active
    if (checkActive($item, 1)) {
        if (checkEmailActive($item, 1)) {
            build_and_send_email($ID, $item , 1, $postID);
        }
    }
}

//Notify the post creator

$sql = "SELECT ID FROM Posts WHERE ID = '$postID';";

$result = mysql_query($sql) or die(mysql_error());
$rows = mysql_fetch_assoc($result);


if (checkEmailActive($ID)) {
    build_and_send_email($ID, $user_id, 1, $postID, '');
}
*/

//=========================================================================================================================//
//BELOW IS END OF Post Approval HANDLING CODE ==========================================================================//

// check if user has approved this post


$sql2 = "SELECT ID FROM PostApprovals WHERE Post_ID = '$postID' AND Member_ID = '$ID' ";
$result2 = mysql_query($sql2) or die(mysql_error());

// get approvals for each post
$sql3 = "SELECT ID FROM PostApprovals WHERE Post_ID = '$postID' ";
$result3 = mysql_query($sql3) or die(mysql_error());
$rows3 = mysql_fetch_assoc($result3);
$approvals = mysql_numrows($result3);

// show disapprove if members has approved the post
echo "<div id = 'approvals$postID'>";


if (mysql_numrows($result2) > 0) {

    echo '<form>';

    echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';
    echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
    echo '<input type ="button" class = "btnDisapprove" />';

    if ($approvals > 0) {
        //echo '<tr><td>';

        echo '&nbsp;<span style = "color:red;font-weight:bold;font-size:16">' . $approvals . '</font>';
    }
    echo '</form>';
} else {
    echo '<form>';

    echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';
    echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
    echo '<input type ="button" class = "btnApprove" />';

    if ($approvals > 0) {
        //echo '<tr><td>';

        echo '&nbsp;<span style = "color:red;font-weight:bold;font-size:16">' . $approvals . '</font>';
    }
    echo '</form>';
}

//-------------------------------------------------------------
// End of approvals
//-----------------------------------------------------------

?>
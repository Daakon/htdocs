<?php
//handle approves
require 'imports.php';

// variables that get sent in post must have identical names every where they exist
$postID = $_POST['postID'];
$ID = $_POST['ID'];
$memberID = $_POST['memberID'];

if (!empty($_POST['ID'])) {


$sql = "INSERT INTO PostApprovals (Post_ID,   Member_ID, Owner_ID) Values
                                  ('$postID',  '$ID', '$memberID')";
mysql_query($sql) or die(mysql_error());


//An approval just popped so we should set the notifications
//A comment was just made, we need to send out some notifications.
//The first thing is to identify all of the id's connected with this post


//Get the ids of all the people connected with a post comment
$sql = "SELECT Member_ID FROM PostComments WHERE Post_ID = $postID ";

$result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting IDs for all post commentors"));

$comment_ids = array();

//Iterate over the results and sort out the biz ids from the consumer ones.
while ($rows = mysql_fetch_assoc($result)) {
    array_push($comment_ids, $rows['Member_ID']);
}

//Boil the ids down to unique values bc we dont want it send double emails or notifications
$comment_ids = array_unique($comment_ids);
//Send consumer notifications


foreach ($comment_ids as $item) {

    if (!empty($item) && $item != $ID) {
        // only send email if account & email active
        if (checkActive($item)) {
            if (checkEmailActive($item)) {
                build_and_send_email($ID, $item, 2, $postID, '');
            }
        }
    }
}

    // notify post owner if not in comment ids
    $sql2 = "Select Member_ID From Posts WHERE ID = '$postID' ";
    $result2 = mysql_query($sql2);
    $row2 = mysql_fetch_assoc($result2);
    $postOwnerID = $row2['Member_ID'];

    if (in_array($postOwnerID, $comment_ids)) {
        // already notified
    }
    else {
        // notify owner if owner didn't approve post
        if ($ID != $postOwnerID) {
            if (checkActive($postOwnerID)) {
                if (checkEmailActive($postOwnerID)) {
                    build_and_send_email($ID, $postOwnerID, 2, $postID, '');
                }
            }
        }
    }

}



//=========================================================================================================================//
//BELOW IS END OF Post Approval HANDLING CODE ==========================================================================//

// check if user has approved this post


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

    echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';
    echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
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

$_SESSION['PostID'] = $postID;
$_SESSION['Approvals'] = $approvals;
//-------------------------------------------------------------
// End of approvals
//-----------------------------------------------------------
?>



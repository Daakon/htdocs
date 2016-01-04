<?php
//handle approves
require 'imports.php';

// variables that get sent in post must have identical names every where they exist
$postID = $_POST['postID'];
$ID = $_POST['ID'];

if (!empty($_POST['ID'])) {


$sql = "INSERT INTO PostApprovals (Post_ID,   Member_ID) Values
                                  ('$postID',  '$ID')";
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

    if (!empty($item)) {
        // only send email if account & email active
        if (checkActive($item)) {
            if (checkEmailActive($item)) {
                build_and_send_email($ID, $item, 2, $postID, '');
            }
        }
    }
}

    //Notify the post creator

    $sql = "SELECT Member_ID FROM Posts WHERE ID = '$postID';";

    $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting Post owner ID"));
    $rows = mysql_fetch_assoc($result);
    $creatorID = $rows['Member_ID'];

    if (checkEmailActive($ID)) {
        build_and_send_email($ID, $creatorID, 2, $postID, '');
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



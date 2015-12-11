<?php

require 'imports.php';
// handle approves

$mediaName = $_POST['mediaName'];
$mediaType = $_POST['mediaType'];
$postID = $_POST['postID'];
$mediaID = $_POST['mediaID'];
$mediaDate = $_POST['mediaDate'];

$ID = $_POST['ID'];

if (!empty($_POST['ID'])) {

$sql = "INSERT INTO MediaApprovals (Media_ID,  Member_ID) Values
                                  ('$mediaID', '$ID')";
mysql_query($sql) or die(mysql_error());
//An approval just popped so we should set the notifications
//A comment was just made, we need to send out some notifications.
//The first thing is to identify all of the id's connected with this bulletin
$user_id = $ID;



//Get the ids of all the consumers connected with a bulletin comment
$sql = "SELECT Member_ID FROM MediaComments WHERE Media_ID = $mediaID ";

$result = mysql_query($sql) or die(mysql_error());

$comment_ids = array();

//Iterate over the results and sort out the biz ids from the consumer ones.
while ($rows = mysql_fetch_assoc($result)) {
    array_push($comment_ids, $rows['Member_ID']);
}

//Boil the id's down to unique values bc we dont want it send double emails or notifications
$comment_ids = array_unique($comment_ids);
//Send consumer notifications


foreach ($comment_ids as $item) {

    // only send email if account & email active
    if (strlen($item) > 0) {
        if (checkActive($item)) {
            if (checkEmailActive($item)) {
                build_and_send_email($user_id, $item, 7, $mediaName, '');
            }
        }
    }
}


    //Notify the post creator
    $sql = "SELECT Member_ID FROM Media WHERE MediaName = '$mediaName';";

    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    $creatorID = $rows['Member_ID'];

    if (checkEmailActive($creatorID)) {
        echo "<script>alert('$creatorID');</script>";
        build_and_send_email($ID, $creatorID, 7, $mediaName, '');
    }
}

//=========================================================================================================================//
//BELOW IS END OF Post Approval HANDLING CODE ==========================================================================//

// check if user has approved this post

$sql2 = "SELECT * FROM MediaApprovals WHERE Media_ID = '$mediaID' AND Member_ID = '$ID' ";
$result2 = mysql_query($sql2) or die(mysql_error());
$rows2 = mysql_fetch_assoc($result2);

// get approvals for each bulletin
$sql3 = "SELECT * FROM MediaApprovals WHERE Media_ID = '$mediaID' ";
$result3 = mysql_query($sql3) or die(mysql_error());
$rows3 = mysql_fetch_assoc($result3);
$approvals = mysql_num_rows($result3);

echo "<div id = 'approvals$postID'>";

if (mysql_num_rows($result2) > 0) {

    echo '<form>';
    echo '<input type ="hidden" class = "ID" value="' . $ID . '"/>';
    echo '<input type ="hidden" class = "mediaID" value = "' . $mediaID . '" />';
    echo '<input type ="hidden" class = "mediaName" value ="' . $mediaName . '" />';
    echo '<input type ="hidden" class = "mediaType" value = "' . $mediaType . '" />';
    echo '<input type ="hidden" class = "mediaDate" id = "mediaDate" value = "' . $mediaDate . '" />';


    echo '<input type ="button" class = "btnDisapprove" />';

    if ($approvals > 0) {
        //echo '<tr><td>';

        echo '&nbsp;<span>' . $approvals . '</font>';
    }
    echo '</form>';
} else {

    echo '<form>';
    echo '<input type ="hidden" class = "ID" value="' . $ID . '"/>';
    echo '<input type ="hidden" class = "mediaID" value = "' . $mediaID . '" />';
    echo '<input type ="hidden" class = "mediaName" id = "mediaName" value ="' . $mediaName . '" />';
    echo '<input type ="hidden" class = "type" id = "mediaType" value = "' . $mediaType . '" />';
    echo '<input type ="hidden" class = "mediaDate" id = "mediaDate" value = "' . $mediaDate . '" />';


    echo '<input type ="button" class = "btnApprove" />';

    if ($approvals > 0) {
        //echo '<tr><td>';

        echo '&nbsp;<span>' . $approvals . '</font>';
    }
    echo '</form>';
}
echo "</div>"; // end of approval div

?>

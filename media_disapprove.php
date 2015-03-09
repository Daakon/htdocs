<?php

require 'connect.php';
require 'mediaPath.php';
require 'getSession.php';
require_once 'email.php';

ini_set('memory_limit', '900M');
// handle approves

$mediaName = $_POST['mediaName'];
$mediaType = $_POST['mediaType'];
$postID = $_POST['postID'];
$mediaID = $_POST['mediaID'];
$mediaDate = $_POST['mediaDate'];

$ID = $_POST['ID'];


$sql = "DELETE FROM PostApprovals WHERE Post_ID = $postID AND  Member_ID = $ID";
mysql_query($sql) or die(mysql_error());


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

echo "<div id = 'approvals$postID'>";

if (mysql_numrows($result2) > 0) {

    echo '<form>';
    echo '<input type ="hidden" class = "postID" value = "' . $postID . '" />';
    echo '<input type ="hidden" class = "id" value="' . $id . '"/>';
    echo '<input type ="hidden" class = "mediaID" value = "' . $mediaID . '" />';
    echo '<input type ="hidden" class = "mediaName" value ="' . $mediaName . '" />';
    echo '<input type ="hidden" class = "mediaType" value = "' . $mediaType . '" />';
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
    echo '<input type ="hidden" class = "type" id = "mediaType" value = "' . $mediaType . '" />';
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

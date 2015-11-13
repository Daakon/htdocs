<?php

require 'imports.php';
// handle approves

$mediaName = $_POST['mediaName'];
$mediaType = $_POST['mediaType'];
$postID = $_POST['postID'];
$mediaID = $_POST['mediaID'];
$mediaDate = $_POST['mediaDate'];

$ID = $_POST['ID'];


$sql = "DELETE FROM MediaApprovals WHERE Media_ID = $mediaID AND  Member_ID = $ID";
mysql_query($sql) or die(mysql_error());


//=========================================================================================================================//
//BELOW IS END OF BULLETIN Approval HANDLING CODE ==========================================================================//

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

        echo '&nbsp;<span style = "color:red;font-weight:bold;font-size:16px">' . $approvals . '</font>';
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

        echo '&nbsp;<span style = "color:red;font-weight:bold;font-size:16px">' . $approvals . '</font>';
    }
    echo '</form>';
}
echo "</div>"; // end of approval div

?>

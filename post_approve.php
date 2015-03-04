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

$sql2 = "SELECT * FROM PostApprovals WHERE Post_ID = '$postID' AND Member_ID = '$ID' ";
$result2 = mysql_query($sql2) or die(mysql_error());


// show disapprove if members has approved the post
echo '<table>';
echo '<tr>';
echo '<td>';
echo "<div id = 'approvals$postID'>";


if (mysql_numrows($result2) > 0) {

    echo '<form>';

    echo '<input type ="hidden" class = "postID" id = "postID" value = "'.$postID.'" />';
    echo '<input type ="button" class = "btnDisapprove" />';

    if ($approvals > 0) {
        //echo '<tr><td>';

        echo '&nbsp;<span style = "color:red;font-weight:bold;font-size:16">'.$approvals.'</font>';
    }
    echo '</form>';
}

else {
    echo '<form>';

    echo '<input type ="hidden" class = "postID" id = "postID" value="'.$postID.'"/>';
    echo '<input type ="button" class = "btnApprove" />';

    if ($approvals > 0) {
        //echo '<tr><td>';

        echo '&nbsp;<span style = "color:red;font-weight:bold;font-size:16">'.$approvals.'</font>';
    }
    echo '</form>';
}

echo '</td></tr></table>';

//-------------------------------------------------------------
// End of approvals
//-----------------------------------------------------------

?>
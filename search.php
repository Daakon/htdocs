
<table id="tblGroupChat" style="margin-bottom:0px;background-color: white;">

    <?php

require 'imports.php';
$ID = $_SESSION['ID'];
$sql1 = "SELECT BlockedID, BlockerID FROM Blocks WHERE (BlockerID = $ID Or BlockedID = $ID)";
$result1 = mysql_query($sql1) or die(logError(mysql_error(), $url, "Getting IDs for all post commentors"));

$blockIDs = array();

//Iterate over the results and sort out the biz ids from the consumer ones.
while ($rows = mysql_fetch_assoc($result1)) {
    array_push($blockIDs, $rows['BlockedID'], $rows['BlockerID']);
}


if($_POST)
{
    $q=$_POST['search'];
    $sql =mysql_query("select ID,FirstName,LastName,Username from Members where concat(FirstName,'',LastName) like '%$q%'
    and ID Not in ( '" . implode($blockIDs, "', '") . "' ) And (IsActive = 1) And (IsSuspended = 0) order by id LIMIT 5");

    while($row=mysql_fetch_array($sql)) {
        $name = $row['FirstName'] . ' ' . $row['LastName'];
        $firstName = $row['FirstName'];
        $receiverID = $row['ID'];
        $username = $row['Username'];

        if (checkBlock($ID, $receiverID)) {
            $display = "display:none;";
        }

        ?>

        <br/>
        <tr>
            <td id="tbName"
                style="border-top:1px solid #e3e3e3;border-bottom:1px solid #e3e3e3;padding:5px;<?php echo $display ?>">
                <a href="/<?php echo $username ?>">
                    <img src="<?php echo get_users_photo_by_id($receiverID) ?>"
                     style="width:50px; height:50px; float:left; margin-right:6px;display:inline-table"/><?php echo $name; ?>
                </a>

            </td>
        </tr>


        <?php
    }

}
?>

</table>

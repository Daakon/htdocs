<?php
require 'imports.php';

get_head_files();
get_header();

$ID = $_SESSION['ID'];
?>

<?php
if (isset($_POST['unblock']) && $_POST['unblock'] == "Unblock This User") {
    $blockedID = $_POST['blockedID'];
    $ID = $_POST['ID'];

    $scrollx = $_REQUEST['scrollx'];
    $scrolly = $_REQUEST['scrolly'];

    $sql = "DELETE FROM Blocks WHERE BlockerID = $ID And BlockedID = $blockedID";
    mysql_query($sql) or die(mysql_error());
    echo "<script>location='/settings'</script>";
}
?>


<?php
if (isset($_POST['block']) && $_POST['block'] == "Block This User") {
    $blockedID = $_POST['blockedID'];
    $ID = $_POST['ID'];

    $scrollx = $_REQUEST['scrollx'];
    $scrolly = $_REQUEST['scrolly'];

    $sql = "INSERT INTO Blocks (BlockerID,   BlockedID) Values
                              ('$ID',  '$blockedID')";
    mysql_query($sql) or die(mysql_error());
    echo "<script>location='/home?scrollx=$scrollx&scrolly=$scrolly'</script>";
}
?>

<div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 col-sm-12 col-xs-12 roll-call containerFlush" >

    <?php require 'profile_menu.php'; ?>

    <h4><div  onclick="document.getElementById('blockList').style.display = 'block';">Blocked Members</div></h4>

    <div id="blockList" style="display:none;">

        <div style="margin-bottom:20px;"  onclick="document.getElementById('blockList').style.display = 'none';"><img src="/images/close.png" height="25" width="25"/>
            <!-- adding onclick to hide this element when you click it -->
            Close
        </div>

        <table>

            <?php
            $ID = $_SESSION['ID'];
            $sql1 = "SELECT BlockedID, BlockerID FROM Blocks WHERE (BlockerID = $ID Or BlockedID = $ID)";
            $result1 = mysql_query($sql1) or die(logError(mysql_error(), $url, ""));

            $blockIDs = array();

            //Get blocked IDs
            while ($rows1 = mysql_fetch_assoc($result1)) {
                if ($rows1['BlockedID'] != $ID) {
                    array_push($blockIDs, $rows1['BlockedID']);
                    if ($rows1['BlockerID'] != $ID) {
                        array_push($blockIDs, $rows1['BlockerID']);
                    }
                }
            }

            $sql = "SELECT ID, FirstName, LastName From Members Where ID IN ( '" . implode($blockIDs, "', '") . "' ) ";
            $result = mysql_query($sql) or die(mysql_error());

            while ($rows = mysql_fetch_assoc($result)) {
                $memberID = $rows['ID'];
                $firstName = $rows['FirstName'];
                $lastName = $rows['LastName'];

                echo "<tr>
                        <td>";
                echo "$firstName $lastName"; ?>
                        </td>

                    <td style="padding-left:20px;">
                <form action="" method="post" onsubmit="return confirm('Do you really want to unblock this member?') ">
                    <input type="hidden" id="blockedID" name="blockedID" class="blockedID" value="<?php echo $memberID ?>" />
                    <input type="hidden" id="ID" name="ID" class="ID" value="<?php echo $ID ?>" />
                    <input type="submit" id="unblock" name="unblock" class="btnBlock" value="Unblock This User" />
                </form>
                    </td>
                </tr>
            <?php
            }
            ?>

        </table>
    </div>


</div>

<?php
require 'imports.php';
session_start();
$ID = $_SESSION['ID'];
$senderID = $_POST['senderID'];

$sql = "SELECT * FROM (SELECT * FROM Messages
                    WHERE ThreadOwner_ID = $ID
                    AND (Sender_ID = $senderID Or Receiver_ID = $senderID)
                    AND (IsDeleted = 0)
                    Order By ID DESC LIMIT 25, 10000) as ROWS Order By ID ASC ";
$result = mysql_query($sql);
if (mysql_num_rows($result) > 0) {
    $rowCount = true;
    while ($rows = mysql_fetch_assoc($result)) {
        $senderID = $rows['Sender_ID'];
        $message = $rows['Message'];
        $date = $rows['MessageDate'];
        // get receiver name
        $sql2 = "SELECT FirstName,LastName, ProfilePhoto,Username
                    FROM Members, Profile
                    WHERE Profile.Member_ID = $senderID
                    AND Members.ID = $senderID ";
        $result2 = mysql_query($sql2) or die(mysql_error());
        $rows2 = mysql_fetch_assoc($result2);
        $pic = $rows2['ProfilePhoto'];
        $name = $rows2['FirstName'] .' '.$rows2['LastName'];
        $username = $rows2['Username'];
        echo "
                    <div class='profileImageWrapper-Feed'>
                    <a href='/$username'>
                    <img src = '$mediaPath$pic' class='profilePhoto-Feed' alt='' />
                    </a>
                    </div>
                    <div class='profileNameWrapper-Feed'>
                    <a href='/$username'>
                    <div class=\"profileName-Feed\">$name</div>
                    </a>
                    </div>
                    ";
        echo "<div class='post' style='clear:both'>".nl2br($message)."</div>";
        echo "<div style='opacity:0.5'>".date('l F d Y g:i:s A',strtotime($date))."</div>";
        echo "<hr/>";
    }
}
?>
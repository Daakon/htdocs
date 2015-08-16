<?php

require 'connect.php';
require 'getSession_public.php';
require 'html_functions.php';
require 'mediaPath.php';
require 'model_functions.php';

get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];


$username = $_SESSION['Username'];

$sql = "SELECT * FROM Members
WHERE
Members.Username = '$username'
And Members.IsActive = 1 ";

$result = mysql_query($sql) or die(mysql_error());
$rows = mysql_fetch_assoc($result);
$memberID = $rows['ID'];
$fName = $rows['FirstName'];
$lName = $rows['LastName'];

if (mysql_numrows($result) == 0) {
    echo '<script>alert("This profile could not be found");location = "/index.php"</script>';
}
?>



<div class="container" >
    <div class="row row-padding">

        <ul class="list-inline">

            <?php require 'profile_menu_public.php'; ?>
        </ul>
        <br/><br/>

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call ">

            <h2>Messages</h2>
            <hr/>

            <?php
            // get only the person who text their profile
            $sql = "SELECT * FROM Messages WHERE ThreadOwner_ID = $ID AND (Sender_ID = $memberID Or Receiver_ID = $memberID) AND IsDeleted = 0 Order By ID DESC Limit 1";
            $result = mysql_query($sql) or die(mysql_error());

            if (mysql_numrows($result) > 0) {
                while ($rows = mysql_fetch_assoc($result)) {
                    if ($rows['Sender_ID'] != $ID) {
                        $otherID = $rows['Sender_ID'];
                    }
                    else {
                        $otherID = $rows['Receiver_ID'];
                    }

                    $subject = $rows['Subject'];

                    // get sender name
                    $sql2 = "SELECT FirstName, ProfilePhoto
                FROM Members, Profile
                WHERE Profile.Member_ID = $otherID
                AND Members.ID = $otherID ";

                    $result2 = mysql_query($sql2) or die(mysql_error());
                    $rows2 = mysql_fetch_assoc($result2);
                    $pic = $rows2['ProfilePhoto'];
                    $userName = $rows2['FirstName'];

                    $sql3 = "SELECT New FROM Messages WHERE ThreadOwner_ID = $ID And (Sender_ID = $otherID) AND (Receiver_ID = $ID) AND (New = 1)";
                    $result3 = mysql_query($sql3) or die(mysql_error());
                    $row3 = mysql_fetch_assoc($result3);

                    echo "<a href = '/view_messages_public.php?id=$otherID'><img src = '$mediaPath$pic' class='profilePhoto-Feed' alt='' /> $userName </a>";
                    if (mysql_num_rows($result3) > 0) { echo "<span style='color:red;font-weight:bold'>New</font>"; }
                    echo "<br/>";
                    echo "$subject";
                    echo "<hr/>";
                    echo "<br/>";
                }
            }
            else {
                if (!isset($_SESSION['ID']) || empty($_SESSION['ID'])) { echo "You must be logged in to see your message thread with this person"; }
                else {
                echo "You and $fName have no message history";
            }}
            ?>

            <!-------------------------------------------------------------------->
        </div>
    </div>

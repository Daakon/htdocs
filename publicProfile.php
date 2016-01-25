<?php
// render profile public view
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
preg_match("/[^\/]+$/",$url ,$match);
if ($_SESSION['UsernameUpdated'] == 1) {
    $username = $_SESSION['Username'];
}
else {
    $username = $match[0];
}

$memberID = get_id_from_username($username);

$sql = "SELECT
                        Members.ID As MemberID,
                        Members.FirstName As FirstName,
                        Members.LastName As LastName,
                        Members.Email As Email,
                        Members.Password As Password,
                        TIMESTAMPDIFF(YEAR, Members.DOB, CURDATE()) AS Age,
                        Profile.ProfilePhoto As ProfilePhoto,
                        Profile.ProfileVideo As ProfileVideo,
                        Profile.Poster As Poster,
                        Profile.Address As Address,
                        Profile.ShowAddress As ShowAddress,
                        Profile.City As City,
                        Profile.Zip As Zip,
                        Profile.ShowZip As ShowZip,
                        Profile.State As State,
                        Profile.Phone As Phone,
                        Profile.ShowPhone As ShowPhone,
                        Profile.About As About,
                        Profile.RSS As RSS
                        FROM Members, Profile
                        WHERE Members.ID = $memberID
                        AND Profile.Member_ID = $memberID ";

$result = mysql_query($sql) or die(logError(mysql_error(), $url, 'Getting public profile'));

if (mysql_num_rows($result) == 0) {
    echo "<script>alert('Profile not found');</script>";
    header('Location:home');
}

$rows = mysql_fetch_assoc($result);

$memberID = $rows['MemberID'];
$profilePhoto = $rows['ProfilePhoto'];
$profileVideo = $rows['ProfileVideo'];
$posterName = $rows['Poster'];
$firstName = $rows['FirstName'];
$lastName = $rows['LastName'];
$address = $rows['Address'];
$showAddress = $rows['ShowAddress'];
$city = $rows["City"];
$state = $rows['State'];
$zip = $rows['Zip'];
$showZip = $rows['ShowZip'];
$phone = $rows['Phone'];
$showPhone = $rows['ShowPhone'];
$about = $rows['About'];
$rss = $rows['RSS'];
$email = $rows['Email'];
$password = $rows['Password'];
$age = $rows['Age'];

?>

<div align ="center">

    <?php if (isset($_SESSION['ID'])) { ?>

    <div id="followDiv1">
        <table >
            <tr>
                <td >
                    <?php

                    $sqlFollow = "SELECT * FROM Follows WHERE Follower_ID = $ID And Followed_ID = $memberID ";
                    $resultFollow = mysql_query($sqlFollow) or die (logError(mysql_error(), $url, "Getting Follows"));

                    if (mysql_num_rows($resultFollow) == 0) {
                        echo '<form>';
                        echo '<input type = "hidden" class = "followerID" value = "' . $ID . '" />';
                        echo '<input type = "hidden" class = "followedID" value = "' . $memberID . '">';
                        echo '<input type = "button" class = "btnFollow" value = "Follow" />';
                        echo '</form>';
                    } else {
                        echo '<form>';
                        echo '<input type = "hidden" class = "followerID" value = "' . $ID . '" />';
                        echo '<input type = "hidden" class = "followedID" value = "' . $memberID . '">';
                        echo '<input type = "button" class = "btnUnfollow" value = "Unfollow" />';
                        echo '</form>';
                    }

                    ?>
                </td>
            </tr>
        </table>

        <?php
        $sqlFollowCount = "SELECT * FROM Follows WHERE Followed_ID = $memberID ";
        $sqlFollowCountResult = mysql_query($sqlFollowCount) or die(logError(mysql_error(), $url, "Getting Follow Count"));
        echo '<span class ="profileFont">' . $count = mysql_num_rows($sqlFollowCountResult) . '</span>';

        ?>
    </div>

    <?php }  ?>

    <br/>

    <!--Profile photo --------------------------------------------------------------------------------->


    <img src = "<?php echo $mediaPath.$profilePhoto ?>" class="profilePhoto" alt="Profile Photo" />


    <br/><br/>

    <!--Profile video --------------------------------------------------------------------------------->
    <?php if ($profileVideo != "default_video.png") { ?>
        <div align = "center">
            <video src = " <?php echo $videoPath . $profileVideo ?>" poster="/poster/<?php echo $posterName ?>"  preload="auto" autoplay="autoplay" muted controls />
        </div>
    <?php } ?>



    <h3>
        <?php echo "<div class=\"public-profile-label profileFont\">$firstName $lastName </div>" ?>
    </h3>


    <!--Profile ---------------------------------------------------------------------------------------->

    <?php
    if ($showZip == 0) {
        $zip = '';
    }
    if ($showAddress == 0) {
        $address = '';
    }
    if ($showPhone == 0) {
        $phone = '';
    }
    ?>

    <?php echo "<span class='profileFont'>$address </span> <br/>"; ?>
    <?php echo "<span class='profileFont'>$city, $state $zip </span> <br/>"; ?>
    <?php echo "<span class='profileFont'>$phone</span>"; ?>
    <br/><br/>


    <?php echo "<span class='profileFont'>$about</span>"; ?>

    <br/><br/>



    <?php if (isset($ID) && !empty($ID) && $memberID != $ID) { ?>


        <a href="/view_messages.php/<?php echo $username ?>"><img src="/images/messages.png" height="70" width="80" /></a>
    <?php } elseif ($memberID == $ID) { ?>
        <?php echo "Sorry but you can't message yourself, that's kind of weird anyway";

    } else { echo "<span class='red-font'>You must be logged in to message this person</span>"; }

    if (strlen($rss) > 0) {
        echo "<h3>$firstName's RSS Feed</h3>";
        require 'rss.php';

    }
    ?>

</div>
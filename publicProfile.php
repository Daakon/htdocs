<?php

// render profile public view
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
preg_match("/[^\/]+$/",$url ,$match);
$username = $match[0];

if ($username == '') {
    $username = 'playdoe';
}

$memberID = get_id_from_username($username);

$ID = $_SESSION['ID'];

    if (isset($ID) && !empty($ID)) {
        if (checkBlock($ID, $memberID)) {
            echo "<script>alert('This profile could not be found');location='/home' </script>";
            exit;
    }
    }


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

<div class="col-lg-offset-3 col-lg-6 col-md-offset-2 col-md-8 roll-call" align ="center">

    <?php if (isset($_SESSION['ID'])) { ?>

    <div id="followDiv1">
        <table >
            <tr>
                <td >
                    <?php

                    $sqlFollow = "SELECT * FROM Follows WHERE Follower_ID = $ID And Followed_ID = $memberID ";
                    $resultFollow = mysql_query($sqlFollow) or die (logError(mysql_error(), $url, "Getting Follows"));

                    if (isEmailValidated($ID)) {
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
        <video src = "<?php echo $videoPath . $profileVideo ?>" poster="/poster/<?php echo $posterName ?>"  preload="none" muted autoplay="autoplay" controls ></video>
        <br/>
    <?php } ?>

    <br/>

    <h3>
        <?php echo "<div class=\"public-profile-label profileFont\">$firstName $lastName </div>" ?>
    </h3>

    <h4>
        <?php echo $about ?>
    </h4>

    <!--Profile ---------------------------------------------------------------------------------------->

    <br/><br/>



    <?php if (isset($ID) && !empty($ID) && $memberID != $ID) { ?>


        <a href="/view_messages.php/<?php echo $username ?>"><img src="/images/messages.png" height="70" width="80" /></a>
    <?php } elseif ($memberID == $ID) { ?>
        <?php echo "Sorry but you can't message yourself, that's kind of weird anyway";

    } else { echo "<span class='red-font'>You must be logged in to message this person</span>"; }


    ?>

</div>
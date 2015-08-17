<?php
require 'connect.php';
require 'getSession_public.php';
require 'html_functions.php';
require 'mediaPath.php';
require 'findURL.php';
require 'model_functions.php';
require 'getState.php';

get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];

$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
preg_match("/[^\/]+$/",$url ,$match);
$username = $match[0];
$_SESSION['Username'] = $username;
$token = $match[1];
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
?>

<?php include('media_sizes.html'); ?>

<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

<?php $sql = "SELECT Poster FROM Profile WHERE Member_ID = $memberID";
$result = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_assoc($result);
$bgPhoto = $row['Poster'];
?>

<body style="
    background-image: url(/poster/<?php echo $bgPhoto ?>);
    display: block;
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    opacity: 0.9;
    background-repeat: no-repeat;
    background-position: 50% 0;
    -ms-background-size: cover;
    -o-background-size: cover;
    -moz-background-size: cover;
    -webkit-background-size: cover;
    background-size: cover;
    ">


<div class="container" >
    <div class="row row-padding">

        <?php require 'profile_menu_public.php'; ?>
        <br/><br/>

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call ">

            <?php


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
                        Profile.City As City,
                        Profile.State As State,
                        Profile.About As About
                        FROM Members, Profile
                        WHERE Members.ID = $memberID
                        AND Profile.Member_ID = $memberID ";

            $result = mysql_query($sql) or die(mysql_error());

            if (mysql_num_rows($result) == 0) {
                echo "<script>alert('Profile not found');</script>";
                header('Location:home.php');
            }

            $rows = mysql_fetch_assoc($result);

            $memberID = $rows['MemberID'];
            $profilePhoto = $rows['ProfilePhoto'];
            $profileVideo = $rows['ProfileVideo'];
            $posterName = $rows['Poster'];
            $firstName = $rows['FirstName'];
            $lastName = $rows['LastName'];
            $city = $rows["City"];
            $state = $rows['State'];
            $about = $rows['About'];
            $email = $rows['Email'];
            $password = $rows['Password'];
            $age = $rows['Age'];

            ?>


            <hr/>
            <br/>

            <!--Profile video --------------------------------------------------------------------------------->

            <div align ="center">
                <img src = "<?php echo $mediaPath.$profilePhoto ?>" class="profilePhoto" alt="Profile Photo" />
            </div>

            <?php if (get_is_service_provider($memberID) == 1) { ?>
            <!--Profile video --------------------------------------------------------------------------------->
            <div align ="center">
                <?php if ($profileVideo != "default_video.png") { ?>
                    <video src = " <?php echo $videoPath . $profileVideo ?>" poster="/poster/<?php echo $posterName ?>"  preload="auto" controls />
                <?php } else { ?>
                <h2>No Profile Video Uploaded</h2>
            </div>
        <?php }} ?>

            <div class="content-block">
                <h2>
            <?php echo $firstName ?>
                </h2>
            </div>

            <!--Profile ---------------------------------------------------------------------------------------->


            <br/><br/>

                   <div class="public-profile-label">City</div>
                   <?php echo $city ?>

            <br/><br/>

                    <div class="public-profile-label">State</div>
                   <?php echo $state ?>

            <br/><br/>

            <?php if (get_is_service_provider($memberID)) { ?>
            <div class="public-profile-label">About</div>
            <?php echo $about ?>
            <?php } ?>

                    <?php if (isset($ID) && !empty($ID) && $memberID != $ID) { ?>
                        <br/><br/>
                    <div class="public-profile-label">Message Me</div>
                        <a href="/view_messages.php?id=<?php echo $memberID ?>"><?php echo $username ?></a>
                    <?php } elseif ($memberID == $ID) { ?>
                    <?php echo "Sorry but you can't message yourself, that's kind of weird anyway";

                    } else { echo "<span style='color:red;'>You must be logged in to message this person</span>"; } ?>


            <!------------->
        </div>
    </div>
</div>

</body>
</html>

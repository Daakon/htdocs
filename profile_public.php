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


<body>


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
                        Members.DOB As DOB,
                        Profile.Poster As ProfilePhoto,
                        Profile.ProfileVideo As ProfileVideo,
                        Profile.Poster As Poster,
                        Profile.HomeCity As HomeCity,
                        Profile.HomeState As HomeState,
                        Profile.CurrentCity As CurrentCity,
                        Profile.CurrentState As CurrentState,
                        Profile.Interests As Interests,
                        Profile.Books As Books,
                        Profile.Movies As Movies,
                        Profile.Food As Food,
                        Profile.Dislikes As Dislikes,
                        Profile.Plan As Plan
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
            $homeCity = $rows["HomeCity"];
            $homeState = $rows['HomeState'];
            $currentCity = $rows['CurrentCity'];
            $currentState = $rows['CurrentState'];
            $interests = $rows['Interests'];
            $books = $rows['Books'];
            $movies = $rows['Movies'];
            $food = $rows['Food'];
            $dislikes = $rows["Dislikes"];
            $plan = $rows['Plan'];
            $email = $rows['Email'];
            $password = $rows['Password'];
            $dob = $rows['DOB'];


            ?>


            <hr/>
            <br/>

            <!--Profile video --------------------------------------------------------------------------------->

            <div align ="center">
                <?php if ($profileVideo != "default_photo.png") { ?>
                    <video src = " <?php echo $videoPath . $profileVideo ?>" poster="/poster/<?php echo $posterName ?>"  preload="auto" controls />
                <?php } else { ?>
                    <img src = "/poster/<?php echo $posterName ?>" class="defaultProfileVideo" alt="Profile Video" />
                <?php } ?>
            </div>



            <!--Profile ---------------------------------------------------------------------------------------->

            <br/>
            <br/>



                    <div class="public-profile-label">First Name</div>
                   <?php echo $firstName ?>

            <br/><br/>

                    <div class="public-profile-label">Last Name</div>
                    <?php echo $lastName ?>

            <br/><br/>

                   <div class="public-profile-label">Home City</div>
                   <?php echo $homeCity ?>

            <br/><br/>

                    <div class="public-profile-label">Home State</div>
                   <?php echo $homeState ?>

            <br/><br/>

                    <div class="public-profile-label">Current City</div>
                    <?php echo $currentCity ?>

            <br/><br/>

                    <div class="public-profile-label">Current State</div>
                    <?php echo $currentState ?>

            <br/><br/>

                    <div class="public-profile-label">Interests</div>
                   <?php echo $interests ?>

            <br/><br/>

                    <div class="public-profile-label">Favorite Books</div>
                    <?php echo $books ?>

            <br/><br/>

                    <div class="public-profile-label">Favorite Movies</div>
                    <?php echo $movies ?>

            <br/><br/>

                    <div class="public-profile-label">Favorite Food</div>
                    <?php echo $food ?>

            <br/><br/>

                    <div class="public-profile-label">Dislikes</div>
                    <?php echo $dislikes ?>

            <br/><br/>

                    <div class="public-profile-label">5 Year Plan</div>
                    <?php echo $plan ?>

            <br/><br/>

                    <?php if (isset($ID) && !empty($ID) && $memberID != $ID) { ?>
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

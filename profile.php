<?php
require 'connect.php';
require 'getSession.php';
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

?>


<?php

// handle upload profile pic
if (isset($_POST['photo']) && ($_POST['photo'] == "Upload Photo")) {
if (isset($_FILES['flPostPhoto']) && strlen($_FILES['flPostPhoto']['name']) > 1) {

    if ($_FILES['flPostPhoto']['size'] > 50000000) {
        echo '<script>alert("File is too large");</script>';
        exit;
    }

    // add unique id to image name to make it unique and add it to the file server
    $mediaName = $_FILES["flPostPhoto"]["name"];
    $mediaName = uniqid() . $mediaName;
    $mediaFile = $_FILES['flPostPhoto']['tmp_name'];
    $type = $_FILES["flPostPhoto"]["type"];

    require 'media_post_file_path.php';

    if ($type == "image/jpg" || $type == "image/jpeg") {

        $src = imagecreatefromjpeg($mediaFile);
    } else if ($type == "image/png") {

        $src = imagecreatefrompng($mediaFile);
    } else if ($type == "image/gif") {
        $src = imagecreatefromgif($mediaFile);
    } else {
        echo "<script>alert('Invalid File Type');</script>";
        exit;
    }

    $exif = exif_read_data($_FILES['flPostPhoto']['tmp_name']);

    if (!empty($exif['Orientation'])) {
        $ort = $exif['Orientation'];

        switch ($ort) {
            case 8:
                if (strstr($url, 'localhost:8888')) {
                    // local php imagerotate doesn't work

                } else {
                    $src = imagerotate($src, 90, 0);
                }
                break;
            case 3:
                if (strstr($url, 'localhost:8888')) {
                    // local php imagerotate doesn't work

                } else {
                    $src = imagerotate($src, 180, 0);
                }
                break;
            case 6:
                if (strstr($url, 'localhost:8888')) {
                    // local php imagerotate doesn't work
                } else {
                    $src = imagerotate($src, -90, 0);
                }
                break;
        }
    }

    require 'media_post_file_path.php';

    // photo file types
    $photoFileTypes = array("image/jpg", "image/jpeg", "image/png", "image/tiff",
        "image/gif", "image/raw");

    if ($type == "image/jpg" || $type == "image/jpeg") {
        imagejpeg($src, $postMediaFilePath, 100);

    } else if ($type == "image/png") {
        imagepng($src, $postMediaPath, 0, NULL);

    } else {
        imagegif($src, $postMediaFilePath, 100);

    }

    imagedestroy($src);
    imagedestroy($tmp);


    // write photo to media table
    $sql2 = "INSERT INTO Media (Member_ID, MediaName,     MediaType,  wasProfilePhoto, MediaDate) Values
                               ('$ID',     '$mediaName',  '$type',       1,            CURDATE())";
    mysql_query($sql2) or die(mysql_error());


    // update photo pointer in database
    $sql = "UPDATE Profile Set ProfilePhoto = '$mediaName' WHERE Member_ID = '$ID'";
    mysql_query($sql) or die(mysql_error());


    // alert everything is good
    echo "<script>alert('Update Successful');</script>";
}
}
?>

<?php

// handle upload profile pic
if (isset($_POST['video']) && ($_POST['video'] == "Upload Video")) {
    if (isset($_FILES['flPostVideo']) && strlen($_FILES['flPostVideo']['name']) > 1) {

        if ($_FILES['flPostVideo']['size'] > 50000000) {
            echo '<script>alert("File is too large");</script>';
            exit;
        }

        // add unique id to image name to make it unique and add it to the file server
        $mediaName = $_FILES["flPostVideo"]["name"];
        $mediaName = uniqid() . $mediaName;
        $mediaFile = $_FILES['flPostVideo']['tmp_name'];
        $type = $_FILES["flPostVideo"]["type"];

        require 'media_post_file_path.php';

        // video file types
        $videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
            "video/quicktime", "video/webm", "video/x-matroska",
            "video/x-ms-wmw");

        if (in_array($type, $videoFileTypes)) {
            $cmd = "ffmpeg -i $mediaFile -vf 'transpose=1' $mediaFile";
            exec($cmd);
            move_uploaded_file($mediaFile, $postMediaFilePath);
        }
        else {
            echo "<script>alet('Invalid File Type');</script>";
            exit;
        }

        imagedestroy($src);
        imagedestroy($tmp);


        // write photo to media table
        $sql2 = "INSERT INTO Media (Member_ID, MediaName,     MediaType,  wasProfilePhoto, MediaDate) Values
                               ('$ID',     '$mediaName',  '$type',       1,            CURDATE())";
        mysql_query($sql2) or die(mysql_error());


        // update photo pointer in database
        $sql = "UPDATE Profile Set ProfileVideo = '$mediaName' WHERE Member_ID = '$ID'";
        mysql_query($sql) or die(mysql_error());


        // alert everything is good
        echo "<script>alert('Update Successful');</script>";
    }
}
?>


<?php

// handle profile update
if (isset($_POST['submit']) && $_POST['submit'] == "Update Profile") {
    $firstName = $_POST['FirstName'];
    $lastName = $_POST['LastName'];
    $homeCity = $_POST['homeCity'];
    $homeState = $_POST['homeState'];
    $currentCity = $_POST['currentCity'];
    $currentState = $_POST['currentState'];
    $interests = $_POST['interests'];
    $books = $_POST['books'];
    $movies = $_POST['movies'];
    $food = $_POST['food'];
    $dislikes = $_POST['dislikes'];
    $plan = $_POST['plan'];
    $dob = $_POST['DOB'];
}

?>

<style>

    iframe {
        max-width: 100%;
        width:500px;
        max-height: 500px;
    }

    img {
        max-width: 100%;
        max-height:500px;
    }

    video {
        max-width: 100%;
        width:500px;
        max-height: 500px;
    }

    embed {
        max-width: 100%;
        max-height: 500px;
    }

    script {
        max-width: 100%;
        max-height: 500px;
    }

    .btnApprove {
        background: url("/images/gray_check.png") no-repeat;
        width: 30px;
        height: 30px;
        border: none;
    }

    .btnDisapprove {
        background: url("/images/red_check.png") no-repeat;
        width: 30px;
        height: 30px;
        border: none;
    }
</style>

<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>


<body>

<div class="container" >
    <div class="row row-padding">

        <a href ="/home.php" class="black-link"><h4>Roll Call</h4></a>
        <br/><br/>

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call ">

            <?php
            $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            preg_match("/[^\/]+$/",$url ,$match);
            $username = $match[0];

                $sql = "SELECT
                        Members.ID As MemberID,
                        Members.FirstName As FirstName,
                        Members.LastName As LastName,
                        Members.Email As Email,
                        Members.Password As Password,
                        Members.DOB As DOB,
                        Profile.ProfilePhoto As ProfilePhoto,
                        Profile.ProfileVideo As ProfileVideo,
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
                        WHERE Members.ID = $ID
                        AND Profile.Member_ID = $ID ";

            $result = mysql_query($sql) or die(mysql_error());

            if (mysql_num_rows($result) == 0) {
                echo "<script>alert('Profile not found'); location = 'home.php'</script>";
            }

            $rows = mysql_fetch_assoc($result);

//            $memberID = $rows['MemberID'];
            $profilePhoto = $rows['ProfilePhoto'];
            $profileVideo = $rows['ProfileVideo'];
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
            $username = $rows['Username'];


            ?>

            <div align ="center">
            <img src = "<?php echo $mediaPath.$profilePhoto ?>" class="profilePhoto" alt="Profile Photo" />
            </div>


            <form method="post" enctype="multipart/form-data" action="">
                <img src="/images/image-icon.png" class="img-icon" alt="Photos/Video"/>
                <strong>Upload A Profile Photo</strong>
                <input type="file" width="10px;" name="flPostPhoto" id="flPostPhoto"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="500000000"
                <br/>
                <br/>
                <input type="submit" class="post-button" name="photo" id="photo" value="Upload Photo"/>
            </form>

            <br/>
            <hr/>
            <br/>

            <!--Profile video --------------------------------------------------------------------------------->

            <div align ="center">
                <?php if ($profileVideo != "default_video.png") { ?>
                <video src = "<?php echo $mediaPath.$profileVideo ?>" class="profileVideo" alt="Profile Video"  frameborder = "1" controls preload="none" SCALE="ToFit" />
                <?php } else { ?>
                <img src = "<?php echo $mediaPath.$profileVideo ?>" class="defaultProfileVideo" alt="Profile Video" />
                <?php } ?>
            </div>

            <form method="post" enctype="multipart/form-data" action="">
                <img src="/images/image-icon.png" class="img-icon" alt="Photos/Video"/>
                <strong>Upload A Profile Video</strong>
                <input type="file" width="10px;" name="flPostVideo" id="flPostVideo"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="500000000"
                <br/>
                <br/>
                <input type="submit" class="post-button" name="video" id="video" value="Upload Video"/>
            </form>

            <!--Profile ---------------------------------------------------------------------------------------->

            <br/>
            <p id="notice"></p>
            <br/>

            <form id="ajax-form" method="post" action = "">

                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <br/>
                    <input type ="text" class="form-control" id="FirstName" name="FirstName" value="<?php echo $firstName ?>" onblur="saveData();" />
                </div>

                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" class="form-control" id="LastName" name="LastName" value="<?php echo $lastName ?>" />
                 </div>

                <div class="form-group">
                    <label for="homeCity">Home City</label>
                    <input type="text" class="form-control" id="HomeCity" name="HomeCity" value="<?php echo $homeCity ?>" />
                </div>

                <div class="form-group">
                <label for="homeState">State</label>
                <select class="form-control">
                    <option value="<?php echo $state ?>"><?php echo $homeState ?></option>
                    <?php getState() ?>
                </select>
                </div>

                <div class="form-group">
                <label for="currentCity">Current City</label>
                <input type="text" class="form-control" id="CurrentCity" name="CurrentCity" value="<?php $currentCity ?>" />
                </div>

                <div class="form-group">
                    <label for="currentState">Current State</label>
                    <select class="form-control">
                        <option value="<?php echo $state ?>"><?php echo $currentState ?></option>
                        <?php getState() ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="interests">Interests</label>
                    <textarea class="form-control" id="Interests" name="Interests"><?php echo $interests ?> </textarea>
                </div>

                <div class="form-group">
                    <label for="books">Favorite Books</label>
                    <textarea class="form-control" id="Books" name="Books" ><?php echo $books ?></textarea>
                </div>

                <div class="form-group">
                    <label for="movies">Favorite Movies</label>
                    <textarea class="form-control" id="Movies" name="Movies"><?php echo $movies ?></textarea>
                </div>

                <div class="form-group">
                    <label for="food">Favorite Food</label>
                    <textarea class="form-control" id="Food" name="Food"><?php echo $food ?></textarea>
                </div>

                <div class="form-group">
                    <label for="dislikes">Dislikes</label>
                    <input type="text" class="form-control" id="Dislikes" name="Dislikes" value="<?php echo $dislikes ?>" />
                </div>

                <div class="form-group">
                    <label for="plan">5 Year Plan</label>
                    <textarea class="form-control" id="Plan" name="Plan"><?php echo $plan ?></textarea>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="Email" name="Email" value="<?php echo $email ?>" />
                </div>

                <div class="form=group">
                    <label for="DOB">Date Of Birth</label>
                    <input type="date" class="form-control" id="DOB" name="DOB" value="<?php echo $dob ?>" />
                </div>
                <br/>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="Username" name="Username" value="<?php echo $username ?>" readonly="readonly" />
                </div>

                <input type = "submit" value = "Update" name = "updateProfile" id = "updateProfile" class="btn btn-default" />
            </form>

          <!------------->
            </div>
        </div>
    </div>


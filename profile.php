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

        $exif = @exif_read_data($mediaFile);

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
            imagepng($src, $postMediaFilePath, 0, NULL);

        } else {
            imagegif($src, $postMediaFilePath, 100);

        }



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

// handle upload profile video
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

            // where ffmpeg is located
            $ffmpeg = '/usr/bin/ffmpeg';

            // poster file name
            $posterName = "poster".uniqid().".jpg";

            //where to save the image
            $poster = "$posterPath$posterName";


            //time to take screenshot at
            $interval = 5;

            //screenshot size
            //$size = '440x280'; -s $size -f

            //ffmpeg command
            $cmd = "$ffmpeg -i \"$postMediaFilePath\" -r 1  image2 $poster 2>&1";

            exec($cmd);
            $poster = imagecreatefromjpeg($poster);
            $exif = @exif_read_data($poster);

            if ( isset($exif['Orientation']) && !empty($exif['Orientation']) ) {

                // Decide orientation
                if ( $exif['Orientation'] == 3 ) {
                    $rotation = 180;
                } else if ( $exif['Orientation'] == 6 ) {
                    $rotation = 90;
                } else if ( $exif['Orientation'] == 8 ) {
                    $rotation = -90;
                } else {
                    $rotation = 0;
                }

                // Rotate the image
                if ( $rotation ) {
                    $img = imagerotate($poster, $rotation, 0);
                    imagejpeg($img, $posterPath.$posterName, 100);
                }
            }
            else {
                // if we cannot determine the exif data
                // then we will rotate the image if it is wider than it is tall
                // this is the best fallback so far.
                $size = getimagesize("$posterPath$posterName");
                $width = $size[0];
                $height = $size[1];
                if ($width > $height) {
                    $img = imagerotate($poster, -90, 0);
                    imagejpeg($img, $posterPath.$posterName, 100);
                }
            }
        }
        else {
            echo "<script>alet('Invalid File Type');</script>";
            exit;
        }


        // write photo to media table
        $sql2 = "INSERT INTO Media (Member_ID, MediaName,     MediaType,  wasProfilePhoto, MediaDate,  Poster      ) Values
                               ('$ID',     '$mediaName',  '$type',       1,            CURDATE(),     '$posterName')";
        mysql_query($sql2) or die(mysql_error());


        // update photo pointer in database
        $sql = "UPDATE Profile Set ProfileVideo = '$mediaName', Poster = '$posterName' WHERE Member_ID = '$ID'";
        mysql_query($sql) or die(mysql_error());


        // alert everything is good
        echo "<script>alert('Update Successful');</script>";
    }
}
?>


<?php

// handle profile update
if (isset($_POST['updateProfile']) && $_POST['updateProfile'] == "Update") {
    $firstName = $_POST['FirstName'];
    $lastName = $_POST['LastName'];
    $homeCity = $_POST['HomeCity'];
    $homeState = $_POST['HomeState'];
    $currentCity = $_POST['CurrentCity'];
    $currentState = $_POST['CurrentState'];
    $interests = mysql_real_escape_string($_POST['Interests']);
    $books = mysql_real_escape_string($_POST['Books']);
    $movies = mysql_real_escape_string($_POST['Movies']);
    $food = mysql_real_escape_string($_POST['Food']);
    $dislikes = mysql_real_escape_string($_POST['Dislikes']);
    $plan = mysql_real_escape_string($_POST['Plan']);
    $dob = $_POST['DOB'];
    $emailStatus = $_POST['EmailStatus'];
    $password = $_POST['Password'];
    $username = $_POST['Username'];

//only if password has changed do we hash it
    if (check_password($ID, $password)==false) {
        $password = md5($password);
    }
    // update Member table first
    $sql = "Update Members
          Set
          FirstName = '$firstName',
          LastName = '$lastName',
          DOB = '$dob',
          EmailActive = '$emailStatus ',
          Password = '$password'
          WHERE ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());

    // update Profile table
    $sql = "Update Profile
            Set HomeCity = '$homeCity',
            HomeState = '$homeState',
            CurrentCity = '$currentCity',
            CurrentState = '$currentState',
            Interests = '$interests',
            Books = '$books',
            Movies = '$movies',
            Food = '$food',
            Dislikes = '$dislikes',
            Plan = '$plan'
             WHERE Member_ID = $ID ";
    mysql_query($sql) or die(mysql_error());
    echo "<script>alert('Update Successful');</script>";

    $scrollx = $_REQUEST['scrollx'];
    $scrolly = $_REQUEST['scrolly'];
}

?>


<?php

// handle profile text

require 'class-Clockwork.php';

if (isset($_POST['text']) && $_POST['text'] == "Text") {
    $result = mysql_query("SELECT Username FROM Members WHERE ID = $ID");
    $row = mysql_fetch_assoc($result);
    $username = $row['Username'];

    $number = $_POST['number'];
    $number = "1".$number;
    $name = get_users_name($ID);
    $API_KEY = '7344d6254838e6d2c917c4cb78305a3235ba951d';
    try
    {
        // Create a Clockwork object using your API key
        $clockwork = new Clockwork( $API_KEY );
        $domain;

        if (strstr($url, "dev")) {
            $domain = "http://dev.rapportbook.com/profile_public.php/";
        }
        else {
            $domain = "http://rapportbook.com/profile_public.php/";
        }

        // Setup and send a message
        $text = "$name has shared their profile with you. $domain$username";
        $message = array( 'to' => $number, 'message' => $text );
        $result = $clockwork->send( $message );

        // Check if the send was successful
        if($result['success']) {
            //echo 'Message sent - ID: ' . $result['id'];
            echo "<script>alert('SMS Sent');</script>";
        } else {
            $error = $result['error_message'];
            echo "<script>alert('Message failed - Error: $error');</script>";
        }
    }
    catch (ClockworkException $e)
    {
        echo 'Exception sending SMS: ' . $e->getMessage();
    }

}
?>

<?php include('media_sizes.html'); ?>

<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

<script>
    function checkFields() {
        var firstName = document.getElementById('FirstName').value;
        var lastName = document.getElementById('LastName').value;

        if (firstName == "") {
            alert('First Name cannot be empty');
            return false;
        }
        if (lastName == "") {
            alert('Last Name cannot be empty');
            return false;
        }
        return true;
    }
</script>



<body>


<div class="container" >

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script src = "jquery-1.8.3.min.js"></script>

    <span id = "newMessageCount" style = "padding-left:30px;width:150px;"></span>

    <div class="row row-padding">

        <?php require 'profile_menu.php'; ?>

        <br/><br/>

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call ">

            <?php
            $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            preg_match("/[^\/]+$/",$url ,$match);
            $username = $match[0];

            require 'checkUsername.php';

            $sql = "SELECT
                        Members.ID As MemberID,
                        Members.FirstName As FirstName,
                        Members.LastName As LastName,
                        Members.Email As Email,
                        Members.Password As Password,
                        Members.DOB As DOB,
                        Members.EmailActive As EmailStatus,
                        Profile.ProfilePhoto As ProfilePhoto,
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
                        WHERE Members.ID = $ID
                        AND Profile.Member_ID = $ID ";

            $result = mysql_query($sql) or die(mysql_error());

            if (mysql_num_rows($result) == 0) {
                echo "<script>alert('Profile not found');</script>";
                header('Location:home.php');
            }

            $rows = mysql_fetch_assoc($result);

            //            $memberID = $rows['MemberID'];
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
            $emailStatus = $rows['EmailStatus'];

            if (strlen($posterName) == 0) {
                $posterName = "video-bg.jpg";
            }

            ?>

            <div align ="center">

                <script>
                    function showTextBox(textDiv) {
                        var textBox = document.getElementById(textDiv);
                        if (textBox.style.display == 'none') {
                            textBox.style.display = 'block';
                        }
                        else {
                            textBox.style.display = 'none';
                        }
                    }
                </script>

                <input onclick="showTextBox('textDiv')" type="image" value="Share" src="/images/share.png" height="50px" width="50px" style="margin-top:10px" />
                <br/>

                <form method="post" action="">
                    <div id="textDiv" style="display:none;">
                        <div class="form-group">
                            <label for="text">Text Your Profile</label>

                            <input type="text" id="number" name="number" class="form-control text-center" style="width:150px;" placeholder="2125551212"/>
                        </div>
                        <input type="submit" id="text" name="text" value="Text" style="border-radius: 10px" class="btn btn-default" />
                    </div>
                </form>

                <img src = "<?php echo $mediaPath.$profilePhoto ?>" class="profilePhoto" alt="Profile Photo" />
            </div>


            <form method="post" enctype="multipart/form-data" action="" >
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

            <script>
                // show uploading
                function showUploading() {
                    document.getElementById("progress").style.display = "block";
                }
            </script>

            <div align ="center">
                <?php if ($profileVideo != "default_video.png") { ?>
                   <video src = " <?php echo $videoPath . $profileVideo ?>" poster="/poster/<?php echo $posterName ?>"  preload="auto" controls />
                <?php } else { ?>
                    <img src = "<?php echo $mediaPath.$profileVideo ?>" class="defaultProfileVideo" alt="Profile Video" />
                <?php } ?>
            </div>

            <form method="post" enctype="multipart/form-data" action="" onsubmit="showUploading()">
                <img src="/images/image-icon.png" class="img-icon" alt="Photos/Video"/>
                <strong>Upload A Profile Video</strong>
                <input type="file" width="10px;" name="flPostVideo" id="flPostVideo"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="500000000"
                <br/>
                <div id="progress" style="display:none;">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            <span class="sr-only">Loading</span>
                        </div>
                    </div>
                </div>
                <br/>
                <input type="submit" class="post-button" name="video" id="video" value="Upload Video"/>

            </form>

            <!--Profile ---------------------------------------------------------------------------------------->

            <br/>
            <p id="notice"></p>
            <br/>

            <form id="ajax-form" method="post" action = "" onsubmit="return checkFields();">

                <div class="form-group">
                    <label for="FirstName">First Name</label>
                    <br/>
                    <input type ="text" class="form-control" id="FirstName" name="FirstName" value="<?php echo $firstName ?>" onblur="saveData();" />
                </div>

                <div class="form-group">
                    <label for="LastName">Last Name</label>
                    <input type="text" class="form-control" id="LastName" name="LastName" value="<?php echo $lastName ?>" />
                </div>

                <div class="form-group">
                    <label for="HomeCity">Home City</label>
                    <input type="text" class="form-control" id="HomeCity" name="HomeCity" value="<?php echo $homeCity ?>" />
                </div>

                <div class="form-group">
                    <label for="HomeState">Home State</label>
                    <select id="HomeState" name="HomeState" class="form-control">
                        <option  value="<?php echo $homeState ?>"><?php echo $homeState ?></option>
                        <?php getState() ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="CurrentCity">Current City</label>
                    <input type="text" class="form-control" id="CurrentCity" name="CurrentCity" value="<?php echo $currentCity ?>" />
                </div>

                <div class="form-group">
                    <label for="CurrentState">Current State</label>
                    <select id="CurrentState" name="CurrentState" name="" class="form-control">
                        <option value="<?php echo $currentState ?>"><?php echo $currentState ?></option>
                        <?php getState() ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="Interests">Interests</label>
                    <textarea class="form-control" id="Interests" name="Interests"><?php echo $interests ?> </textarea>
                </div>

                <div class="form-group">
                    <label for="Books">Favorite Books</label>
                    <textarea class="form-control" id="Books" name="Books" ><?php echo $books ?></textarea>
                </div>

                <div class="form-group">
                    <label for="Movies">Favorite Movies</label>
                    <textarea class="form-control" id="Movies" name="Movies"><?php echo $movies ?></textarea>
                </div>

                <div class="form-group">
                    <label for="Food">Favorite Food</label>
                    <textarea class="form-control" id="Food" name="Food"><?php echo $food ?></textarea>
                </div>

                <div class="form-group">
                    <label for="Dislikes">Dislikes</label>
                    <input type="text" class="form-control" id="Dislikes" name="Dislikes" value="<?php echo $dislikes ?>" />
                </div>

                <div class="form-group">
                    <label for="Plan">5 Year Plan</label>
                    <textarea class="form-control" id="Plan" name="Plan"><?php echo $plan ?></textarea>
                </div>

                <div class="form-group">
                    <label for="Email">Email</label>
                    <input type="email" class="form-control" id="Email" name="Email" value="<?php echo $email ?>" />
                </div>

                <div class="form-group">
                    <label for="DOB">Date Of Birth</label>
                    <input type="date" class="form-control" id="DOB" name="DOB" value="<?php echo $dob ?>" />
                </div>
                <br/>

                <?php
                $otherEmailValue = "";
                $otherEmailText = "";
                if ($emailStatus == 1) {
                    $emailStatusText = "On";
                    $otherEmailValue = 0;
                    $otherEmailText = "Off";
                }
                else {
                    $emailStatusText = "Off";
                    $otherEmailValue = 1;
                    $otherEmailText = "On";
                }
                ?>

                <div class="form-group">
                    <label for="EmailStatus">Email Notification</label>
                    <select id="EmailStatus" name="EmailStatus" name="EmailStatus" class="form-control">
                        <option value="<?php echo $emailStatus ?>"><?php echo $emailStatusText ?></option>
                        <option value="<?php echo $otherEmailValue ?>"><?php echo $otherEmailText ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="username">Password</label>
                    <input type="password" class="form-control" id="Password" name="Password" value="<?php echo $password ?>"  />
                </div>

                <div class="form-group">
                    <label for="password">Username</label>
                    <input type="text" class="form-control" id="Username" name="Username" value="<?php echo $username ?>" readonly="readonly" />
                </div>

                <input type = "submit" value = "Update" name = "updateProfile" id = "updateProfile" class="btn btn-default" />
            </form>

            <!------------->
        </div>
    </div>
</div>

</body>
</html>

<?php

$scrollx = 0;
$scrolly = 0;

if(!empty($_REQUEST['scrollx'])) {
    $scrollx = $_REQUEST['scrollx'];
}

if(!empty($_REQUEST['scrolly'])) {
    $scrolly = $_REQUEST['scrolly'];
}
?>

<script type="text/javascript">

    window.scrollTo(<?php echo "$scrollx" ?>, <?php echo "$scrolly" ?>);

</script>
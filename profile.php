<?php
require 'connect.php';
require 'model_functions.php';
require 'mediaPath.php';
require 'getSession.php';
require 'html_functions.php';
require 'findURL.php';
require 'email.php';
require 'category.php';
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
        // handle transparency
        imagesavealpha($src, true);
        if ($type == "image/jpg" || $type == "image/jpeg") {
            imagejpeg($src, $postMediaFilePath, 50);
        } else if ($type == "image/png") {
            imagepng($src, $postMediaFilePath, 0, NULL);
        } else {
            imagegif($src, $postMediaFilePath, 50);
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
            $cmd = "$ffmpeg -i \"$postMediaFilePath\" -r 1 -f image2 $poster 2>&1";
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
                    imagejpeg($img, $posterPath.$posterName, 50);
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
                    imagejpeg($img, $posterPath.$posterName, 50);
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
    $city = $_POST['City'];
    $state = $_POST['State'];
    $zip = $_POST['Zip'];
    $phone = $_POST['Phone'];
    $email = $_POST['Email'];
    $about = $_POST['About'];
    $interest = $_POST['Interest'];
    $dob = $_POST['DOB'];
    $emailStatus = $_POST['EmailStatus'];
    $password = $_POST['Password'];
    $username = $_POST['Username'];
//only if password has changed do we hash it
    if (check_password($ID, $password)==false) {
        $password = md5($password);
    }

    // check email
    if (check_email($email) == false) {
        echo "<script>alert('Invalid Email');location='profile.php/$username'</script>";
        exit();
    }

    // update Member table first
    $sql = "Update Members
          Set
          FirstName = '$firstName',
          LastName = '$lastName',
          DOB = '$dob',
          Email = '$email',
          EmailActive = '$emailStatus ',
          Interest = '$interest',
          Password = '$password'
          WHERE ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());
    // update Profile table
    $sql = "Update Profile
            Set City = '$city',
                State = '$state',
                Phone = '$phone',
                Zip = '$zip',
                About = '$about'
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

<script>
    // show uploading
    function showPhotoUploading() {
        document.getElementById("PhotoProgress").style.display = "block";
    }
    // show uploading
    function showVideoUploading() {
        document.getElementById("VideoProgress").style.display = "block";
    }
</script>

<script>
    function capFname() {
        var fName = document.getElementById('FirstName').value;
        document.getElementById('FirstName').value = fName.substring(0,1).toUpperCase() + fName.substring(1, fName.length);
    }
</script>

<script>
    function capLname() {
        var lName = document.getElementById('LastName').value;
        document.getElementById('LastName').value = lName.substring(0,1).toUpperCase() + lName.substring(1, lName.length);
    }
</script>

<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
preg_match("/[^\/]+$/",$url ,$match);
$username = $match[0];
$_SESSION['Username'] = $username;
$token = $match[1];
$username = $_SESSION['Username'];
$profileID = get_id_from_username($username);
$sql = "SELECT ProfilePhoto FROM Profile WHERE Member_ID = $profileID";
$result = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_assoc($result);
$bgPhoto = $row['ProfilePhoto'];
?>

<body style="
    background-image: url(/media/<?php echo $bgPhoto ?>);
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

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script src = "jquery-1.8.3.min.js"></script>

    <span id = "newMessageCount" style = "padding-left:30px;width:150px;"></span>

    <div class="row row-padding">

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 ">
            <ul class="list-inline">

                <?php require 'profile_menu.php'; ?>
            </ul>
        </div>

        <br/>

        <div class="col-md-8  col-lg-8 col-md-offset-2 col-lg-offset-2 roll-call ">

            <?php
            $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            preg_match("/[^\/]+$/",$url ,$match);
            $username = $match[0];
            require 'checkUsername.php';
            $sql = "SELECT DISTINCT
                        Members.ID As MemberID,
                        Members.FirstName As FirstName,
                        Members.LastName As LastName,
                        Members.Email As Email,
                        Members.Password As Password,
                        Members.DOB As DOB,
                        Members.Interest As Interest,
                        Members.EmailActive As EmailStatus,
                        Profile.ProfilePhoto As ProfilePhoto,
                        Profile.ProfileVideo As ProfileVideo,
                        Profile.Poster As Poster,
                        Profile.City As City,
                        Profile.State As State,
                        Profile.Zip As Zip,
                        Profile.Phone As Phone,
                        Profile.About As About
                        FROM Members, Profile
                        WHERE Members.ID = $ID
                        AND Profile.Member_ID = $ID
                        Order By MemberID ";
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
            $city = $rows["City"];
            $state = $rows['State'];
            $zip = $rows['Zip'];
            $phone = $rows['Phone'];
            $about = $rows['About'];
            $email = $rows['Email'];
            $password = $rows['Password'];
            $dob = $rows['DOB'];
            $interest = $rows['Interest'];
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

                <!--<input onclick="showTextBox('textDiv')" type="image" value="Share" src="/images/share.png" height="50px" width="50px" style="margin-top:10px" />-->
                <br/>


                <img src = "<?php echo $mediaPath.$profilePhoto ?>" class="profilePhoto" alt="Profile Photo" />
            </div>


            <form method="post" enctype="multipart/form-data" action="" >
                <img src="/images/image-icon.png" class="img-icon" alt="Photos/Video"/>
                <strong>Upload A Profile Photo</strong>
                <input type="file" width="10px;" name="flPostPhoto" id="flPostPhoto"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="500000000"
                <br/>
                <div id="PhotoProgress" style="display:none;">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            <span class="sr-only">Loading</span>
                        </div>
                    </div>
                </div>
                <br/>
                <input type="submit" class="post-button" name="photo" id="photo" value="Upload Photo" onclick="showPhotoUploading()" />
            </form>

            <br/>
            <hr/>

            <?php

            if (get_is_service_provider($ID) == 1) {?>
            <!--Profile video --------------------------------------------------------------------------------->
            <div align ="center">
                <?php if ($profileVideo != "default_video.png") { ?>
                    <video src = " <?php echo $videoPath . $profileVideo ?>" poster="/poster/<?php echo $posterName ?>"  preload="auto" controls />
                <?php } else { ?>
                   <h5>No Profile Video Uploaded</h5>
            </div>
                <?php } ?>

            <form method="post" enctype="multipart/form-data" action="" onsubmit="showUploading()">
                <img src="/images/image-icon.png" class="img-icon" alt="Photos/Video"/>
                <strong>Upload A Profile Video</strong>
                <input type="file" width="10px;" name="flPostVideo" id="flPostVideo"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="500000000" />
                <br/>
                <div id="VideoProgress" style="display:none;">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            <span class="sr-only">Loading</span>
                        </div>
                    </div>
                </div>
                <br/>
                <input type="submit" class="post-button" name="video" id="video" value="Upload Video" onclick="showVideoUploading()" />
            </form>
            <!--Profile ---------------------------------------------------------------------------------------->
<?php } ?>

            <br/>
            <p id="notice"></p>
            <br/>

            <form id="ajax-form" method="post" action = "" onsubmit="return checkFields();">

                <div class="form-group">
                    <label for="FirstName">First Name</label>
                    <br/>
                    <input type ="text" class="form-control" id="FirstName" name="FirstName" value="<?php echo $firstName ?>" onblur="capFname()" />
                </div>

                <div class="form-group">
                    <label for="LastName">Last Name</label>
                    <input type="text" class="form-control" id="LastName" name="LastName" value="<?php echo $lastName ?>" onblur="capLname()" />
                </div>

                <div class="form-group">
                    <label for="City">City</label>
                    <input type="text" class="form-control" id="City" name="City" value="<?php echo $city ?>" />
                </div>

                <div class="form-group">
                    <label for="State">State</label>
                    <select id="State" name="State" class="form-control">
                        <option  value="<?php echo $state ?>"><?php echo $state ?></option>
                        <?php getState() ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="Zip">Zip Code</label>
                    <input type="text" class="form-control" id="Zip" name="Zip" value="<?php echo $zip ?>" />
                </div>

                <div class="form-group">
                    <label for="Phone">Phone</label>
                    <input type="text" class="form-control" id="Phone" name="Phone" value="<?php echo $phone ?>" />
                </div>


                    <label for="Service">Primary Interest</label>
                <div class="form-group">
                    <select class="form-control input-lg" id="Interest" name="Interest">
                        <option value="<?php echo $interest ?>"><?php echo $interest ?></option>
                        <?php echo category() ?>
                    </select>
                </div>


                <div class="form-group">
                    <label for="About">About</label>
                    <textarea class="form-control" id="About" name="About"><?php echo $about ?></textarea>
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

        <div >
            <!--Right Column -->
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
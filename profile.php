<?php
session_start();
if (!isset($_SESSION['ID']) && empty($_SESSION['ID'])) {
    $_SESSION['IsProfilePage'] = true;
}
else {
    $_SESSION['IsProfilePage'] = false;
}

require 'imports.php';
get_head_files();
get_header();

$ID = $_SESSION['ID'];
$urlUsername = get_username_from_url();
$_SESSION['Username'] = $urlUsername;

?>



<?php
// handle upload profile pic
if (isset($_POST['photo']) && ($_POST['photo'] == "Upload Photo")) {
    if (isset($_FILES['flPostPhoto']) && strlen($_FILES['flPostPhoto']['name']) > 1) {
        if ($_FILES['flPostPhoto']['size'] > 5000000000) {
            echo '<script>alert("File is too large");</script>';
            exit;
        }


        // add unique id to image name to make it unique and add it to the file server
        $mediaName = $_FILES["flPostPhoto"]["name"];
        // remove ALL WHITESPACE from image name
        $mediaName = preg_replace('/\s+/', '', $mediaName);
        // remove ALL SPECIAL CHARACTERS, Images paths are extremely sensitive
        $mediaName = str_replace('/[^A-Za-z0-9\-]/', '', $mediaName);
        $mediaName = uniqid() . $mediaName;
        $mediaFile = $_FILES['flPostPhoto']['tmp_name'];

        $checkImage = getimagesize($mediaFile);
        $width = $checkImage[0];
        $height = $checkImage[1];

        if ($width < 180 || $height < 180) {
            echo '<script>alert("This image is too small");location = "/'.$urlUsername.'"</script>';
            exit;
        }

        $type = $_FILES["flPostPhoto"]["type"];
        require 'media_post_file_path.php';
        if ($type == "image/jpeg") {
            $src = imagecreatefromjpeg($mediaFile);
        } else if ($type == "image/png") {
            $src = imagecreatefrompng($mediaFile);
        } else if ($type == "image/gif") {
            // must save gif as jpeg
            $src = imagecreatefromjpeg($mediaFile);
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

        // check if the photo was saved
        if (file_exists($postMediaFilePath)) {

            // write photo to media table
            $sql2 = "INSERT INTO Media (Member_ID, MediaName,     MediaType,  wasProfilePhoto, MediaDate) Values
                               ('$ID',     '$mediaName',  '$type',       1,            CURDATE())";
            mysql_query($sql2) or die(mysql_error());
            // update photo pointer in database
            $sql = "UPDATE Profile Set ProfilePhoto = '$mediaName' WHERE Member_ID = $ID";
            mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting Profile Photo"));
            // alert everything is good
            echo "<script>alert('Update Successful');</script>";
        }
        else {
            echo "<script>alert('There was an issue uploading this photo. Please try another photo');</script>";
        }
    }
}
?>

<?php
// handle upload profile video
if (isset($_POST['video']) && ($_POST['video'] == "Upload Video")) {
    if (isset($_FILES['flPostVideo']) && strlen($_FILES['flPostVideo']['name']) > 1) {
        if ($_FILES['flPostVideo']['size'] > 5000000000) {
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
            $ffmpeg = '/usr/local/bin/ffmpeg';
            // poster file name
            $posterName = "poster".uniqid().".jpg";
            //where to save the image
            $poster = "$posterPath$posterName";
            //time to take screenshot at
            //$interval = 3;
            //screenshot size
            //$size = '440x280'; -s $size -f
            //ffmpeg command
            $cmd = "$ffmpeg -i \"$postMediaFilePath\" -r 1 -ss 3 -f image2 $poster 2>&1";
            exec($cmd);
        }
        else {
            echo "<script>alert('Invalid File Type');</script>";
            exit;
        }
        // write photo to media table
        $sql2 = "INSERT INTO Media (Member_ID, MediaName,     MediaType,  wasProfilePhoto, MediaDate,  Poster      ) Values
                               ('$ID',     '$mediaName',  '$type',       1,            CURDATE(),     '$posterName')";
        mysql_query($sql2) or die(mysql_error());
        // update photo pointer in database
        $sql = "UPDATE Profile Set ProfileVideo = '$mediaName', Poster = '$posterName' WHERE Member_ID = '$ID'";
        mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting Profile Video"));
        // alert everything is good
        echo "<script>alert('Update Successful');</script>";
    }
}
?>


<?php
// handle profile update
if (isset($_POST['updateProfile']) && $_POST['updateProfile'] == "Update") {
    $firstName = $_POST['FirstName'];
    $firstName = mysql_real_escape_string($firstName);
    $lastName = $_POST['LastName'];
    $lastName = mysql_real_escape_string($lastName);

    if (!empty($_POST['ddCity']) && isset($_POST['ddCity'])) {
        $city = $_POST['ddCity'];
    }
    else { $city = getMemberCity($ID); }

  /*  $address = $_POST['Address'];
    $showAddress = $_POST['ShowAddress'];
    $state = $_POST['State'];
    $zip = $_POST['Zip'];
    $showZip = $_POST['ShowZip'];
    $phone = $_POST['Phone'];
    $showPhone = $_POST['ShowPhone'];*/
    $email = $_POST['Email'];
    /*$about = $_POST['About'];
    $about = mysql_real_escape_string($about);
    $about = makeLinks($about);
    $about = closetags($about);
    $rss = mysql_real_escape_string($_POST['RSS']);*/
    $dob = $_POST['DOB'];
    $emailStatus = $_POST['EmailStatus'];
    $smsStatus = $_POST['SmsStatus'];
    $password = $_POST['Password'];
    $username = $_POST['Username'];
    $usernameStatus = $_POST['UsernameStatus'];

//only if password has changed do we hash it
    if (check_password($ID, $password)==false) {
        $password = md5($password);
    }

    // check email
    if (check_email($email) == false) {
        echo "<script>alert('Invalid Email');location='/$username'</script>";
        exit();
    }

    // check that updated email does not exist
    if (is_existing_email($email, $ID)) {
        echo "<script>alert('This email already belongs to someone');location='/$username'</script>";
        exit();
    }
    $email = mysql_real_escape_string($email);

    if ($usernameStatus == 0) {
        if ($_SESSION['Username'] != trim($username)) {
            // remove white spaces
            $username = preg_replace('/\s+/', '', $username);
            $usernameUpdate = ", Username = '$username', IsUsernameUpdated = 1 ";
            $_SESSION['Username'] = $username;
            $username = $_SESSION['Username'];
            $_SESSION['UsernameUpdated'] = 1;
        }
    }
    else {
        $usernameUpdate = '';
    }
    // update Member table first
    $sql = "Update Members
          Set
          FirstName = '$firstName',
          LastName = '$lastName',
          DOB = '$dob',
          Email = '$email',
          EmailActive = '$emailStatus ',
          SmsActive = '$smsStatus ',
          Password = '$password'
          $usernameUpdate
          WHERE ID = $ID ";
    $result = mysql_query($sql) or die(mysql_error());
    // update Profile table
    /*$sql = "Update Profile
            Set Address = '$address',
                ShowAddress = $showAddress,
                City = '$city',
                State = '$state',
                Phone = '$phone',
                ShowPhone = $showPhone,
                Zip = '$zip',
                ShowZip = $showZip,
                About = '$about',
                RSS = '$rss'
             WHERE Member_ID = $ID ";
    mysql_query($sql) or die(logError(mysql_error(), $url, "Updating Profile"));*/
    echo "<script>alert('Update Successful');location='/$username'</script>";
}
?>


<?php include('media_sizes.html'); ?>

<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

<script>
    // CHECK DEMOGRAPHIC INFORMATION
    function checkFields() {
        var firstName = document.getElementById('FirstName').value;

        if (firstName == "") {
            alert('You must supply a first name or business name');
            return false;
        }


        // check state
        var ddState = document.getElementById('State');
        var state = ddState.options[ddState.selectedIndex].value;

        if (state == '') {
            alert('State needed');
            return false;
        }

// check city
        if (document.getElementById('ddCity') != null) {
            var ddCity = document.getElementById('ddCity');
            var city = ddCity.options[ddCity.selectedIndex].value;

            if (city == '') {
                alert('City needed');
                return false;
            }
        }

        // check zip
       /* var zip = document.getElementById('Zip').value;
        if (zip == '' || zip == "0") {
            alert('Zip Code needed');
            return false;
        }*/


        // check phone if provided
        /*var phone = document.getElementById('Phone').value;
        // remove special characters
        var rawPhone = phone.replace(/[^\d]/g,'');
        if (rawPhone.length > 0) {
            var format =/^\d{3}\-?\d{3}\-?\d{4}$/;
            if (format.test(rawPhone)) {
                return true
            }
            else {
                alert('Invalid phone format');
                return false;
            }
        }*/

        // check password
        /*var password = document.getElementById('Password').value;
        if (password == '') {
            alert('Password needed');
            return false;
        }*/

        // check password
        /*var about = document.getElementById('About').value;
        if (about == '') {
            alert('Please provide something about yourself in your about section');
            return false;
        }*/

        // check dob
        var dob = document.getElementById('DOB').value;
        var userDate = new Date(dob);
        var today = new Date();


        if (userDate.getFullYear() <= 1900) {
            alert('Your birth date must be greater than 1900');
            return false;
        }

            var age = today.getFullYear() - userDate.getFullYear();

            if(age <= 13) {
                alert("You have to be at least 13 years old!");
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

<script>
    // follow
    $(document).ready(function() {
        $("body").delegate(".btnFollow", "click", function() {
            var parentDiv = $(this).closest("div[id^=followDiv]");
            data={
                memberID: $(this).closest('tr').find('.followedID').val(),
                ID: $(this).closest('tr').find('.followerID').val()
                //add other properties similarly
            }

            $.ajax({
                type: "post",
                url: "follow.php",
                data: data,
                success: function(data)
                {
                    parentDiv.html(data);
                }

            })
        });
    });
</script>

<script>
    // unfollow
    $(document).ready(function() {
        $("body").delegate(".btnUnfollow", "click", function() {
            var parentDiv = $(this).closest("div[id^=followDiv]");
            data={
                memberID: $(this).closest('tr').find('.followedID').val(),
                ID: $(this).closest('tr').find('.followerID').val()
                //add other properties similarly
            }

            $.ajax({
                type: "post",
                url: "unfollow.php",
                data: data,
                success: function(data)
                {
                    parentDiv.html(data);
                }

            })
        });
    });
</script>


<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
preg_match("/[^\/]+$/",$url ,$match);
if ($_SESSION['UsernameUpdated'] == 1) {
    $username = $_SESSION['Username'];
}
else {
    $username = $match[0];
}


$token = $match[1];
$username = $_SESSION['Username'];
$profileID = get_id_from_username($username);
$sql = "SELECT ProfilePhoto FROM Profile WHERE Member_ID = $profileID";
$result = mysql_query($sql) or (logError(mysql_error(), $url, 'Getting Profile Photo'));
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

    <span id = "newMessageCount" class="new-message-count"></span>

    <div class="row row-padding">

        <div class="col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2  roll-call ">

            <?php
                require 'profile_menu.php';
            ?>

            <?php



            // get current member session username
            $sql = "SELECT Username FROM Members WHERE ID = $ID ";
            $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting username from session ID"));
            $rows = mysql_fetch_assoc($result);

            // if profile is not the current member session ID
            // serve the public profile
            if ($rows['Username'] != $username) {

            require 'publicProfile.php';
            }
            else {
                //*************************-----------------------------------------------------------------------
                // render edit profile view
                //************************** -----------------------------------------------------------------------

                $sql = "SELECT DISTINCT
                        Members.ID As MemberID,
                        Members.FirstName As FirstName,
                        Members.LastName As LastName,
                        Members.Email As Email,
                        Members.Password As Password,
                        Members.DOB As DOB,

                        Members.EmailActive As EmailStatus,
                        Members.SmsActive As SmsStatus,
                        Members.IsUsernameUpdated As IsUsernameUpdated,
                        Profile.ProfilePhoto As ProfilePhoto,
                        Profile.ProfileVideo As ProfileVideo,
                        Profile.Poster As Poster,
                        Profile.Address As Address,
                        Profile.ShowAddress As ShowAddress,
                        Profile.City As City,
                        Profile.State As State,
                        Profile.Zip As Zip,
                        Profile.ShowZip As ShowZip,
                        Profile.Phone As Phone,
                        Profile.ShowPhone As ShowPhone,
                        Profile.About As About,
                        Profile.RSS As RSS
                        FROM Members, Profile
                        WHERE Members.ID = $ID
                        AND Profile.Member_ID = $ID
                        Order By MemberID ";
            $result = mysql_query($sql) or die (logError(mysql_error(), $url, "Getting private profile data"));
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
            $address = $rows['Address'];
            $showAddress = $rows['ShowAddress'];
            $city = $rows["City"];
            $state = $rows['State'];
            $zip = $rows['Zip'];
            $showZip = $rows['ShowZip'];
            $phone = $rows['Phone'];
            $showPhone = $rows['ShowPhone'];
            $about = $rows['About'];
            $email = $rows['Email'];
            $password = $rows['Password'];
            $dob = $rows['DOB'];

            $rss = $rows['RSS'];
            $emailStatus = $rows['EmailStatus'];
            $smsStatus = $rows['SmsStatus'];
            $usernameStatus = $rows['IsUsernameUpdated'];

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

                <script>
                    function getCity(sel) {
                        var state = sel.options[sel.selectedIndex].value;

                        $.ajax({
                            type: "POST",
                            url: "/getCity.php",
                            data: "state="+state+"&page=profile",
                            cache: false,
                            beforeSend: function () {

                            },
                            success: function(html) {
                                $("#divCity").html(html);
                            }
                        });

                    }
                </script>

                <!--<input onclick="showTextBox('textDiv')" type="image" value="Share" src="/images/share.png" height="50px" width="50px" style="margin-top:10px" />-->
                <br/>


                <img src = "<?php echo $mediaPath.$profilePhoto ?>" class="profilePhoto" alt="Profile Photo" />
            </div>

            <br/>

            <div class="hidden-lg">

                <form method="post" enctype="multipart/form-data" action="" >
                    <img src="/images/image-icon.png" class="img-icon" alt="Photos/Video"/>
                    <strong>Upload A Profile Photo</strong>
                    <input type="file" width="10px;" name="flPostPhoto" id="flPostPhoto"/>
                    <input type="hidden" name="MAX_FILE_SIZE" value="500000000"
                    <br/>
                    <div id="PhotoProgress" style="display:none;">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" class="progress-bar">
                                <span class="sr-only">Loading</span>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <input type="submit" class="post-button" name="photo" id="photo" value="Upload Photo" onclick="showPhotoUploading()" />
                </form>

            </div>

            <hr class="hr-line"/>

 <!--Profile ---------------------------------------------------------------------------------------->
            <p id="notice"></p>
            <br/>

            <form id="ajax-form" method="post" action = "" onsubmit="return checkFields();">

                <div class="form-group">
                    <label for="FirstName">First Name</label>
                    <br/>
                    <input type ="text" class="form-control" id="FirstName" name="FirstName" value="<?php echo $firstName ?>" onblur="capFname()" />
                </div>

                <div class="form-group">
                    <label for="LastName">Last Name </label><span style="font-style: italic;padding-left:5px;font-size:12px;font-weight:bold;color:red;">(Not required)</span>
                    <input type="text" class="form-control" id="LastName" name="LastName" value="<?php echo $lastName ?>" onblur="capLname()" />
                </div>

               <!-- <div class="form-group">
                    <label for="Address">Address </label><span style="font-style: italic;padding-left:5px;font-size:12px;">
                    <input type="text" class="form-control" id="Address" name="Address" value="<?php /*echo $address */?>" />
                </div>-->

                <?php
             /*   $otherShowAddressValue = "";
                $otherShowAddressText = "";
                if ($showAddress == 1) {
                    $showAddressText = "Yes";
                    $otherShowAddressValue = 0;
                    $otherShowAddressText = "No";
                }
                else {
                    $showAddressText = "No";
                    $otherShowAddressValue = 1;
                    $otherShowAddressText = "Yes";
                }*/
                ?>

               <!-- <div class="form-group">
                    <label for="ShowAddress">Show address in profile</label>
                    <select id="ShowAddress" name="ShowAddress" class="form-control">
                        <option value="<?php /*echo $showAddress */?>"><?php /*echo $showAddressText */?></option>
                        <option value="<?php /*echo $otherShowAddressValue */?>"><?php /*echo $otherShowAddressText */?></option>
                    </select>
                </div>-->

                <!--<div class="form-group">
                    <label for="State">State</label>
                    <select id="State" name="State" class="form-control" >
                        <option  value="<?php /*echo $state */?>"><?php /*echo $state */?></option>
                        <?php /*getState() */?>
                    </select>
                </div>-->

               <!-- <label for="City">City:</label>
                <br/>
                <?php /*echo $city */?>

                <br/>

                <div id="divCity"></div>

                <br/>-->

                <!--<div class="form-group">
                    <label for="Zip">Zip Code</label>
                    <input type="text" class="form-control" id="Zip" name="Zip" value="<?php /*echo $zip */?>" />
                </div>-->

                <?php
               /* $otherShowZipValue = "";
                $otherShowZipText = "";
                if ($showZip == 1) {
                    $showZipText = "Yes";
                    $otherShowZipValue = 0;
                    $otherShowZipText = "No";
                }
                else {
                    $showZipText = "No";
                    $otherShowZipValue = 1;
                    $otherShowZipText = "Yes";
                }*/
                ?>

               <!-- <div class="form-group">
                    <label for="ShowZip">Show zip code in profile</label>
                    <select id="ShowZip" name="ShowZip" class="form-control">
                        <option value="<?php /*echo $showZip */?>"><?php /*echo $showZipText */?></option>
                        <option value="<?php /*echo $otherShowZipValue */?>"><?php /*echo $otherShowZipText */?></option>
                    </select>
                </div>-->

                <!--<div class="form-group">
                    <label for="Phone">Phone</label>
                    <input type="text" class="form-control" id="Phone" name="Phone" value="<?php /*echo $phone */?>" />
                </div>-->

                <?php
                /*$otherShowPhoneValue = "";
                $otherShowPhoneText = "";
                if ($showPhone == 1) {
                    $showPhoneText = "Yes";
                    $otherShowPhoneValue = 0;
                    $otherShowPhoneText = "No";
                }
                else {
                    $showPhoneText = "No";
                    $otherShowPhoneValue = 1;
                    $otherShowPhoneText = "Yes";
                }*/
                ?>

                <!--<div class="form-group">
                    <label for="ShowPhone">Show phone number in profile</label>
                    <select id="ShowPhone" name="ShowPhone" class="form-control">
                        <option value="<?php /*echo $showPhone */?>"><?php /*echo $showPhoneText */?></option>
                        <option value="<?php /*echo $otherShowPhoneValue */?>"><?php /*echo $otherShowPhoneText */?></option>
                    </select>
                </div>-->

<!--
                <div class="form-group">
                    <label for="About">About</label>
                    <textarea class="form-control textArea" id="About" name="About"><?php /*echo $about */?></textarea>
                </div>
-->

                <div class="form-group">
                    <label for="Email">Email</label>
                    <input type="email" class="form-control" id="Email" name="Email" value="<?php echo $email ?>" />
                </div>

                <!--<div class="form-group">
                    <label for="RSS">RSS</label>
                    <br/>
                    <input type ="text" class="form-control" id="RSS" name="RSS" value="<?php /*echo $rss */?>"  />
                </div>-->

                <div class="form-group">
                    <label for="DOB">Date Of Birth</label>
                    <input type="date" class="form-control" id="DOB" name="DOB" value="<?php /*echo $dob */?>" />
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


                <?php
                $otherSmsValue = "";
                $otherSmsText = "";
                if ($smsStatus == 1) {
                    $smsStatusText = "On";
                    $otherSmsValue = 0;
                    $otherSmsText = "Off";
                }
                else {
                    $smsStatusText = "Off";
                    $otherSmsValue = 1;
                    $otherSmsText = "On";
                }
                ?>

                <div class="form-group">
                    <label for="SmsStatus">SMS Notification</label>
                    <select id="SmsStatus" name="SmsStatus" name="SmsStatus" class="form-control">
                        <option value="<?php echo $smsStatus ?>"><?php echo $smsStatusText ?></option>
                        <option value="<?php echo $otherSmsValue ?>"><?php echo $otherSmsText ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="username">Password</label>
                    <input type="password" class="form-control" id="Password" name="Password" value="<?php echo $password ?>"  />
                </div>

                <?php
                if ($usernameStatus == 1) {
                    $readonly = "readonly='readonly'";
                    $style="display:none;";
                }
                else {
                    $readonly = '';
                    $style="display:block;";
                }
                ?>


                <div class="form-group">
                    <label for="password">Username</label>
                    <input type="text" class="form-control" id="Username" name="Username" value="<?php echo $username ?>" <?php echo $readonly ?> />
                    <h6 style="color:red;<?php echo $style ?>">You can change your username one time.</h6>
                    <input type="hidden" id="UsernameStatus" name="UsernameStatus" value="<?php echo $usernameStatus ?>" />
                </div>


                <input type = "submit" value = "Update" name = "updateProfile" id = "updateProfile" class="btn btn-default" />
            </form>

                <br/>
                <?php /*if (strlen($rss) > 0) {
                    echo "<h3>Your RSS Feed</h3>";
                    require 'rss.php';

             } */

            } ?>
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
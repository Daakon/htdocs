<?php

require 'connect.php';
require 'getSession.php';
require 'html_functions.php';
require 'mediaPath.php';
require 'findURL.php';
require 'model_functions.php';
require 'email.php';

get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];

?>


<?php
// handle message
if (isset($_POST['send']) && $_POST['send'] == "Send") {

    $checkID = $_GET['id'];
    $receiverID = $_POST['receiverID'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $message = mysql_real_escape_string($message);

    // check if sender prior message thread exists
    $sql="SELECT * FROM Messages WHERE ThreadOwner_ID = $ID And Receiver_ID = $checkID Or Sender_ID = $checkID And InitialMessage = 1 ";
    $result = mysql_query($sql) or die(mysql_error());
    $numRows = mysql_num_rows($result);
    $initialMessage;

    if ($numRows > 0) {
        $senderInitialMessage = 0;
    }
    else {
        $senderInitialMessage = 1;
    }

    // check if reciever prior message thread exists
    $sql="SELECT * FROM Messages WHERE ThreadOwner_ID = $checkID And Receiver_ID = $ID Or Sender_ID = $ID And InitialMessage = 1 ";
    $result = mysql_query($sql) or die(mysql_error());
    $numRows = mysql_num_rows($result);
    $recieverInitialMessage;

    if ($numRows > 0) {
        $receiverInitialMessage = 0;
    }
    else {
        $receiverInitialMessage = 1;
    }

// if photo is provided
    if (isset($_FILES['flPostMedia']) && strlen($_FILES['flPostMedia']['name']) > 1) {

// check file size
        if ($_FILES['flPostMedia']['size'] > 50000000) {
            echo '<script>alert("File is too large. The maximum file size is 50MB.");location = "home.php?"</script>';
            exit;
        }

// check if file type is a photo
        $videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
            "video/quicktime", "video/webm", "video/x-matroska",
            "video/x-ms-wmw");
// video file types
        $photoFileTypes = array("image/jpg", "image/jpeg", "image/png", "image/tiff",
            "image/gif", "image/raw");

        // add unique id to image name to make it unique and add it to the file server
        $mediaName = $_FILES["flPostMedia"]["name"];
        $mediaName = trim(uniqid() . $mediaName);
        $mediaFile = $_FILES['flPostMedia']['tmp_name'];
        $type = trim($_FILES["flPostMedia"]["type"]);

        require 'media_post_file_path.php';

        if (in_array($type, $videoFileTypes)) {
            // convert to mp4
            $fileName = pathinfo($mediaName, PATHINFO_FILENAME);
            $newFileName = $fileName.".mp4";
            exec("ffmpeg -i $fileName -vcodec libx264 -pix_fmt yuv420p -profile:v baseline -preset slow -crf 22 -movflags +faststart $newFileName");
            $mediaName = $newFileName;

        } else {
            $mediaString = 'photo';
            if ($type == "image/jpg" || $type == "image/jpeg") {
                $src = imagecreatefromjpeg($mediaFile);
            } else if ($type == "image/png") {
                $src = imagecreatefrompng($mediaFile);
            } else if ($type == "image/gif") {
                $src = imagecreatefromgif($mediaFile);
            } else {
                echo "<script>alert('Invalid File Type'); ";
                exit;
            }
        }

        // read exif data
        $exif = exif_read_data($_FILES['flPostMedia']['tmp_name']);

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

// save photo/video
        require 'media_post_file_path.php';
        if (in_array($type, $videoFileTypes)) {
            move_uploaded_file($mediaFile, $postMediaFilePath);
        } else {
            if ($type == "image/jpg" || $type == "image/jpeg") {
                imagejpeg($src, $postMediaFilePath, 100);

            } else if ($type == "image/png") {
                imagepng($src, $postMediaFilePath, 0, NULL);

            } else {
                imagegif($src, $postMediaFilePath, 100);

            }
        }

// if photo didn't get uploaded, notify the user
        if (!file_exists($postMediaFilePath)) {
            echo "<script>alert('File could not be uploaded, try uploading a different file type.');</script>";
        }


// check if file type is a photo
        if (in_array($type, $photoFileTypes)) {

            $img = '<img src = "' . $postMediaFilePath . '" />';
        } // check if file type is a video
        elseif (in_array($type, $videoFileTypes)) {
            $img = '<a href = "' . $videoPath . $mediaName . '"><img src = "' . $images . 'video-bg.jpg" height="100" width = "100" /></a>';
        } else {
            // if invalid file type
            echo '<script>alert("Invalid File Type!");</script>';
            exit;
        }

        $message = $message . '<br/><br/>' . $img . '<br/>';



        // create thread for sender
        $sql = "INSERT INTO Messages (ThreadOwner_ID, Sender_ID,  Receiver_ID,    Subject,    Message,  InitialMessage) Values
                                     ($ID,             $ID,       $receiverID, '$subject',  '$message', $senderInitialMessage) ";
        mysql_query($sql) or die(mysql_error());

        // create thread for receiver
        $sql = "INSERT INTO Messages (ThreadOwner_ID, Sender_ID, Receiver_ID,  Subject,    Message,   InitialMessage,  New) VALUES
                                    ($receiverID,    $ID,        $receiverID, '$subject', '$message', '$receiverInitialMessage',  '1') ";
        mysql_query($sql) or die(mysql_error());

        echo "<script>alert('Message Sent'); </script>";
    }
//----------------------
// if not comment photo
//----------------------

    else {

        // create thread for sender
        $sql = "INSERT INTO Messages (ThreadOwner_ID, Sender_ID,  Receiver_ID,    Subject,    Message, InitialMessage) Values
                                      ($ID,             $ID,       $receiverID, '$subject',  '$message',    '$senderInitialMessage') ";
        mysql_query($sql) or die(mysql_error());

        // create thread for receiver
        $sql = "INSERT INTO Messages (ThreadOwner_ID, Sender_ID, Receiver_ID,  Subject,    Message,   InitialMessage,    New) VALUES
                                    ($receiverID,    $ID,        $receiverID, '$subject', '$message',  '$receiverInitialMessage', '1') ";
        mysql_query($sql) or die(mysql_error());

        echo "<script>alert('Message Sent'); </script>";
    }

    // notify recipient of email
    build_and_send_email($ID, $receiverID,8, "");

}
?>

<?php
// delete messages
if (isset($_POST['delete']) && $_POST['delete'] == "Delete Messages") {
    $receiverID = $_POST['receiverID'];
    $sql = "DELETE FROM Messages WHERE ThreadOwner_ID = $ID AND (Sender_ID = $receiverID Or Receiver_ID = $receiverID) ";
    mysql_query($sql) or die(mysql_error());
    echo "<script>location = 'messages.php'</script>";
}
?>


<?php include('media_sizes.html'); ?>

<div class="container" >
    <div class="row row-padding">

        <?php require 'profile_menu.php'; ?>
        <br/><br/>

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call ">

            <h2>View Messages</h2>
            <hr/>

            <?php $senderID = $_GET['id']; ?>

            <?php
            // get subject
            $sql = "SELECT * FROM Messages
                    WHERE ThreadOwner_ID = $ID
                    AND (Receiver_ID = $senderID)
                    AND (IsDeleted = 0) LIMIT 1 ";
            $result = mysql_query($sql) or die(mysql_error());
            $row = mysql_fetch_assoc($result);
            $subject = $row['Subject'];
            ?>

            <h4 style="color:red;font-weight:bold;"><?php echo $subject ?></h4>
            <br/>

            <?php

            $sql = "SELECT * FROM Messages
                    WHERE ThreadOwner_ID = $ID
                    AND (Sender_ID = $senderID Or Receiver_ID = $senderID)
                    AND (IsDeleted = 0) ";
            $result = mysql_query($sql) or die(mysql_error());


            if (mysql_numrows($result) > 0) {
                while ($rows = mysql_fetch_assoc($result)) {
                    $senderID = $rows['Sender_ID'];

                    $message = $rows['Message'];

                    // get receiver name
                    $sql2 = "SELECT FirstName, LastName, ProfilePhoto
                    FROM Members, Profile
                    WHERE Profile.Member_ID = $senderID
                    AND Members.ID = $senderID ";

                    $result2 = mysql_query($sql2) or die(mysql_error());
                    $rows2 = mysql_fetch_assoc($result2);
                    $pic = $rows2['ProfilePhoto'];
                    $name = $rows2['FirstName'] . ' ' . $rows2['LastName'];

                    echo "<img src = '$mediaPath$pic' class='profilePhoto-Feed' alt='' /> $name";
                    echo "<br/><br/>";
                    echo "$message";
                    echo "<hr/>";
                    echo "<br/>";
                }
            }

            ?>


<?php
// reinitialize sender ID
$senderID = $_GET['id'];
?>

            <form action="" method="post" enctype="multipart/form-data">
                <img src="/images/image-icon.png" height="30px" width="30px" alt="Photos/Video"/>
                <input type="file" width="10px;" name="flPostMedia" id="flPostMedia"/>
                <textarea name="message" id="message" class="form-control"></textarea>
                <input type="hidden" id="subject" name="subject" value="<?php echo $subject ?>" />
                <input type="hidden" id="receiverID" name="receiverID" value="<?php echo $senderID ?>" />
                <input type="submit" class="btn btn-default" id="send" name="send" value="Send" />
            </form>

            <br/><br/>

            <form action="" method="post" onsubmit = "return confirm('Do you really want to delete this message thread')" >
                <input type="hidden" id="receiverID" name="receiverID" value="<?php echo $senderID ?>" />
                <input type="submit" class="btn btn-default" style="background:red;color:white;" id="delete" name="delete" value="Delete Messages" />
            </form>
            <!-------------------------------------------------------------------->
        </div>
    </div>

    <?php
    $sql = "UPDATE Messages SET New = 0 WHERE ThreadOwner_ID = $ID AND (Sender_ID = $senderID Or Receiver_ID = $senderID) ";
    mysql_query($sql) or die(mysql_error());
?>
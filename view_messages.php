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
// check if member exists for messaging
$senderID = $_GET['id'];

$sql = "SELECT ID FROM Members WHERE ID = $senderID ";
$result = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_assoc($result);
if (mysql_num_rows($result) == 0) {
    echo "<script>alert('Member does not exist'); location='/home.php' </script>";
}
?>

<?php
// handle message
if (isset($_POST['send']) && $_POST['send'] == "Send") {

    $receiverID = $_POST['receiverID'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $message = mysql_real_escape_string($message);
    $message = makeLinks($message);

    // check if sender prior message thread exists
    $sql="SELECT * FROM Messages WHERE ThreadOwner_ID = $ID And Receiver_ID = $receiverID Or Sender_ID = $receiverID And InitialMessage = 1 ";
    $result = mysql_query($sql) or die(mysql_error());
    $numRows = mysql_num_rows($result);
    $initialMessage;

    if ($numRows > 0) {
        $initialMessage = 0;
    }
    else {
        $initialMessage = 1;
    }


// if photo is provided
    if (isset($_FILES['flPostMedia']) && strlen($_FILES['flPostMedia']['name']) > 1) {

// check file size
        if ($_FILES['flPostMedia']['size'] > 500000000) {
            echo '<script>alert("File is too large. The maximum file size is 500MB.");</script>';
            exit;
        }

// check if file type is a video
        $videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
            "video/quicktime", "video/webm", "video/x-matroska",
            "video/x-ms-wmw");


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

                echo "<script>alert('Invalid File Type 1');location='/view_messages.php?id=$senderID ";
                exit;
            }

        if (in_array($type, $videoFileTypes)) {
            // convert to mp4 if not already an mp4
            if ($type != "video/mp4") {
                $audioName = $fileName;
                $newFileName = $fileName . ".mp4";
                $oggFileName = $fileName . ".ogv";
                $webmFileName = $fileName . ".webm";


                // convert mp4
                exec("$ffmpeg -i $fileName $newFileName");
                $mediaName = $newFileName;

                // convert ogg
                exec("$ffmpeg -i $fileName  $oggFileName");
                // convert webm
                exec("$ffmpeg -i $fileName  $webmFileName");

            }
        } else {

            echo "<script>alert('Invalid File Type'); location='/home.php'</script>";
            exit;
        }

        require 'media_post_file_path.php';

// save photo/video
        if (in_array($type, $videoFileTypes) || in_array($type, $audioFileTypes)) {
            move_uploaded_file($mediaFile, $postMediaFilePath);


            //copy new mp4 file path to ogg file path
            copy($postMediaFilePath, $postOggFilePathTemp);
            // overwrite mp4 with real ogg file path
            copy($postOggFilePath, $postOggFilePathTemp);
            // copy new mp4 file path to webm file path
            copy($postMediaFilePath, $postWebmFilePathTemp);
            // overwrite mp4 with real webm file path
            copy($postWebmFilePath, $postWebmFilePathTemp);
        }

// if photo didn't get uploaded, notify the user
        if (!file_exists($postMediaFilePath)) {
            echo "<script>alert('File could not be uploaded, try uploading a different file type.');</script>";
        }


// check if file type is a photo
         // check if file type is a video
        if (in_array($type, $videoFileTypes)) {
            // where ffmpeg is located
                        $ffmpeg = '/usr/bin/ffmpeg';
                        // poster file name
                        $posterName = "poster".uniqid().".jpg";
                        //where to save the image
                        $poster = "$posterPath$posterName";
                        //time to take screenshot at
                        //$interval = 5;
                        //screenshot size
                        //$size = '440x280'; -s $size
                        //ffmpeg command
                        $cmd = "$ffmpeg -i \"$postMediaFilePath\" -r 1 -ss 3 -t 1  -f image2 $poster 2>&1";
                        exec($cmd);
                        $poster = imagecreatefromjpeg($poster);

                        /*$white = imagecolorallocate($poster, 255, 255, 255);
                        $text="Rapportbook.com";
                        $font="/stocky.ttf";*/

                        //imagettftext($poster, 20, 0, 20, 20, $white, $font, $text);


                            $size = getimagesize("$posterPath$posterName");
                            $width = $size[0];
                            $height = $size[1];


                        if ($width > $height && $height < 1000) {
                            // video shot in landscape, needs to be flipped
                            $img = imagerotate($poster, 180, 0);
                            imagejpeg($img, $posterPath.$posterName, 50);
                        }
                        // handle images from videos shot with Iphone
                        if ($width > $height && $height > 700 && $type == "video/quicktime" || $type == "video/mp4") {
                            // video shot in landscape, needs to be flipped
                            $img = imagerotate($poster, -90, 0);
                            imagejpeg($img, $posterPath.$posterName, 50);
                        }


                        $img = '<video poster="/poster/'.$posterName.'" preload="none" controls>
                                <source src = "' . $videoPath . $mediaName . '" type="video/mp4" />
                                <source src = "' . $videoPath . $oggFileName . '" type = "video/ogg" />
                                <source src = "' . $videoPath . $webmFileName . '" type = "video/webm" />
                                Your browser does not seem to support the video tag
                                </video>';

                    }

         else {
            // if invalid file type
            echo '<script>alert("Invalid File Type 1!");</script>';
            exit;
        }

        $message = $message . '<br/><br/>' . $img . '<br/>';



        // create thread for sender
        $sql = "INSERT INTO Messages (ThreadOwner_ID, Sender_ID,  Receiver_ID,    Subject,    Message,  InitialMessage,       MessageDate) Values
                                     ($ID,             $ID,       $receiverID, '$subject',  '$message', $initialMessage, CURRENT_TIMESTAMP ) ";
        mysql_query($sql) or die(mysql_error());

        // create thread for receiver
        $sql = "INSERT INTO Messages (ThreadOwner_ID, Sender_ID, Receiver_ID,  Subject,    Message,   InitialMessage,             New, MessageDate   ) VALUES
                                    ($receiverID,    $ID,        $receiverID, '$subject', '$message', '$initialMessage',  '1', CURRENT_TIMESTAMP ) ";
        mysql_query($sql) or die(mysql_error());

        echo "<script>alert('Message Sent'); </script>";

        // sent notification
        if (strlen(check_phone($receiverID)) > 0) {
            text_notification($receiverID, $ID);
        }
    }
//----------------------
// if not comment photo
//----------------------

    else {

        // create thread for sender
        $sql = "INSERT INTO Messages (ThreadOwner_ID, Sender_ID,  Receiver_ID,    Subject,    Message,      InitialMessage,    MessageDate     ) Values
                                      ($ID,             $ID,       $receiverID, '$subject',  '$message',    '$initialMessage', CURRENT_TIMESTAMP ) ";
        mysql_query($sql) or die(mysql_error());

        // create thread for receiver
        $sql = "INSERT INTO Messages (ThreadOwner_ID, Sender_ID, Receiver_ID,  Subject,    Message,   InitialMessage,    New,  MessageDate     ) VALUES
                                    ($receiverID,    $ID,        $receiverID, '$subject', '$message',  '$initialMessage', '1',    CURRENT_TIMESTAMP ) ";
        mysql_query($sql) or die(mysql_error());

        echo "<script>alert('Message Sent'); </script>";

        // sent notification
        if (strlen(check_phone($receiverID)) > 0) {
            text_notification($receiverID, $ID);
        }
    }

    // notify recipient of email
    build_and_send_email($ID, $receiverID,8, "");
    echo "<script>location = 'view_messages.php?id=$receiverID'</script>";
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

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 ">
            <ul class="list-inline">

                <?php require 'profile_menu.php'; ?>
            </ul>
        </div>

        <br/><br/>

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call ">

            <h2>View Messages</h2>
            <hr/>

            <?php $senderID = $_GET['id'];
            $sql = "SELECT Username FROM Members WHERE ID = $senderID";
            $result = mysql_query($sql) or die(mysql_error());
            $row = mysql_fetch_assoc($result);
            $username = $row['Username'];
            $name = get_users_name($senderID);
            $nameArray = explode(' ', $name);
            $firstName = $nameArray[0];
            ?>
            <h5><span class="viewMessage"><a href="/profile_public.php/<?php echo $username ?>">Visit <?php echo $firstName ?>'s Profile</a></span></h5>
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
                    AND (IsDeleted = 0)
                    Order By ID DESC";
            $result = mysql_query($sql) or die(mysql_error());


            if (mysql_num_rows($result) > 0) {
                while ($rows = mysql_fetch_assoc($result)) {
                    $senderID = $rows['Sender_ID'];

                    $message = $rows['Message'];
                    $date = $rows['MessageDate'];

                    // get receiver name
                    $sql2 = "SELECT FirstName, ProfilePhoto,Username
                    FROM Members, Profile
                    WHERE Profile.Member_ID = $senderID
                    AND Members.ID = $senderID ";

                    $result2 = mysql_query($sql2) or die(mysql_error());
                    $rows2 = mysql_fetch_assoc($result2);
                    $pic = $rows2['ProfilePhoto'];
                    $name = $rows2['FirstName'];
                    $username = $rows2['Username'];

                    echo "<a href='/profile_public.php/$username'><img src = '$mediaPath$pic' class='profilePhoto-Feed' alt='' /> $name</a>";

                    echo "<div class='post'>".nl2br($message)."</div>";

                    echo "<div style='opacity:0.5'>".date('l F d Y g:i:s A',strtotime($date))."</div>";
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
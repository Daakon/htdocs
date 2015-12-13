<?php

require 'imports.php';

get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];
$ffmpeg = '/usr/local/bin/ffmpeg';
?>


<?php
// check if member exists for messaging
$urlUsername = get_username_from_url();
if ($urlUsername == get_username($ID)) {
    $username = get_username($ID);
    echo "<script>alert('Error');location='/messages/$urlUsername'</script>";
}
$senderID = get_id_from_username($urlUsername);

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

    $receiverID = get_id_from_username($urlUsername);
    $receiverUsername = $_POST['username'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $message = mysql_real_escape_string($message);
    $message = makeLinks($message);

    // check if sender has prior message thread with receiver
    $sql="SELECT * FROM Messages WHERE (ThreadOwner_ID = $ID) And (Receiver_ID = $receiverID Or Sender_ID = $receiverID) And (InitialMessage = 1) ";
    $result = mysql_query($sql) or die(mysql_error());
    $numRows = mysql_num_rows($result);
    $initialMessage;

    if ($numRows > 0) {
        $initialMessage = 0;
    }
    else {
        $initialMessage = 1;
    }

    // check if receiver has prior message thread with sender
    $sql="SELECT * FROM Messages WHERE (ThreadOwner_ID = $receiverID) And (Receiver_ID = $ID Or Sender_ID = $ID) And (InitialMessage = 1) ";
    $result = mysql_query($sql) or die(mysql_error());
    $numRows = mysql_num_rows($result);
    $rInitialMessage;

    if ($numRows > 0) {
        $rInitialMessage = 0;
    }
    else {
        $rInitialMessage = 1;
    }


// if photo is provided
    if (strlen($_FILES['flPostMedia']['name'] > 0)) {

        foreach ($_FILES['flPostMedia']['tmp_name'] as $k => $v) {
            $mediaName = $_FILES['flPostMedia']['name'][$k];
            $orgName = $_FILES['flPostMedia']['name'][$k];
            $mediaName = preg_replace('/\s+/', '', $mediaName);
            $mediaName = str_replace('&', '', $mediaName);
            $fileName = pathinfo($mediaName, PATHINFO_FILENAME);
            $mediaName = trim(uniqid() . $mediaName);
            $type = $_FILES['flPostMedia']['type'][$k];
            $tempName = $_FILES['flPostMedia']['tmp_name'][$k];
            $size = $_FILES['flPostMedia']['size'][$k];


            // check if word doc
            $ext = end(explode(".", $mediaName));

            $docFileTypes = array("doc", "docx", "ppt", "pptx", "xsl", "xslx", "pdf");

            // if word
            if ($ext == 'doc' || $ext == 'docx') {
                $downloadText = mysql_real_escape_string("Download $orgName Word Document");
            }

            // if excel
            if ($ext == 'xsl' || $ext == 'xslx') {
                $downloadText = mysql_real_escape_string("Download $orgName Spreadsheet");
            }

            // if powerpoint
            if ($ext == 'ppt' || $ext == 'pptx') {
                $downloadText = mysql_real_escape_string("Download $orgName Power Point");
            }

            // if pdf
            if ($ext == 'pdf') {
                $downloadText = mysql_real_escape_string("Download $orgName PDF");
            }

            if (in_array($ext, $docFileTypes)) {
                require 'media_post_file_path.php';
                require 'mediapath.php';
                move_uploaded_file($tempName, $docFilePath);
                $img = '<a href="'.$docPath.$mediaName.'" download >'.$downloadText.'</a>';
                $img = mysql_real_escape_string($img);
                goto BuildMessage;
            }

// check file size
            if ($size > 500000000) {
                echo '<script>alert("File is too large. The maximum file size is 500MB.");</script>';
                exit;
            }

            // photo file types
            $photoFileTypes = array("image/jpg", "image/jpeg", "image/png", "image/tiff",
                "image/gif", "image/raw");

            $audioFileTypes = array("audio/wav", "audio/mp3", "audio/x-m4a");

// check if file type is a video
            $videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
                "video/quicktime", "video/webm", "video/x-matroska",
                "video/x-ms-wmw");


            // add unique id to image name to make it unique and add it to the file server

            $mediaFile = $tempName;
            $mediaFile2 = "";
            copy($tempName, $mediaFile2);
            $mediaFile3 = "";
            copy($tempName, $mediaFile3);

            require 'media_post_file_path.php';

            if (in_array($type, $videoFileTypes)) {
                // convert to mp4 if not already an mp4
                if ($type != "video/mp4") {
                    $audioName = $fileName;
                    $newFileName = $fileName . ".mp4";
                    $oggFileName = $fileName . ".ogv";
                    $webmFileName = $fileName . ".webm";

                    // convert mp4
                    exec("$ffmpeg -i $fileName -vcodec h264 $newFileName");
                    $mediaName = $newFileName;
                    // convert ogg
                    exec("$ffmpeg -i $fileName -vcodec libtheora -acodec libvorbis $oggFileName");
                    // convert webm
                    exec("$ffmpeg -i $fileName -vcodec libvpx -acodec libvorbis -f webm $webmFileName");
                }
            } else {
                if ($type == "image/jpg" || $type == "image/jpeg") {
                    $src = imagecreatefromjpeg($mediaFile);
                } else if ($type == "image/png") {
                    $src = imagecreatefrompng($mediaFile);
                } else if ($type == "image/gif") {
                    $src = imagecreatefromgif($mediaFile);
                } else {
                    /* echo "<script>alert('Invalid File Type');</script>";
                     header('Location:home.php');
                     exit;*/
                }
            }

            require 'media_post_file_path.php';

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
                if (in_array($type, $photoFileTypes)) {
                    // read exif data
                    $exif = @exif_read_data($mediaFile);
                    if (!empty($exif['Orientation'])) {
                        $ort = $exif['Orientation'];
                        switch ($ort) {
                            case 8:
                                $src = imagerotate($src, 90, 0);
                                break;
                            case 3:
                                $src = imagerotate($src, 180, 0);
                                break;
                            case 6:
                                $src = imagerotate($src, -90, 0);
                                break;
                        }
                    }
                }
                // handle transparency
                imagesavealpha($src, true);
                if ($type == "image/jpg" || $type == "image/jpeg") {
                    imagejpeg($src, $postMediaFilePath, 50);
                } else if ($type == "image/png") {
                    imagepng($src, $postMediaFilePath, 0, NULL);
                } else if ($type == "image/gif") {
                    imagegif($src, $postMediaFilePath, 50);
                } else {
                    /*echo "<script>alert('Invalid File Type');</script>";
                    header('Location:home.php');
                    exit;*/
                }
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


// check if file type is a photo
            if (in_array($type, $photoFileTypes)) {
                $img = '<img src = "' . $mediaPath . $mediaName . '" />';
            }
            // check if file type is a video
            if (in_array($type, $videoFileTypes)) {

                // poster file name
                $posterName = "poster" . uniqid() . ".jpg";
                //where to save the image
                $poster = "$posterPath$posterName";
                //time to take screenshot at
                //$interval = 5;
                //screenshot size
                //$size = '440x280'; -s $size
                //ffmpeg command
                $cmd = "$ffmpeg -i \"$postMediaFilePath\" -r 1 -ss 3 -t 1  -f image2 $poster 2>&1";
                exec($cmd);

                $img = '<video poster="/poster/' . $posterName . '" preload="none" controls>
                                <source src = "' . $videoPath . $mediaName . '" type="video/mp4" />
                                <source src = "' . $videoPath . $oggFileName . '" type = "video/ogg" />
                                <source src = "' . $videoPath . $webmFileName . '" type = "video/webm" />
                                Your browser does not seem to support the video tag
                                </video>';
            }
            BuildMessage:

            if (strlen($img) > 0) {
                $br = "<br/><br/>";
            }
            $newImage .= $br . $img;

        } // end loop

        if (strlen($img) == 0) {
            $br = "<br/>";
        }
        $message = $message . $newImage .'<br/>' ;



        // create thread for sender
        $sql = "INSERT INTO Messages (ThreadOwner_ID, Sender_ID,  Receiver_ID,    Subject,    Message,  InitialMessage,       MessageDate) Values
                                     ($ID,             $ID,       $receiverID, '$subject',  '$message', $initialMessage, CURRENT_TIMESTAMP ) ";
        mysql_query($sql) or die(mysql_error());

        // create thread for receiver
        $sql = "INSERT INTO Messages (ThreadOwner_ID, Sender_ID, Receiver_ID,  Subject,    Message,   InitialMessage,             New, MessageDate   ) VALUES
                                    ($receiverID,    $ID,        $receiverID, '$subject', '$message', '$rInitialMessage',  '1', CURRENT_TIMESTAMP ) ";
        mysql_query($sql) or die(mysql_error());

        echo "<script>alert('Message Sent'); </script>";

        // sent notification
        if (strlen(check_phone($receiverID)) > 0) {
            text_notification($receiverID, $ID);
        }
    }
//----------------------
// if no media
//----------------------

    else {
        $receiverID = get_id_from_username($urlUsername);
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

    // update the initial message row so we know which messages to render first in messages.php
    $sql = "UPDATE Messages SET New = 1
            WHERE ThreadOwner_ID = $receiverID And (InitialMessage = 1) And (Sender_ID = $ID) Or (Receiver_ID = $ID)";
    mysql_query($sql);

    // notify recipient of email
    build_and_send_email($ID, $receiverID,8, "","");
    echo "<script>location = '/view_messages/$receiverUsername'</script>";
}
?>

<?php
// delete messages
if (isset($_POST['delete']) && $_POST['delete'] == "Delete Messages") {
    $receiverID = $_POST['receiverID'];
    $sql = "DELETE FROM Messages WHERE ThreadOwner_ID = $ID AND (Sender_ID = $receiverID Or Receiver_ID = $receiverID) ";
    mysql_query($sql) or die(mysql_error());
    $username = get_username($ID);
    echo "<script>location = '/messages/$username'</script>";
}
?>


<?php include('media_sizes.html'); ?>


    <body onload="window.scrollTo(0,document.body.scrollHeight);">
<div class="container">
    <div class="row row-padding">

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call ">

            <h2>View Messages</h2>
            <hr/>

            <?php $senderID = get_id_from_username($urlUsername);
            $sql = "SELECT FirstName, Username FROM Members WHERE ID = $senderID";
            $result = mysql_query($sql) or die(mysql_error());
            $row = mysql_fetch_assoc($result);
            $username = $row['Username'];
            $firstName = $row['FirstName'];
            ?>
            <h5><span class="viewMessage"><a href="/<?php echo $username ?>">Visit <?php echo $firstName ?>'s Profile</a></span></h5>
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
                    Order By ID ASC";
            $result = mysql_query($sql) or die(mysql_error());


            if (mysql_num_rows($result) > 0) {
                while ($rows = mysql_fetch_assoc($result)) {
                    $senderID = $rows['Sender_ID'];

                    $message = $rows['Message'];
                    $date = $rows['MessageDate'];

                    // get receiver name
                    $sql2 = "SELECT FirstName,LastName, ProfilePhoto,Username
                    FROM Members, Profile
                    WHERE Profile.Member_ID = $senderID
                    AND Members.ID = $senderID ";

                    $result2 = mysql_query($sql2) or die(mysql_error());
                    $rows2 = mysql_fetch_assoc($result2);
                    $pic = $rows2['ProfilePhoto'];
                    $name = $rows2['FirstName'] .' '.$rows2['LastName'];
                    $username = $rows2['Username'];

                    echo "<a href='/$username'><img src = '$mediaPath$pic' class='profilePhoto-Feed' alt='' /> $name</a>";

                    echo "<div class='post'>".nl2br($message)."</div>";

                    echo "<div style='opacity:0.5'>".date('l F d Y g:i:s A',strtotime($date))."</div>";
                    echo "<hr/>";
                }
            }

            ?>



            <ul class="list-inline">

                <?php require 'profile_menu.php'; ?>
            </ul>

            <style>
                .list-inline {
                    margin-top:-20px;
                }
            </style>

            <?php
            // reinitialize sender ID
            $senderID = get_id_from_username($urlUsername);
            ?>


            <form action="" method="post" enctype="multipart/form-data">
                Add Any Combination of:<br/>
                <img src="/images/image-icon.png" height="30px" width="30px" alt="Photos/Video/Documents"/>
                <strong>Photos/Videos/Documents</strong>


                <input type="file" width="10px;" name="flPostMedia[]" id="flPostMedia" multiple />

                <textarea name="message" id="message" class="form-control"></textarea>
                <input type="hidden" id="subject" name="subject" value="<?php echo $subject ?>" />
                <input type="hidden" id="receiverID" name="receiverID" value="<?php echo $senderID ?>" />
                <input type="hidden" id="username" name="username" value="<?php echo $urlUsername ?>"/>
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
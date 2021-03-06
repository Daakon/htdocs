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
$isGroupChat = false;
$groupCheck = '';
$groupChatExist = false;
/*if (!empty($_GET['groupchat'])) {
    goto GroupChat;
}*/
?>

<?php
// check if member is messaging themselves
$urlUsername = get_username_from_url();
if (strstr($urlUsername, "?")) {
// do nothing
}
else if ($urlUsername == get_username($ID)) {
    $username = get_username($ID);
    echo "<script>alert('Error');location='/messages/$urlUsername'</script>";
}
$recipientID = get_id_from_username($urlUsername);
if (strstr($urlUsername, "?")) {
    // do nothing
}
else if (isset($recipientID) && !empty($recipientID)) {
    $sql = "SELECT ID FROM Members WHERE ID = $recipientID ";
    $result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_assoc($result);
}
else {
    $groupCheck = " AND (GroupID = '$urlUsername') ";
    $sql = "SELECT GroupID FROM Messages WHERE GroupID = '$urlUsername' ";
    $result = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_assoc($result);
    if (mysql_num_rows($result) > 0) {
        $groupChatExist = true;
    }
    else {
        echo "<script>alert('Member does not exist'); location='/home.php' </script>";
    }
}
?>

<?php
if (isset($_POST['videoSend']) && $_POST['videoSend'] = "Start Video Chat") {
    // build out appear.in link
    $appearID = 'Playdoe'.uniqid();
    $appearLink = "<iframe src=\"https://appear.in/$appearID\" frameborder=\"0\" height=\"300\"></iframe>";
    $message = $appearLink;
    $hasVideo = true;
    echo "<script>alert('As the sender, you must enter the video room first.');</script>";
    goto StartSend;
}
?>

<?php
// handle message
if (isset($_POST['send']) && $_POST['send'] == "Send") {
    StartSend:
    $uniqueID = uniqid();
    $message = $_POST['message'];
    $isGroupChat = $_POST['isGroupChat'];
    $groupName = $_POST['groupName'];
    $groupID = $_POST['groupID'];
    // get total receiver count

    if (strlen($message) == 0) { echo "<script>alert('Your message appears to be empty'); location = '/view_messages/$urlUsername</script>"; }

    $receiverCount = count($_POST['receiverID']);
    foreach ($_POST['receiverID'] as $key => $receiverID) {
        $receiverUsername = $_POST['username'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];
        $isGroupChat = $_POST['isGroupChat'];
        $groupID = $_POST['groupID'];
        $groupName = $_POST['groupName'];
        $groupChatExist = $_POST['groupChatExist'];
        // creating a chat with one receiver means its a one on one message
        if ($isGroupChat && $groupChatExist == false) {
            if ($receiverCount == 1) {
                $isGroupChat = false;
                $groupChatExist = false;
                $groupName = '';
            }
        }
        // check for new group message
        if (!$isGroupChat) {
            $groupID = '';
            $groupName = '';
        }
        if (!$groupChatExist && strlen($groupID) > 0) {
            $groupID = '';
        }
        if ($hasVideo == true) {
            $message = $message . '<br/>' . $appearLink;
        } else {
            $message = mysql_real_escape_string($message);
            $message = makeLinks($message);
        }
        $hasVideo = false;
        // ----------------------------------------------------------
        if ($isGroupChat && !$groupChatExist) {
            $rInitialMessage = 1;
            $initialMessage = 1;
            $firstMessage = 1;
            $rFirstMessage = 1;
        }
    } // end initial message checks
    // group messages dont condition to else statement without media
// if photo is provided
    if (strlen($_FILES['flPostMedia']['name'] > 0)) {
        foreach ($_FILES['flPostMedia']['tmp_name'] as $k => $v) {
            $mediaName = $_FILES['flPostMedia']['name'][$k];
            $orgName = $_FILES['flPostMedia']['name'][$k];
            $orgName = pathinfo($orgName, PATHINFO_FILENAME);
            $orgName = '<b>' . $orgName . '</b>';

            $mediaName = preg_replace('/\s+/', '', $mediaName);
            // remove ALL SPECIAL CHARACTERS, Images paths are extremely sensitive
            $mediaName = str_replace('/[^A-Za-z0-9\-]/', '', $mediaName);
            // remove ampersand
            $mediaName = str_replace('&', '', $mediaName);
            
            $fileName = pathinfo($mediaName, PATHINFO_FILENAME);
            $mediaName = trim(uniqid() . $mediaName);
            $type = $_FILES['flPostMedia']['type'][$k];
            $tempName = $_FILES['flPostMedia']['tmp_name'][$k];
            $size = $_FILES['flPostMedia']['size'][$k];
            // check if word doc
            $ext = end(explode(".", $mediaName));
            $docFileTypes = array("doc", "docx", "ppt", "pptx", "xls", "xlsx", "pdf");
            // if word
            if ($ext == 'doc' || $ext == 'docx') {
                $downloadText = mysql_real_escape_string("Download $orgName Word Document");
            }
            // if excel
            if ($ext == 'xls' || $ext == 'xlsx') {
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
                move_uploaded_file($tempName, $docFilePath);
                $img = '<a href="' . $docPath . $mediaName . '" download >' . $downloadText . '</a>';
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
                    // must save gif as jpeg
                    $src = imagecreatefromjpeg($mediaFile);
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
        $message = $message . $newImage . '<br/>';
        $message = closetags($message);
        // if not a group chat ----------------------------------------
        if ($isGroupChat == false) {
            // check if sender has prior message thread with receiver
            $sql = "SELECT * FROM Messages WHERE (ThreadOwner_ID = $ID) And (Receiver_ID = $receiverID Or Sender_ID = $receiverID) And (InitialMessage = 1) And (GroupID = '') ";
            $result = mysql_query($sql) or die();
            $numRows = mysql_num_rows($result);
            $initialMessage;
            /*  if the sender deleted their messages with the receiver
                the thread would no longer exist at all
                so this would be a first message regardless for the sender
            */
            if ($numRows > 0) {
                $initialMessage = 0;
                $firstMessage = 0;
            } else {
                $initialMessage = 1;
                $firstMessage = 1;
            }
            // check if sender has prior message thread with receiver
            $sql = "SELECT * FROM Messages WHERE (ThreadOwner_ID = $receiverID) And (Receiver_ID = $ID Or Sender_ID = $ID) And (InitialMessage = 1) And (GroupID = '') ";
            $result = mysql_query($sql) or die();
            $numRows = mysql_num_rows($result);
            $initialMessage;
            /*  if the sender deleted their messages with the receiver
                the thread would no longer exist at all
                so this would be a first message regardless for the sender
            */
            if ($numRows > 0) {
                $rInitialMessage = 0;
                $rFirstMessage = 0;
            } else {
                $rInitialMessage = 1;
                $rFirstMessage = 1;
            }
            // create thread for sender
            $sql = "INSERT INTO Messages (ThreadOwner_ID, Sender_ID,  Receiver_ID,    Subject,    Message,  InitialMessage, FirstMessage ,      MessageDate) Values
                                     ($ID,             $ID,       $receiverID, '$subject',  '$message', $initialMessage, $firstMessage, CURRENT_TIMESTAMP ) ";
            mysql_query($sql) or die(mysql_error());
            // create thread for receiver
            $sql = "INSERT INTO Messages  (ThreadOwner_ID, Sender_ID,  Receiver_ID,  Subject,    Message,         InitialMessage,      New,        FirstMessage,       MessageDate   ) VALUES
                                                 ($receiverID,    $ID,        $receiverID, '$subject', '$message',     '$rInitialMessage',    '1',        $rFirstMessage,     CURRENT_TIMESTAMP ) ";
            mysql_query($sql) or die(mysql_error());
            if ($receiverID != $ID) {
                build_and_send_email($ID, $receiverID, 8, "", "");
                // send notification
                if (strlen(check_phone($receiverID)) > 0) {
                    text_notification($receiverID, $ID, $groupID);
                }
            }
        }
        // if an existing group chat ---------------------------------------
        if ($groupChatExist) {
            $initialMessage = 0;
            $firstMessage = 0;
            $rInitialMessage = 0;
            $rFirstMessage = 0;
            // loop for receivers in group message
            $sqlRecipients = "SELECT ThreadOwner_ID FROM Messages Where GroupID = '$groupID' ";
            $resultRecipients = mysql_query($sqlRecipients);
            $recipient_ids = array();
//Iterate over the results
            while ($rows = mysql_fetch_assoc($resultRecipients)) {
                array_push($recipient_ids, $rows['ThreadOwner_ID']);
            }
            $recipient_ids = array_unique($recipient_ids);
            foreach ($recipient_ids as $item) {
                if ($item == $ID) {
                    // create thread for sender
                    $sql = "INSERT INTO Messages (ThreadOwner_ID, Sender_ID,    Receiver_ID,   Subject,    Message,   InitialMessage,    New,  FirstMessage,   MessageDate,         GroupID ,     GroupName  ) VALUES
                                                      ($ID,            $ID,          $ID,        '$subject', '$message', '$initialMessage',  '1',  $firstMessage, CURRENT_TIMESTAMP,  '$groupID',   '$groupName' ) ";
                    mysql_query($sql) or die(mysql_error());
                }
                if ($item != $ID && checkBlock($ID, $item) == false) {
                    // create thread for receiver
                    $sql = "INSERT INTO Messages (ThreadOwner_ID, Sender_ID,    Receiver_ID,   Subject,    Message,   InitialMessage,    New,  FirstMessage,   MessageDate,         GroupID , GroupName  ) VALUES
                                                      ($item,            $ID,          $item,    '$subject', '$message', '$rInitialMessage',  '1',  $rFirstMessage, CURRENT_TIMESTAMP,  '$groupID',   '$groupName' ) ";
                    mysql_query($sql) or die(mysql_error());
                    // bring deleted members back into chat
                    $sql = "Update Messages Set IsDeleted = 0 WHERE ThreadOwner_ID = 1 And GroupID = '$groupID' And InitialMessage = 1 ";
                    mysql_query($sql) or die(mysql_error());
                }
            }
            foreach ($recipient_ids as $item) {
                if ($item != $ID) {
                    build_and_send_email($ID, $item, 8, "", "", $groupID);
                    // send notification
                    if (strlen(check_phone($item)) > 0) {
                        if (text_notification($item, $ID, $groupID)) {
                            $receiverName = get_users_name($item);
                            $text = $receiverName . " was sent an SMS";
                            //echo "<script>alert('$text')</script>";
                        }
                    }
                }
            }
        }
        // if a new group chat-----------------------------------------------------
        else if ($isGroupChat && !$groupChatExist) {
            if ($isGroupChat) {
                $groupID = $uniqueID;
                $senderFirstName = get_user_firstName($ID);
                $groupName .= ' '.$senderFirstName;
            }
            // loop for receivers in NEW group message
            array_push($_POST['receiverID'], $ID);
            foreach ($_POST['receiverID'] as $key => $receiverID) {
                if (checkBlock($ID, $receiverID) == false) {
                    // loop through everyone
                    $sql = "INSERT INTO Messages (ThreadOwner_ID, Sender_ID,   Receiver_ID,     Subject,    Message,   InitialMessage,     New,  FirstMessage,   MessageDate,        GroupID ,      GroupName  ) VALUES
                                                 ('$receiverID',    $ID,      $receiverID,     '$subject', '$message', '$rInitialMessage',  '', $rFirstMessage, CURRENT_TIMESTAMP,  '$groupID',   '$groupName' ) ";
                    mysql_query($sql) or die(mysql_error());
                    if ($receiverID != $ID) {
                        build_and_send_email($ID, $receiverID, 8, "", "", $groupID);
                    }
                }
            }
            foreach ($_POST['receiverID'] as $key => $receiverID) {
                // send notification
                if ($receiverID != $ID) {
                    if (strlen(check_phone($receiverID)) > 0) {
                        if (text_notification($receiverID, $ID, $groupID)) {
                            $receiverName = get_users_name($receiverID);
                            $text = $receiverName . " was sent an SMS";
                            //echo "<script>alert('$text');</script>";
                        }
                    }
                }
            }
        }
        //echo "<script>alert('Message Sent'); </script>";
    }


// notify everyone
    foreach ($_POST['receiverID'] as $key => $receiverID) {
        if ($groupChatExist) {
            // loop for receivers in group message
            $sqlRecipients = "SELECT ThreadOwner_ID FROM Messages Where GroupID = '$groupID' ";
            $resultRecipients = mysql_query($sqlRecipients);
            $recipient_ids = array();
//Iterate over the results
            while ($rows = mysql_fetch_assoc($resultRecipients)) {
                array_push($recipient_ids, $rows['ThreadOwner_ID']);
            }
            $recipient_ids = array_unique($recipient_ids);
            foreach ($recipient_ids as $item) {
                if ($item != $ID) {
                    // update New so we know what to render first in messages.php
                    $sql2 = "UPDATE Messages SET New = 1
            WHERE (ThreadOwner_ID = $item) And (InitialMessage = 1) And (GroupID = '$groupID')";
                    mysql_query($sql2) or die(logError(mysql_error(), $url, "Updating initial message where receiver_ID = receiver_ID"));
                }
            }
        }
        if ($isGroupChat && !$groupChatExist) {
            foreach ($_POST['receiverID'] as $key => $receiverID) {
                // update New so we know what to render first in messages.php
                $sql2 = "UPDATE Messages SET New = 1
            WHERE (ThreadOwner_ID = $receiverID) And (InitialMessage = 1) And (GroupID = '$groupID')";
                mysql_query($sql2) or die(logError(mysql_error(), $url, "Updating initial message where receiver_ID = receiver_ID"));
            }
        }
        if ($isGroupChat == false) {
            // update New so we know what to render first in messages.php
            $sql2 = "UPDATE Messages SET New = 1
            WHERE (ThreadOwner_ID = $receiverID) And (Receiver_ID = $receiverID) And (Sender_ID = $ID) And (InitialMessage = 1) And (GroupID = '') ";
            mysql_query($sql2) or die(logError(mysql_error(), $url, "Updating initial message where receiver_ID = receiver_ID"));
            // update New so we know what to render first in messages.php
            $sql2 = "UPDATE Messages SET New = 1
            WHERE (ThreadOwner_ID = $receiverID) And (Receiver_ID = $ID) And (Sender_ID = $receiverID) And (InitialMessage = 1) And (GroupID = '') ";
            mysql_query($sql2) or die(logError(mysql_error(), $url, "Updating initial message where receiver ID = session ID "));
        }
    }
    if ($isGroupChat) {
        $receiverUsername = $groupID;
    }
    else {
        $receiverUsername = get_username($receiverID);
    }
    echo "<script>location = '/view_messages/$receiverUsername'</script>";
}
?>

<?php
// delete messages
if (isset($_POST['delete']) && $_POST['delete'] == "Delete Messages") {
    $receiverID = $_POST['receiverID'];
    $isGroupChat = $_POST['isGroupChat'];
    $groupID = $_POST['groupID'];
    $deleteGroupChat = '';
    if ($isGroupChat) {
        $sql = "UPDATE Messages SET IsDeleted = 1 WHERE (ThreadOwner_ID = $ID) And (InitialMessage = 1) AND (GroupID = '$groupID') ";
    }
    else {
        $sql = "DELETE FROM Messages WHERE ThreadOwner_ID = $ID AND (Sender_ID = $receiverID Or Receiver_ID = $receiverID)";
    }
    mysql_query($sql) or die(mysql_error());
    $username = get_username($ID);
    echo "<script>location = '/messages/$username'</script>";
}
?>

<script>
    // show uploading
    function showUploading() {
        document.getElementById("progress").style.display = "block";
        return true;
    }
</script>

<script>
    function auto_grow(element) {
        element.style.height = "5px";
        element.style.height = (element.scrollHeight)+"px";
    }
</script>

<style>
    .list-inline {
        margin-bottom:-30px;
    }
</style>

<body onload='location.href="#pageStart"'>

<div class="container containerFlush" style="position: relative;">
    <div class="row row-padding">

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call ">

            <h4>View Messages</h4>

            <ul class="list-inline">
                <?php require 'profile_menu.php'; ?>
            </ul>



            <script type="text/javascript" src="jquery-1.8.0.min.js"></script>
            <script type="text/javascript">
                $(function(){
                    $(".search").keyup(function()
                    {
                        // clear empty result
                        if (!this.value.trim()) {
                            $('#result').html('');
                            return;
                        }
                        var searchid = $(this).val();
                        var dataString = 'search='+ searchid;
                        if(searchid!='')
                        {
                            $.ajax({
                                type: "POST",
                                url: "getRecipients.php",
                                data: dataString,
                                cache: false,
                                success: function(html)
                                {
                                    $("#result").html(html).show();
                                }
                            });
                        }return false;
                    });
                    jQuery("#result").live("click",function(e){
                        var $clicked = $(e.target);
                        var $name = $clicked.find('.name').html();
                        var decoded = $("<div/>").html($name).text();
                        $('#searchID').val(decoded);
                    });
                    jQuery(document).live("click", function(e) {
                        var $clicked = $(e.target);
                        if (! $clicked.hasClass("search")){
                            jQuery("#result").fadeOut();
                        }
                    });
                    $('#searchID').click(function(){
                        jQuery("#result").fadeIn();
                    });
                });
            </script>



            <?php
            $recipientID = get_id_from_username($urlUsername);
            if (strstr($urlUsername, "?")) {
            }
            else if (isset($recipientID) && !empty($recipientID)) {
                $sql = "SELECT FirstName, Username FROM Members WHERE ID = $recipientID";
                $result = mysql_query($sql) or die(mysql_error());
                $row = mysql_fetch_assoc($result);
                $username = $row['Username'];
                $firstName = $row['FirstName'];
            }
            ?>

            <?php if (!empty($_GET['groupchat']) || (!isset($recipientID) && empty($recipientID))) {
                // define if this is a group chat
                $isGroupChat = true;
            }
            ?>

            <?php if ($isGroupChat && $groupChatExist == false) { ?>
                <h5>Chat
                    &nbsp;&nbsp;<input type="text" class="search form-control" id="searchID" value="<?php $final_name ?>"
                                       placeholder="Search for people"/>

                    <div id="result"></div>
                    <div id="previewNames"></div>
                </h5>
                <hr class="hr-line" />

            <?php }  ?>

            <?php
            if (strstr($urlUsername, "?")) {
                // do nothing
            }
            else if (isset($recipientID) && !empty($recipientID)) {
                // get subject
                $sql = "SELECT * FROM Messages
                    WHERE ThreadOwner_ID = $ID
                    AND (Receiver_ID = $recipientID)
                    $groupCheck
                    AND (IsDeleted = 0) LIMIT 1 ";
                $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting message subject"));
                $row = mysql_fetch_assoc($result);
                $subject = $row['Subject'];
            }
            ?>

            <?php
            if ($groupChatExist) {
                echo "In this chat:<span style='font-weight:bold;font-size:16px;'> $groupName </span>";
                $sqlUpdate = "UPDATE Messages SET New = 0, FirstMessage = 0 WHERE ThreadOwner_ID = $ID And GroupID = '$urlUsername' ";
                mysql_query($sqlUpdate) or die();
            }
            else if (isset($urlUsername) && !empty($urlUsername)) {
                echo "You are chatting with<span style='font-weight:bold;font-size:16px;'> ". get_username_from_url() . "</span>";
            }
            ?>

            <hr class="hr-line" />

            <!--Message box -->
            <form style="width:100%" id="messageForm" action="" method="post" enctype="multipart/form-data" onsubmit="return showUploading()">

                <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
                <div id="content">
                                <textarea name="message" id="message"  style="float:left;font-size:17px;border:none;clear:both;"
                                          onkeyup="auto_grow(this)"
                                          placeholder="Type your message" spellcheck="true"></textarea>
                </div>

                <div style="clear:both" id="image-holder"> </div>


                <input type="hidden" id="subject" name="subject" value="<?php echo $subject ?>" />
                <?php if (isset($recipientID) && !empty($recipientID)) { ?>
                    <input type="hidden" id="receiverID" name="receiverID[]" value="<?php echo $recipientID ?>" />
                <?php } ?>
                <input type="hidden" id="isGroupChat" name="isGroupChat" value="<?php echo $isGroupChat ?>" />
                <input type="hidden" id="groupChatExist" name="groupChatExist" value="<?php echo $groupChatExist ?>" />
                <input type="hidden" id="groupID" name="groupID" value="<?php echo $urlUsername ?>" />
                <input type="hidden" id="groupName" name="groupName" value="<?php echo $groupName ?>" />


                <div id="progress" style="display:none;">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" class="progress-bar">
                            <b>File uploading...please wait</b>
                        </div>
                    </div>
                </div>


                <label style="float:left;" for="flPostMedia">
                    <img src="/images/combo.png" style="height:25px;width:50px;float:left;margin-right:10px;" />
                </label>
                <input type="file" name="flPostMedia[]" id="flPostMedia" class="flPostMedia" style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' multiple />

                <input style="float:left;" type="submit" class="btn btn-primary" id="send" name="send" value="Send" <?php echo $readonly ?> />
                <input type="image" class="" id="videoSend" name="videoSend" src="/images/video-chat.png" style="margin-top:-10px;height:50px;width:50px;float:left;padding-bottom:10px;padding-left:5px;" value = "Start Video Chat" />
            </form>

            <br/><br/>

            <div id="progress" style="display:none;">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" class="progress-bar">
                        <b>File uploading...please wait</b>
                    </div>
                </div>
            </div>


            <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
            <script>
                $(function () {
                    $("#flPostMedia").change(function () {
                        if (typeof (FileReader) != "undefined") {
                            var dvPreview = $("#image-holder");
                            dvPreview.html("");
                            var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png|.bmp|.mov|.mpeg|.mpg|.ogg|.mp4|.webm|.x-matroska|.x-ms-wmw)$/;
                            $($(this)[0].files).each(function () {
                                var file = $(this);
                                if (regex.test(file[0].name.toLowerCase())) {
                                    var reader = new FileReader();
                                    reader.onload = function (e) {
                                        var img = $("<img />");
                                        img.attr("style", "height:100px;width: 100px");
                                        img.attr("src", e.target.result);
                                        dvPreview.append(img);

                                        var video = $("<video />");
                                        video.attr("style", "height:100px;width: 100px");
                                        video.attr("src", e.target.result);
                                        dvPreview.append(video);
                                    }
                                    reader.readAsDataURL(file[0]);
                                } else {
                                    alert(file[0].name + " document uploaded.");
                                    dvPreview.html("<img src='/images/document.png' height='100' width='100' />");
                                    return false;
                                }
                            });
                        } else {
                            alert("This browser does not support HTML5 FileReader.");
                        }
                    });
                });

            </script>


            <br/>

            <?php if ($rowCount == true) { ?>
                <form action="" method="post" onsubmit = "return confirm('Do you really want to delete this message thread')" >
                    <input type="hidden" id="receiverID" name="receiverID" value="<?php echo $recipientID ?>" />
                    <input type="hidden" id="isGroupChat" name="isGroupChat" value="<?php echo $isGroupChat ?>" />
                    <input type="hidden" id="groupID" name="groupID" value="<?php echo $urlUsername ?>" />
                    <input type="submit" class="btn btn-default" style="background:red;color:white;" id="delete" name="delete" value="Delete Messages" />

                </form>
            <?php } ?>


            <hr class="hr-line" />


            <?php
            if (strstr($urlUsername, "?")) {
            } else {

                // get subject
                if ($groupChatExist) {
                    $subjectSql = "SELECT Subject FROM (SELECT * FROM Messages
                    WHERE ThreadOwner_ID = $ID
                    AND (GroupID = '$urlUsername')
                    Order By ID DESC LIMIT 100) as ROWS Order By ID DESC ";
                    $subjectResult = mysql_query($sql);
                    $subjectCount = mysql_num_rows($subjectResult);
                } else {
                    $subjectSql = "SELECT Subject FROM (SELECT * FROM Messages
                    WHERE ThreadOwner_ID = $ID
                    AND (Sender_ID = $recipientID Or Receiver_ID = $recipientID)
                    AND (IsDeleted = 0) And (GroupID = '')
                    Order By ID DESC LIMIT 100) as ROWS Order By ID DESC ";
                    $subjectResult = mysql_query($subjectSql);
                }

                // check if group message
                if ($groupChatExist) {
                    $sql = "SELECT * FROM (SELECT * FROM Messages
                    WHERE ThreadOwner_ID = $ID
                    AND (GroupID = '$urlUsername')
                    Order By ID DESC LIMIT 100) as ROWS Order By ID DESC ";
                    $result = mysql_query($sql);
                    $count = mysql_num_rows($result);
                } else {
                    $sql = "SELECT * FROM (SELECT * FROM Messages
                    WHERE ThreadOwner_ID = $ID
                    AND (Sender_ID = $recipientID Or Receiver_ID = $recipientID)
                    AND (IsDeleted = 0) And (GroupID = '')
                    Order By ID DESC LIMIT 100) as ROWS Order By ID DESC ";
                    $result = mysql_query($sql);
                }

                $subjectRows = mysql_fetch_assoc($subjectResult);
                $subject = $subjectRows['Subject'];
            }

            ?>


            <?php
            if (!empty($urlUsername)) {
                if (mysql_num_rows($result) > 0) {
                    $rowCount = true;
                    while ($rows = mysql_fetch_assoc($result)) {
                        $recipientID = $rows['Sender_ID'];
                        $message = $rows['Message'];
                        $date = $rows['MessageDate'];
                        $groupName = $rows['GroupName'];
                        if (checkBlock($ID, $recipientID)) {
                            $display = "style= 'display:none;'";
                            $display2 = "style='display:none;clear:both;'";
                            $display3 = "style=display:none;opacity:0.5;'";
                        }
                        else {
                            $display = "style='display:block;'";
                            $display2 = "style='clear:both;'";
                            $display3 = "style=opacity:0.5;'";
                        }
                        // get receiver name
                        $sql2 = "SELECT FirstName,LastName, ProfilePhoto,Username
                    FROM Members, Profile
                    WHERE Profile.Member_ID = $recipientID
                    AND Members.ID = $recipientID ";
                        $result2 = mysql_query($sql2) or die(mysql_error());
                        $rows2 = mysql_fetch_assoc($result2);
                        $pic = $rows2['ProfilePhoto'];
                        $name = $rows2['FirstName'] . ' ' . $rows2['LastName'];
                        $username = $rows2['Username'];
                        echo "
                    <div class='profileImageWrapper-Feed' $display>
                    <a href='/$username'>
                    <img src = '$mediaPath$pic' class='profilePhoto-Feed' alt='' />
                    </a>
                    </div>
                    <div class='profileNameWrapper-Feed' $display>
                    <a href='/$username'>
                    <div class=\"profileName-Feed\">$name</div>
                    </a>
                    </div>
                    ";
                        echo "<div class='post' $display2>" . nl2br($message) . "</div>";
                        echo "<div $display3>" . date('l F d Y g:i:s A', strtotime($date)) . "</div>";
                        echo "<hr/>";
                    }
                }
            }
            ?>



            <?php
                if (isAdmin($ID)) {
                echo "<a href='/backoffice?username=$urlUsername'>Calculate Redemptions</a>";
               }
               ?>

            <?php
            if (strstr($urlUsername, "?")) {
            } else if ($groupChatExist == false) {
                // reinitialize sender ID
                $recipientID = get_id_from_username($urlUsername);
                $readonly = "";
                if (checkBlock($ID, $recipientID)) {
                    $readonly = "disabled = 'disabled'";
                }
                $sql2 = "UPDATE Messages SET New = 0, FirstMessage = 0 WHERE ThreadOwner_ID = $ID AND (Receiver_ID = $ID) And (Sender_ID = $recipientID) And (GroupID = '') ";
                mysql_query($sql2) or die();
                $sql3 = "UPDATE Messages SET New = 0, FirstMessage = 0 WHERE ThreadOwner_ID = $ID AND (Sender_ID = $ID) And (Receiver_ID = $recipientID) And (GroupID = '') ";
                mysql_query($sql3) or die();
            }

            ?>

            <?php
            if ($urlUsername == 'playdoe') { ?>
            <div style="padding-bottom:30px;">

                <div onclick="document.getElementById('faq').style.display = 'block';" style="font-size:20px;color:red;padding-bottom:10px;"> FAQ</div>
                <table id="faq" style="display:none;border:1px solid blue">
                    <th class="lead faq-header" >I can't post anything</th>

                    <tr>
                        <td style="padding:5px;">
                            New members are required to validate their emails before posting.
                        </td>
                    </tr>


                    <th class="lead faq-header">I can't like or comment on anything</th>
                    </tr>
                    <td style="padding:5px;">
                        One post by each member needs to be shared in order to like and comment on other members post.
                    </td>
                    <tr>

                        <th class="lead faq-header">How do I make money?</th>
                    <tr>
                        <td style="padding:5px;">
                            Simply post stuff as you do on any other social network. But when you do, add the hashtag of the current date, i.e January 1, 2016 would be #1116.
                            The post with the most likes daily wins a $10 gift card. Then direct message Team Playde and let us know what gift card you would like.
                            <br/>
                            <lead>Please note there must be at least 10 posts per day for a gift card to be issued.</lead>
                        </td>
                    </tr>

                </table>

                <?php } ?>

<!--                <a id='pageStart' href='#'></a>-->


                <!-------------------------------------------------------------------->
            </div>
        </div>



<?php
require 'imports.php';
$ffmpeg = '/usr/local/bin/ffmpeg';
//require 'ads.php';
get_head_files();
get_header();
require 'memory_settings.php';


$ID = $_SESSION['ID'];

if (isSuspended($ID)) {
    echo "<script>alert('This account has been suspended. Please contact support: support@playdoe.com'); location = '/logout.php';</script>";

}


if (isset($_POST['submit'])) {
    // handle connection feed post
    $post = mysql_real_escape_string($_POST['post']);
//$category = $_POST['hashtag'];
    $city = $_SESSION['City'];
    $state = $_SESSION['State'];
    $IsSponsored = $_POST['IsSponsored'];

    $_SESSION['NewPostID'] = null;
    $newPostID = null;
    if ($_SESSION['Post'] == $_POST['post']) {
        echo "<script>alert('Your post appears to be empty');</script>";
    } /*else if ($category == "") {
        echo "<script>alert('Your post needs a hash tag');</script>";
    }*/ else {
        if (strlen($post) > 0) {

        // predict next post ID so when can reference each image to the new post
                    $sql = "SELECT ID FROM Posts Order by ID DESC LIMIT 1";
                    $result = mysql_query($sql);
                    $row = mysql_fetch_assoc($result);
                    $lastPostID = $row['ID'];
                    $newPostID = $lastPostID +1;
                    $_SESSION['NewPostID'] = $newPostID;

            $post = makeLinks($post);
            $post = hashtag_links($post);
            $post = mentionLink($post, $ID, $newPostID, 16);
            $post = "<p>$post</p>";


            // Loop through each image uploaded.
            if (strlen($_FILES['flPostMedia']['name'] > 0)) {
                foreach ($_FILES['flPostMedia']['tmp_name'] as $k => $v) {
                    $mediaName = $_FILES['flPostMedia']['name'][$k];

                    /*if (strlen($mediaName) == 0) {
                        echo "<script>alert('A photo or video is required'); location='/home'</script>";
                        exit;
                    }*/
                    // remove ALL WHITESPACE from image name
                    $mediaName = preg_replace('/\s+/', '', $mediaName);
                    // remove ALL SPECIAL CHARACTERS, Images paths are extremely sensitive
                    $mediaName = str_replace('/[^A-Za-z0-9\-]/', '', $mediaName);
                    // remove ampersand
                    $mediaName = str_replace('&', '', $mediaName);
                    $type = $_FILES['flPostMedia']['type'][$k];
                    $tempName = $_FILES['flPostMedia']['tmp_name'][$k];
                    $size = $_FILES['flPostMedia']['size'][$k];
                    $mediaFile = $tempName;

                    if (strlen($mediaName) > 0) {
// check file size
                        if ($size > 5000000000) {
                            echo '<script>alert("File is too large. The maximum file size is 500MB.");</script>';
                            exit();
                        }

                        $checkImage = getimagesize($mediaFile);
                        $width = $checkImage[0];
                        $height = $checkImage[1];

                        if (in_array($type, $photoFileTypes)) {
                            if ($width < 1080 || $height < 1080) {
                                echo '<script>alert("This image is too small. Make sure you are only uploading a photo or video you took with your mobile device");location = "/home"</script>';
                                exit;
                            }
                        }

                        // create media type arrays
                        $videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
                            "video/quicktime", "video/webm", "video/x-matroska",
                            "video/x-ms-wmw");
                        // photo file types
                        $photoFileTypes = array("image/jpg", "image/jpeg", "image/png", "image/tiff",
                            "image/gif", "image/raw");
                        $audioFileTypes = array("audio/wav", "audio/mp3", "audio/x-m4a");
                        // add unique id to image name to make it unique and add it to the file server
                        $fileName = pathinfo($mediaName, PATHINFO_FILENAME);
                        $mediaName = trim(uniqid() . $mediaName);

                        $mediaFile2 = "";
                        copy($tempName, $mediaFile2);
                        $mediaFile3 = "";
                        copy($tempName, $mediaFile3);
                        require 'media_post_file_path.php';
                        if (in_array($type, $audioFileTypes) || in_array($type, $videoFileTypes)) {
                            $audioName = $fileName;
                        }
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
                                // must save gifs as jpeg
                                $src = imagecreatefromjpeg($mediaFile);
                                echo "<script>alert('$src');</script>";
                            } else {
                                /* echo "<script>alert('Invalid File Type');</script>";
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
                        // if photo didn't get uploaded, notify the user
                        if (!file_exists($postMediaFilePath)) {
                            echo "<script>alert('File could not be uploaded, try uploading a different file type.');location='/home.php'</script>";
                            exit;
                        } else {
                            // store media pointer
                            $sql = "INSERT INTO Media (Member_ID,  MediaName,    MediaOgg,     MediaWebm,      MediaType,  MediaDate,  AudioName    ) Values
                                              ('$ID',    '$mediaName', '$oggFileName', '$webmFileName',  '$type',   CURRENT_DATE(), '$audioName'  )";
                            mysql_query($sql) or die(logError(mysql_error(), $url, "Storing Photo name from post into Media table"));
                            $mediaID = mysql_insert_id();
                            // get media ID
                            $sqlGetMedia = "SELECT * FROM Media WHERE MediaName = '$mediaName'";
                            $mediaResult = mysql_query($sqlGetMedia) or die(logError(mysql_error(), $url, "Inserting uploaded media name into media table"));
                            $mediaRow = mysql_fetch_assoc($mediaResult);
                            //$mediaID = $mediaRow['ID'];
                            $media = $mediaRow['MediaName'];
                            $mediaType = $mediaRow['MediaType'];
                            $mediaDate = $mediaRow['MediaDate'];
                        }
                        // build post links based on media type
                        if (in_array($type, $audioFileTypes)) {
                            $img = '<b>' . $audioName . '</b><br/><audio controls>
                            <source src="' . $mediaPath . $mediaName . '" type="' . $mediaType . '">
                            Your browser does not support the audio element.
                            </audio>';
                        }
                        if (in_array($type, $photoFileTypes)) {
                            $img = '<img src = "' . $mediaPath . $mediaName . '" width="100%" height="auto" />';
                        } // check if file type is a video
                        if (in_array($type, $videoFileTypes)) {
                            // where ffmpeg is located
                            $ffmpeg = '/usr/local/bin/ffmpeg';
                            // poster file name
                            $posterName = "poster" . uniqid() . ".jpg";
                            //where to save the image
                            $poster = "$posterPath$posterName";
                            //time to take screenshot at
                            //$interval = 3;
                            //screenshot size
                            //$size = '440x280'; -s $size
                            //ffmpeg command
                            $cmd = "$ffmpeg -i \"$postMediaFilePath\" -r 1 -ss 3 -t 1  -f image2 $poster 2>&1";
                            exec($cmd);
                            $img = '<video poster="/poster/' . $posterName . '" preload="none" autoplay="autoplay" muted controls width="100%" height="auto">
                                <source src = "' . $videoPath . $mediaName . '" type="video/mp4" />
                                <source src = "' . $videoPath . $oggFileName . '" type = "video/ogg" />
                                <source src = "' . $videoPath . $webmFileName . '" type = "video/webm" />
                                </video>';
                        }
                        $newImage .= $img.'<br/>';
                    }

                    // update Media table with new post id
                    $sqlUpdateMedia = "UPDATE Media SET PostID = $newPostID, Poster='$posterName' WHERE ID = '$mediaID' ";
                    mysql_query($sqlUpdateMedia) or die(logError(mysql_error(), $url, "Fetching next post ID to reference images to"));
                } // end of loop -----------------------------------
            }
            $post = $post . $newImage;

            $sql = "INSERT INTO Posts (Post,    Poster,	      Category,    Member_ID,  IsSponsored,      PostDate) Values
                                      ('$post', '$posterName', '$category', '$ID',     '$IsSponsored',   NOW())";
            mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting post with media"));
            $newPostID = mysql_insert_id();
            // update Media table with new post id
            $sqlUpdateMedia = "UPDATE Media SET Post_ID = $newPostID, PostID = $newPostID, Poster='$posterName' WHERE ID = '$mediaID' ";
            mysql_query($sqlUpdateMedia) or die(logError(mysql_error(), $url, "Updating Media table with new post ID"));
            if ($newPostID != null) {
                $_SESSION['NewPostID'] = $newPostID;
            }
        } // if no media
        else {

            $sql = "INSERT INTO     Posts (Post,       Category,    Member_ID,  IsSponsored,     PostDate) Values
                                      ('$post',   '$category',   '$ID',    '$IsSponsored',    NOW())";
            mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting post without media"));
        }
        alert_followers($newPostID);


    }

    echo "<script>location='/home'</script>";
}
?>
<?php

//-------------------------------------------------
// handle post comments
//-------------------------------------------------
if (isset($_POST['btnComment']) && ($_POST['btnComment'] == "Comment")) {
    $postID = $_POST['postID'];
    $ownerID = $_POST['memberID'];
    $comment = $_POST['postComment'];
    $comment = mysql_real_escape_string($comment);
    if (strlen($comment) > 0) {
// find urls
        $comment = makeLinks($comment);
        $comment = mentionLink($comment, $ID, $postID, 17);

        if ($_SESSION['PostComment'] == $_POST['postComment']) {
            echo "<script>alert('Your comment appears to be empty'); location='/home';</script>";
            exit;
        }
// if photo is provided
            if ($_FILES['flCommentMedia']['name'] != "") {

                foreach ($_FILES['flCommentMedia']['tmp_name'] as $k => $v) {
                    $mediaName = $_FILES["flCommentMedia"]["name"][$k];

                    // remove ALL WHITESPACE from image name
                    $mediaName = preg_replace('/\s+/', '', $mediaName);
                    // remove ALL special characters
                    $mediaName = str_replace('/[^A-Za-z0-9\-]/', '', $mediaName);
                    // remove ampersand
                    $mediaName = str_replace('&', '', $mediaName);
                    $fileName = pathinfo($mediaName, PATHINFO_FILENAME);
                    // add unique id to image name to make it unique and add it to the file server
                    $mediaName = trim(uniqid() . $mediaName);
                    $mediaFile = $_FILES['flCommentMedia']['tmp_name'][$k];
                    $type = trim($_FILES["flCommentMedia"]["type"][$k]);
                    $tempName = $_FILES['flCommentMedia']['tmp_name'][$k];
                    $size = $_FILES['flCommentMedia']['size'][$k];
                    $mediaFile = $tempName;
// check file size
                if ($size > 25000000000) {
                    echo '<script>alert("File is too large. The maximum file size is 50MB.");</script>';
                    header('Location:home.php');
                    exit;
                }
// check if file type is a photo
                $videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
                    "video/quicktime", "video/webm", "video/x-matroska",
                    "video/x-ms-wmw");
// video file types
                $photoFileTypes = array("image/jpg", "image/jpeg", "image/png", "image/tiff",
                    "image/gif", "image/raw");
                $audioFileTypes = array("audio/wav", "audio/mp3");

                require 'media_post_file_path.php';
                // create file type instance
                if (in_array($type, $audioFileTypes) || in_array($type, $videoFileTypes)) {
                    $audioName = $fileName;
                }
                if (in_array($type, $videoFileTypes)) {
                    // convert to mp4
                    $mediaString = 'video';
                    $newFileName = $fileName.".mp4";
                    $audioName = $fileName;
                    $ffmpeg = '/usr/local/bin/ffmpeg';
                    exec("$ffmpeg -i $newFileName -vcodec libx264 -pix_fmt yuv420p -profile:v baseline -preset slow -crf 22 -movflags +faststart $newFileName");
                    $mediaName = $newFileName;
                } else {
                    $mediaString = 'photo';
                    if ($type == "image/jpg" || $type == "image/jpeg") {
                        $src = imagecreatefromjpeg($mediaFile);
                    } else if ($type == "image/png") {
                        $src = imagecreatefrompng($mediaFile);
                    } else if ($type == "image/gif") {
                        // must save gifs as jpeg
                        $src = imagecreatefromjpeg($mediaFile);
                    } else {
                        /*echo "<script>alert('Invalid File Type');</script>";
                        header('Location:home.php');
                        exit;*/
                    }
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
// save photo/video
                require 'media_post_file_path.php';
                if (in_array($type, $videoFileTypes) || in_array($type, $audioFileTypes)) {
                    move_uploaded_file($mediaFile, $postMediaFilePath);
                } else {
                    // handle transparency
                    imagesavealpha($src, true);
                    if ($type == "image/jpg" || $type == "image/jpeg") {
                        imagejpeg($src, $postMediaFilePath, 50);
                    } else if ($type == "image/png") {
                        imagepng($src, $postMediaFilePath, 0, NULL);
                    } else {
                        imagegif($src, $postMediaFilePath, 50);
                    }
                }
// if photo didn't get uploaded, notify the user
                if (!file_exists($postMediaFilePath)) {
                    //echo "<script>alert('File could not be uploaded, try uploading a different file type.');</script>";

                } else {
                    // determine which table to put photo pointer in
                    // store media pointer
                    $sql = "INSERT INTO Media (Member_ID,  MediaName,  MediaType,  MediaDate,     AudioName    ) Values
                                              ('$ID',    '$mediaName', '$type',   CURRENT_DATE(), '$audioName')";
                    mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting media into media table"));
                    // get media ID
                    $sqlGetMedia = "SELECT * FROM Media WHERE MediaName = '$mediaName'";
                    $mediaResult = mysql_query($sqlGetMedia) or die(mysql_error());
                    $mediaRow = mysql_fetch_assoc($mediaResult);
                    $mediaID = $mediaRow['ID'];
                    $media = $mediaRow['MediaName'];
                    $mediaType = $mediaRow['MediaType'];
                    $mediaDate = $mediaRow['MediaDate'];
                    // build post links based on media type
                    if (in_array($type, $audioFileTypes)) {
                        $img = '<b>' . $audioName . '</b><br/><audio controls>
                            <source src="' . $mediaPath . $mediaName . '" type="' . $mediaType . '">
                            Your browser does not support the audio element.
                            </audio>';
                        $img = '<a href = "/media.php?id=' . $ID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '" ><br/>' . $img . '</a><br/><br/>';
                        $newImage .= $img.'<br/>';
                    }
                    elseif (in_array($type, $photoFileTypes)) {
                        $img = '<img src = "' . $mediaPath . $mediaName . '" />';
                        $img = '<a href = "/media.php?id=' . $ID . '&mid=' . $mediaID . '&mediaName=' . $media . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '">' . $img . '</a>';
                         $newImage .= $img.'<br/>';
                    } // check if file type is a video
                    elseif (in_array($type, $videoFileTypes)) {
                        // where ffmpeg is located
                        $ffmpeg = '/usr/local/bin/ffmpeg';
                        // poster file name
                        $posterName = "poster" . uniqid() . ".jpg";
                        //where to save the image
                        $poster = "$posterPath$posterName";
                        //time to take screenshot at
                        $interval = 3;
                        //screenshot size
                        //$size = '440x280'; -s $size
                        //ffmpeg command
                        $cmd = "$ffmpeg -i \"$postMediaFilePath\" -r 1 -ss 3 -t 1  -f image2 $poster 2>&1";
                        exec($cmd);
                        $img = '<video poster="/poster/' . $posterName . '" preload="none" autoplay="autoplay" muted controls>
                                <source src = "' . $videoPath . $mediaName . '" type="video/mp4" />
                                <source src = "' . $videoPath . $oggFileName . '" type = "video/ogg" />
                                <source src = "' . $videoPath . $webmFileName . '" type = "video/webm" />
                                </video>';
                        $newImage .= $img.'<br/>';
                    }
                }

                }
                    $comment = $comment . '<br/>'. $newImage;
                    $sql = "INSERT INTO PostComments (Post_ID,   Owner_ID,  Member_ID,   Comment, CommentDate  ) Values
                                                      ('$postID', '$ownerID', '$ID',      '$comment', NOW())";
                    mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting comment"));

            }


            $scrollx = $_REQUEST['scrollx'];
            $scrolly = $_REQUEST['scrolly'];
//A comment was just made, we need to send out some notifications.
//The first thing is to identify all of the id's connected with this post
            $user_id = $_SESSION['ID'];
//Get the ids of all the members connected with a post comment
            $sql = "SELECT Member_ID FROM PostComments WHERE Post_ID = $postID And Member_ID != $ID";
            $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting all ID of members who commented on post"));
            $comment_ids = array();
//Iterate over the results
            while ($rows = mysql_fetch_assoc($result)) {
                array_push($comment_ids, $rows['Member_ID']);
            }
//Boil the id's down to unique values because we dont want to send double emails or notifications
            $comment_ids = array_unique($comment_ids);
//Send consumer notifications
            foreach ($comment_ids as $item) {
                if (strlen($item) > 0) {
                    // only send email if account & email active
                    if (checkActive($item)) {
                        if (checkEmailActive($item)) {
                            build_and_send_email($user_id, $item, 1, $postID);
                        }
                    }
                }
            }
//Notify the post creator
            $sql = "SELECT Member_ID FROM Posts WHERE ID = '$postID' And Member_ID != $ID;";
            $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting post owner ID"));
            $rows = mysql_fetch_assoc($result);
            if (mysql_num_rows($result) > 0) {
                $creatorID = $rows['Member_ID'];
                if ($ID != $creatorID) {
                    if (checkEmailActive($ID)) {
                        build_and_send_email($ID, $creatorID, 1, $postID, '');
                    }
                }
            }
//------------------
//=========================================================================================================================//
//BELOW IS END OF POST COMMENT HANDLING CODE ==========================================================================//

    }
    echo "<script>location='/home?scrollx=$scrollx&scrolly=$scrolly'</script>";
}
?>

<?php
// handle Repost
if (isset($_POST['btnRepost']) && ($_POST['btnRepost'] == "Repost")) {

    $postID = $_POST['postID'];
    $post = getPost($postID);
    $post = mysql_escape_string($post);
    $postDate = $_POST['postDate'];
    $memberID = $_POST['memberID'];
    $ownerID = $_POST['memberID'];
    $reposterID = $_POST['reposterID'];
    $scrollx = $_REQUEST['scrollx'];
    $scrolly = $_REQUEST['scrolly'];

    $sql = "INSERT INTO Posts (Member_ID,     Post, Reposter_ID, OrigPost_ID, PostDate) Values
                              ('$memberID', '$post', $ID,        $postID,     '$postDate')";
    mysql_query($sql) or die(mysql_error());

    if (checkActive($memberID)) {
        if (checkEmailActive($memberID)) {
            build_and_send_email($ID, $memberID, 15, $postID);
        }
    }

    echo "<script>alert('Reposted!'); location='/home?&scrollx=$scrollx&scrolly=$scrolly'</script>";
}



if (isset($_POST['DeleteComment']) && $_POST['DeleteComment'] == "Delete") {
    $commentID = $_POST['commentID'];

    $scrollx = $_REQUEST['scrollx'];
    $scrolly = $_REQUEST['scrolly'];

    $sql = "Update PostComments SET IsDeleted = '1' WHERE ID = $commentID";
    mysql_query($sql) or die (mysql_error());
    echo "<script>location='/home?scrollx=$scrollx&scrolly=$scrolly'</script>";
}
?>

<?php
// block member
if (isset($_POST['block']) && $_POST['block'] == "Block This User") {
    $blockedID = $_POST['blockedID'];
    $ID = $_POST['ID'];

    $scrollx = $_REQUEST['scrollx'];
    $scrolly = $_REQUEST['scrolly'];

    $sql = "INSERT INTO Blocks (BlockerID,   BlockedID) Values
                              ('$ID',  '$blockedID')";
    mysql_query($sql) or die(mysql_error());
    echo "<script>location='/home?scrollx=$scrollx&scrolly=$scrolly'</script>";
}
?>


<?php
// validate email
if (isset($_POST['validate']) && $_POST['validate'] == 'Send Email Verification') {
    $ID = $_POST['id'];

    if (strstr($url, "local")) {
        $link = "/validate_email";
    }
    else if (strstr($url, "dev")) {
        $link = "http://dev.playdoe.com/validate_email";
    }
    else {
        $link = "http://www.playdoe.com/validate_email";
    }

    build_and_send_email(22, $ID, 14, $link);
    echo "<script>alert('Your validation link was sent'); location='/home'</script>";
}
?>

<script type="text/javascript" src="resources/js/site.js"></script>

<script type="text/javascript">
    function saveScrollPositions(theForm) {
        if(theForm) {
            var scrolly = typeof window.pageYOffset != 'undefined' ? window.pageYOffset : document.documentElement.scrollTop;
            var scrollx = typeof window.pageXOffset != 'undefined' ? window.pageXOffset : document.documentElement.scrollLeft;
            theForm.scrollx.value = scrollx;
            theForm.scrolly.value = scrolly;
        }
    }
</script>

<script>
    $(document).ready(function () {
        $("body").delegate(".btnApprove", "click", function () {
            var parentDiv = $(this).closest("div[id^=approvals]");
            var data = {
                postID: $(this).closest('div').find('.postID').val(),
                ID: $(this).closest('div').find('.ID').val(),
                memberID: $(this).closest('div').find('.memberID').val()
                //add other properties similarly
            }
            $.ajax({
                type: "post",
                url: "/post_approve.php",
                data: data,
                cache: true,
                success: function (data) {
                    parentDiv.html(data);
                }
            })
        });
    });
</script>

<script>
    $(document).ready(function () {
        $("body").delegate(".btnDisapprove", "click", function () {
            var parentDiv = $(this).closest("div[id^=approvals]");
            var data = {
                postID: $(this).closest('div').find('.postID').val(),
                ID: $(this).closest('div').find('.ID').val(),
                memberID: $(this).closest('div').find('.memberID').val()
                //add other properties similarly
            }
            $.ajax({
                type: "post",
                url: "/post_disapprove.php",
                data: data,
                cache: true,
                success: function (data) {
                    parentDiv.html(data);
                }
            })
        });
    });
</script>

<!--<script>
    $(document).ready(function () {
        $("body").delegate(".btnComment", "click", function () {
            var tempScrollTop = $(window).scrollTop();
            localStorage.setItem("scrollPos", tempScrollTop);
            var parentDiv = $(this).closest("div[id^=comment]");
            var data = {
                postID: $(this).closest('div').find('.postID').val(),
                ID: $(this).closest('div').find('.ID').val(),
                ownerId: $(this).closest('div').find('.ownerId').val()
                //add other properties similarly
            }
            $.ajax({
                type: "post",
                data: data,
                url: '/home.php',
                cache: true,
                success: function (data) {
                    $(window).scrollTop(tempScrollTop);
                }
            })
        });
    });
</script>-->

<script type="text/javascript">
    function showPost(long,short) {
        var longPost = document.getElementById(long);
        var shortPost = document.getElementById(short);
        if (longPost.style.display == 'none') {
            longPost.style.display = 'block';
            shortPost.style.display = 'none';
        }
    }
</script>
<script type="text/javascript">
    function showComments(id) {
        var e = document.getElementById(id);
        if (e.style.display == 'none') {
            e.style.display = 'block';
        }
        else
            e.style.display = 'none';
    }
</script>
<script>
    // show uploading
    function showUploading() {
        if (document.getElementById('post').value == '') {
            alert('Your post appears to be empty');
            return false;
        }

        document.getElementById("progress").style.display = "block";
        return true;
    }
</script>
<script>
    // show comment uploading
    function showCommentUploading(comment, theForm) {
        document.getElementById(comment).style.display = "block";
        saveScrollPositions(theForm);
    }
</script>




<script>
    function getCity(sel) {
        var state = sel.options[sel.selectedIndex].value;
        $.ajax({
            type: "POST",
            url: "/getCity.php",
            data: "state="+state+"&page=home",
            cache: false,
            beforeSend: function () {
            },
            success: function(html) {
                $("#divCity").html(html);
                $("#ddCurrentCity").hide();
            }
        });
    }
</script>

<script>
    function showOptions(id) {
        var blockButton = document.getElementById(id);
        if (blockButton.style.display == 'none') {
            blockButton.style.display = 'block';
        }
        else {
            blockButton.style.display = 'none';
        }
        //document.getElementById(id).select();
    }
</script>

<style>
    .roll-call {
        min-height: 100px;
    }

</style>



<script>
// load more posts after 10
    $(window).scroll(function () {
        if ($(document).height() <= $(window).scrollTop() + $(window).height()) {
            //alert("End Of The Page");

            // instantiate spinner as hidden
            document.getElementById('gettingMore').style.display = 'none';
            // get the lastPostID input value create by the connection feed code
            var lastPostID = document.getElementById('lastPostID').value;
            //$("#loadMoreConnections").load("/loadMoreConnections.php?lastPostID="+lastPostID);
            $('#loadMoreConnections').append($("<div>").load("/loadMoreConnections.php?lastPostID="+lastPostID));
            // remove the last post ID input element so we only get the last one created with php
            $("input[id=lastPostID]").remove();

            // if we are still getting posts then display spinner
            document.getElementById('gettingMore').style.display = 'none';

        }
        else {

        }

    });
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

<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />


<input type="hidden" id="refreshed" value="no">
<script type="text/javascript">
    // reload page (update likes) on desktop
    window.onload=function(){
        var e=document.getElementById("refreshed");
        if(e.value=="no")e.value="yes";
        else{e.value="no";location.reload();}
    }
</script>

<script>
    // reload page (update likes) on Iphone
    window.onpageshow = function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    }
</script>



<?php //check_demographics($ID); ?>

<!--empty onunload will clear browser cache for clean refresh -->
<body onunload="">
<div class="container containerFlush-home">
    <?php
    ?>
    <div class="row row-padding">

        <!--        <!--Middle Column -->
        <div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8">


        </div>

        <?php
        $genre = $_GET['genre'];
        if (!empty($genre)) {
            $_SESSION['Genre'] = $genre;
            $genre = $_SESSION['Genre'];
        } else {
            if (!empty($_SESSION['Genre'])) {
                $genre = $_SESSION['Genre'];
            } else {
                $genre = get_interest($ID);
            }
        }

        if ($genre == 'All') {
            $genre = 'Show All';
        }
        ?>

        <script type="text/javascript" src="jquery-1.8.0.min.js"></script>
        <script type="text/javascript">
            $(function(){

                $(".search").keyup(function()
                {
                     // clear results if empty
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
                            url: "search.php",
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

        <div align="center">
            <h5>
<div class="inner-addon left-addon">
<i class="glyphicon glyphicon-user"></i>
                <input type="text" class="search form-control" id="searchID" value="<?php $final_name ?>"
                       placeholder="Search for people" style="margin-bottom:-10px;"/>
</div>

                <div id="result"></div>
                <div id="previewNames"></div>


            </h5>
        </div>

        <!--Middle Column -->
        <div class="col-lg-offset-3 col-lg-6 col-md-offset-3 col-md-6 roll-call"
             align="left" >



            <?php
            // set local currency
            //setlocale(LC_MONETARY,"en_US");



            //do something with this information
            /*if( $iPod || $iPhone ){
                //browser reported as an iPhone/iPod touch -- do something here
            }else if($iPad){
                //browser reported as an iPad -- do something here
            }else if($Android){
                //browser reported as an Android device -- do something here
            }else if($webOS){
                //browser reported as a webOS device -- do something here
            }*/



                ?>

                <div style="padding-bottom:0px;">
                <div id="mentionResult"></div>
                    <?php require 'profile_menu.php'; ?>



                    <div style="margin-top:-10px">
                        <a href="/messages/<?php echo $username ?>"><img src = "/images/messages.png" height="20" width="20" /> <?php require 'getNewMessageCount.php' ?></a>
                        <a style="margin-left:20px;" href="/member_follows/<?php echo get_username($ID) ?>"><img src = "/images/follows.png" height="20" width="20" /><?php require 'getNewFollowCount.php' ?></a>

                       <?php
                echo "<span style='color:#888888;margin-left:20px;'><span style='color:#888888'><img src='/images/referral-icon.png' height='30' width='30' /> </span>";
                if (getReferrals($ID, get_referralID($ID)) > 0) { echo "<span style='background:red;padding:5px;'><a style='color:white;' href='/view_messages/redeem'>".getReferrals($ID, get_referralID($ID))."</a></span>"; };
                ?>

                    </div>

                </div>


<script>
              function showMentions(e) {
                  $("#post").on('keydown', function(){

var code = (e.keyCode ? e.keyCode : e.which);

                  // clear results if empty
                     if (!this.value.trim()) {
                        $('#mentionResult').html('');
                        return;
                    }

                        if(code == '50'){
                         // match on last @mention
 var lastMention = $(this).val().split(' ');
            var lastType = lastMention[lastMention.length - 1];

                        var searchid = lastType;

                        var dataString = 'search='+ searchid;
                           $.ajax({
                            type: "POST",
                            url: "/getMentions.php",
                            data: dataString,
                            cache: false,
                            success: function(html)
                            {

 $("#mentionResult").html(html).show();
                            }
                        });

                        }
                    });
                    }
                </script>

                <?php if (isEmailValidated($ID)) { ?>

                    <?php
                    /*if (hasHourPast($ID) == false) {
                        echo "<h5 align='center'>Great job! Post again in one hour. <img src='/images/hourglass.gif' height='50' width='50' /></h5>";
                    }
                    else {*/
                        //if (hasTenPost($ID) == false) {
                            ?>
<hr />
                            <form id="formPost" style="float:left;width:100%" method="post" enctype="multipart/form-data" action="" onsubmit="return showUploading()" >



                                <textarea name="post" id="post" class="form-control"
                                onkeydown='showMentions(event, this)'
                                style="float:left;border:none;font-size:17px"
                                onkeyup="this.style.height='24px'; this.style.height = this.scrollHeight + 12 + 'px';"
                                  placeholder="What's on your mind?" spellcheck="true"></textarea>

 <label style="float:left;clear:both" for="flPostMedia">
                        <img src="/images/camera.png" style="height:25px;width:25px;float:left;margin-right:10px;" />
                    </label>
                                <input type="file" name="flPostMedia[]" id="flPostMedia" class="flPostMedia" style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' multiple />


                                <input style="float:left;margin-left:0px;" type="submit" class="post-button" name="submit" id="submit" value="Post"/>
                            </form>

                                <div style="clear:both" id="image-holder"> </div>


<div id="progress" style="display:none;padding-top:5px;float:left">
                    <div style="float:left" class="progress">
                        <div style="float:left" class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" class="progress-bar">
                            <b>File uploading...please wait</b>
                        </div>
                    </div>
                </div>



                            <?php
                        } // hasTenPost
                       // else { echo "<h5 align='center'>You have reached your daily post limit, great job!"; }
                   // } // hasHourPast
               // } // isEmailValidated
                else {
                    echo "You must verify your email before you can post, like, comment or follow anyone. <br/>
                          <i>If you don't see a verification email, check your spam folder. </i>
                            <form method='post' action='' >
                                <input type='hidden' id='id' name='id' value='$ID' />
                                <input type='submit' id='validate' name='validate' value='Send Email Verification' />
                            </form>

<hr/>
<h4 align='center'>
There are plenty of gifts and prizes waiting on you right now!
</h4>

<img src='/images/giveaway.png' height=50% width=100% />
";
                }
                ?>


<!--Preview uploaded photos and videos -->
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
                    alert(file[0].name + " is not a valid image file.");
                    dvPreview.html("");
                    return false;
                }
            });
        } else {
            alert("This browser does not support HTML5 FileReader.");
        }
    });
});

            </script>


            <div onclick="document.getElementById('msg').style.display = 'block';" id="msg" style="display:none;" class="profile-on-hover">To get paid be sure to provide your Referral ID: <b><?php echo get_referralID($ID) ?></b></div>

        </div>

        <br/>




 <div class="col-lg-offset-3 col-lg-6 col-md-offset-3 col-md-6 roll-call"
             align="left" >

<link rel="stylesheet" href="/resources/css/jquery.bxslider.css">

<script src="resources/js/jquery.bxslider.min.js"></script>

<script>
$(document).ready(function(){
  $('#foobar').bxSlider();
});
</script>

<h4>Most requested giftcards</h4>
<div id="foobar">
  <li><img src="/giftcards/walmart-giftcard.png" height="80%" width="100%" /></li>
  <div><img src="/giftcards/target-giftcard.png" height="80%" width="100%" /></div>
  <li><img src="/giftcards/amazon-giftcard.jpg" height="60%" width="100%" /></li>
</div>
       <!-- SMARTADDON BEGIN --> <script type="text/javascript"> (function() { var s=document.createElement('script');s.type='text/javascript';s.async = true; s.src='http://s1.smartaddon.com/share_addon.js'; var j =document.getElementsByTagName('script')[0];j.parentNode.insertBefore(s,j); })(); </script>

<a href="http://www.smartaddon.com/?share" title="Share Button" onclick="return sa_tellafriend('http://playdoe.com')" style="margin-top:20px"><img alt="Share" src="http://s1.smartaddon.com/s12.png" border="0" /></a> <!-- SMARTADDON END -->

<span style="color:#9197a3;">Your referral ID:</span> </b><?php echo get_referralID($ID); ?>

<?php
$fSql = "SELECT DISTINCT Members.ID as fMemberID, Members.FirstName as fFirstName, Members.LastName as fLastName,
Members.Username as fUsername, Profile.ProfilePhoto as fProfilePhoto
FROM Members, Profile
WHERE Members.ID = Profile.Member_ID
AND Members.ID != $ID
AND Members.ID
IN (

SELECT Posts.Member_ID
FROM Posts
WHERE Posts.IsDeleted =0
GROUP BY Posts.ID
HAVING COUNT( Posts.ID ) >=1
)
AND Members.ID NOT
IN (

SELECT Followed_ID
FROM Follows
WHERE Follower_ID = $ID
GROUP BY Followed_ID
) Limit 4
";

$fResult = mysql_query($fSql) or die(mysql_error());
$fRows = mysql_fetch_assoc($fResult);

if (mysql_num_rows($fResult) > 0) {

echo "<h4 style='color:#8899a6'>Cool people to follow</h4><hr class='hr-line' />";

}

while ($fRows = mysql_fetch_assoc($fResult)) {

$fProfilePhoto = $fRows['fProfilePhoto'];
$fName = $fRows['fFirstName'].' '.$fRows['fLastName'];
$fUsername = $fRows['fUsername'];
$fProfileUrl = "/$fUsername";
$fMemberID = $fRows['fMemberID'];
?>

<div style="clear:both;float:left;" class="profileImageWrapper-Feed">
 <a href="<?php echo $fProfileUrl ?>">
                    <img src="<?php echo $mediaPath. $fProfilePhoto ?>" class="profilePhoto-Feed" alt=""
                         title="<?php echo $fName ?>" />
                </a>
</div>

<div class="profileNameWrapper-Feed" style="float:left;">
 <a href="<?php echo $fProfileUrl ?>">
                    <div style="float:left;" class="profileName-Feed"><?php echo $fName ?></div>
                </a>

<div id="followDiv1" style="float:right;margin-top:-5px;">
                 <table >
            <tr>
                <td >
                    <?php

                    $sqlFollow = "SELECT Follower_ID FROM Follows WHERE Follower_ID = $ID And Followed_ID = $fMemberID ";
                    $resultFollow = mysql_query($sqlFollow) or die (mysql_error());

                    if (isEmailValidated($ID)) {
                        if (mysql_num_rows($resultFollow) == 0) {
                            echo '<form>';
                            echo '<input type = "hidden" class = "followerID" value = "' . $ID . '" />';
                            echo '<input type = "hidden" class = "followedID" value = "' . $fMemberID . '">';
                            echo '<input type = "button" class = "btnFollow" value = "Follow" />';
                            echo '</form>';
                        } else {
                            echo '<form>';
                            echo '<input type = "hidden" class = "followerID" value = "' . $ID . '" />';
                            echo '<input type = "hidden" class = "followedID" value = "' . $fMemberID . '">';
                            echo '<input type = "button" class = "btnUnfollow" value = "Unfollow" />';
                            echo '</form>';
                        }
                    }
                    ?>
                </td>
            </tr>
        </table>
        </div>

</div>

<hr class="hr-line"/>
<?php




}

?>

             </div>











        <?php
        // pre-load Connection Feed
        // get genre selection

        if (isset($_POST['limit'])) {
            $limit = $_POST['limit'];
        }

        if (isset($_POST['searchState'])) {
            $searchState = $_POST['searchState'];

        }

        if (isset($_POST['searchCity'])) {
            $searchCity = $_POST['searchCity'];
        }


        if (!empty($genre) && $genre != "Show All") {
            $genreCondition = "And Posts.Category = '$genre' ";
        }
        else if($genre == "Show All") {
            $genre = '';
            $genreCondition = "And Posts.Category > '' ";
        }
        else { $genreCondition = "And Posts.Category = '$genre' "; }



        if (!empty($searchState)) {
            $stateCondition = "AND (Profile.State = '$searchState')";
        }
        else {
            $stateCondition = "";
        }
        ?>


        <?php
        $limit = "10";
        $postApprovalCondition = "(Select Count(ID) FROM PostApprovals Where Post_ID = PostID) As PostApprovals, ";
        $orderBy = "PostApprovals";
        $lastPostCondition = '';
        require 'connection-feed.php';
        ?>

    </div>


    <div id="loadMoreConnections">
    </div>



    <!--Right Column -->

</div>


<br/><br/>

</div>


    <div id="gettingMore" align="center" style="display:block;margin-top:-20px;" ><img src="/images/spinner.gif" height="50" width="50" /></div>



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

<?php
get_footer_files();
?>
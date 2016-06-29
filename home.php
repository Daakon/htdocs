
<!------------------------------------------------------
ALWAYS COMPRESS THIS FILE BEFORE PUSHING TO PRODUCTION
IT WILL INCREASE THE RENDERING TIME OF HTML ELEMENTS
------------------------------------------------------->
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
            $post = makeLinks($post);
            $post = hashtag_links($post);

            $post = "<p>$post</p>";

            // Loop through each image uploaded.
            if (strlen($_FILES['flPostMedia']['name'] > 0)) {
                foreach ($_FILES['flPostMedia']['tmp_name'] as $k => $v) {
                    $mediaName = $_FILES['flPostMedia']['name'][$k];

                    if (strlen($mediaName) == 0) {
                        echo "<script>alert('A photo or video is required'); location='/home'</script>";
                        exit;
                    }
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
                        $newImage .= $img;
                    }
                    // predict next post ID so when can reference each image to the new post
                    $sql = "SELECT ID FROM Posts Order by ID DESC LIMIT 1";
                    $result = mysql_query($sql);
                    $row = mysql_fetch_assoc($result);
                    $lastPostID = $row['ID'];
                    $lastPostID = $lastPostID +1;
                    $newPostID = $_SESSION['NewPostID'];
                    // update Media table with new post id
                    $sqlUpdateMedia = "UPDATE Media SET PostID = $lastPostID, Poster='$posterName' WHERE ID = '$mediaID' ";
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
                                      ('$post',   '$category',   '$ID',    '$IsSponsored',    CURDATE())";
            mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting post without media"));
        }
        alert_followers($lastPostID);
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
    $ownerId = $_POST['ownerID'];
    $comment = $_POST['postComment'];
    $comment = mysql_real_escape_string($comment);
    if (strlen($comment) > 0) {
// find urls
        $comment = makeLinks($comment);
        $comment = hashtag_links($comment);

        if ($_SESSION['PostComment'] == $_POST['postComment']) {
            echo "<script>alert('Your comment appears to be empty');</script>";
        } else {
// if photo is provided
            if (isset($_FILES['flPostMedia']) && strlen($_FILES['flPostMedia']['name']) > 1) {
// check file size
                if ($_FILES['flPostMedia']['size'] > 25000000000) {
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
                $mediaName = $_FILES["flPostMedia"]["name"];
                // remove ALL WHITESPACE from image name
                $mediaName = preg_replace('/\s+/', '', $mediaName);
                // remove ALL special characters
                $mediaName = str_replace('/[^A-Za-z0-9\-]/', '', $mediaName);
                // remove ampersand
                $mediaName = str_replace('&', '', $mediaName);
                $fileName = pathinfo($mediaName, PATHINFO_FILENAME);
                // add unique id to image name to make it unique and add it to the file server
                $mediaName = trim(uniqid() . $mediaName);
                $mediaFile = $_FILES['flPostMedia']['tmp_name'];
                $type = trim($_FILES["flPostMedia"]["type"]);
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
                    if (empty($exif) || !isset($exif)) {
                        echo "<script>alert('Your photo could not be uploaded');location='/home'</script>";
                        exit;
                    }
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
                    echo "<script>alert('File could not be uploaded, try uploading a different file type.');</script>";
                    header('Location:home.php');
                } else {
                    // determine which table to put photo pointer in
                    // store media pointer
                    $sql = "INSERT INTO Media (Member_ID,  MediaName,  MediaType,  MediaDate,     AudioName    ) Values
                                              ('$ID',    '$mediaName', '$type',   CURRENT_DATE(), '$audioName')";
                    mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting media name from post into Media table"));
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
                        $img = '<b>'.$audioName.'</b><br/><audio controls>
                            <source src="'.$mediaPath . $mediaName.'" type="'.$mediaType.'">
                            Your browser does not support the audio element.
                            </audio>';
                        $img = '<a href = "/media.php?id=' . $ID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '" ><br/>'.$img.'</a><br/><br/>';
                    }
                    if (in_array($type, $photoFileTypes)) {
                        $img = '<img src = "' . $mediaPath . $mediaName .'"  />';
                    } // check if file type is a video
                    elseif (in_array($type, $videoFileTypes)) {
                        // where ffmpeg is located
                        $ffmpeg = '/usr/local/bin/ffmpeg';
                        // poster file name
                        $posterName = "poster".uniqid().".jpg";
                        //where to save the image
                        $poster = "$posterPath$posterName";
                        //time to take screenshot at
                        $interval = 3;
                        //screenshot size
                        //$size = '440x280'; -s $size
                        //ffmpeg command
                        $cmd = "$ffmpeg -i \"$postMediaFilePath\" -r 1 -ss 3 -t 1  -f image2 $poster 2>&1";
                        exec($cmd);
                        $img = '<video poster="/poster/'.$posterName.'" preload="none" autoplay="autoplay" muted controls>
                                <source src = "' . $videoPath . $mediaName . '" type="video/mp4" />
                                <source src = "' . $videoPath . $oggFileName . '" type = "video/ogg" />
                                <source src = "' . $videoPath . $webmFileName . '" type = "video/webm" />
                                </video>';
                    } else {
                        // if invalid file type
                        /*echo '<script>alert("Invalid File Type!");</script>';
                        header('Location:home.php');
                        exit; */
                    }
                    $comment = $comment . '<br/><br/>' . $img . '<br/>';
                    $sql = "INSERT INTO PostComments (Post_ID,     Member_ID,  Owner_ID,   Comment, CommentDate  ) Values
                                                      ($postID,      $ID,      $ownerId, '$comment', CURDATE())";
                    mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting post comment"));

                }
            }
//----------------------
// if not comment photo
//----------------------
            else {
                $sql = "INSERT INTO PostComments (Post_ID,  Member_ID,  Owner_ID,   Comment,  CommentDate ) Values
                                                 ('$postID', '$ID',     '$ownerId', '$comment', CURDATE())";
                mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting comment without media"));
            }
            $scrollx = $_REQUEST['scrollx'];
            $scrolly = $_REQUEST['scrolly'];
//A comment was just made, we need to send out some notifications.
//The first thing is to identify all of the id's connected with this post
            $user_id = $_SESSION['ID'];
//Get the ids of all the members connected with a post comment
            $sql = "SELECT Member_ID FROM PostComments WHERE Post_ID = $postID And Member_ID != $ID ";
            $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting all IDs of members who commented on a post"));
            $comment_ids = array();
//Iterate over the results
            while ($rows = mysql_fetch_assoc($result)) {
                array_push($comment_ids, $rows['Member_ID']);
            }
//Boil the id's down to unique values because we dont want to send double emails or notifications
            $comment_ids = array_unique($comment_ids);
//Send consumer notifications
            foreach ($comment_ids as $item) {
                if (strlen($item) > 0 && $item != $ID) {
                    // only send email if account & email active
                    if (checkActive($item)) {
                        if (checkEmailActive($item)) {
                            build_and_send_email($user_id, $item, 1, $postID);
                        }
                    }
                }
            }
//Notify the post creator
            $sql = "SELECT Member_ID FROM Posts WHERE ID = $postID And Member_ID != $ID ";
            $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting post owner ID to notify of post comment"));
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
    }
    echo "<script>location='/home?&scrollx=$scrollx&scrolly=$scrolly'</script>";
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
<div class="container" style="margin-top:-20px;">
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



        <!--Middle Column -->
        <div class="col-lg-offset-3 col-lg-6 col-md-offset-3 col-md-6 roll-call"
             align="left" >

            <div style="margin-bottom:10px;margin-top:-20px;padding-bottom:10px;" align="center">


                <!--***********************************-->
            </div>

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

            // only show form on mobile devices
            if ($iPhone || $iPad || $Android) {
                ?>

                <div style="padding-left:10px;padding-bottom:0px;">
                    <?php require 'profile_menu.php'; ?>

                    <div style="margin-top:-10px">
                        <a href="/messages/<?php echo $username ?>"><img src = "/images/messages.png" height="20" width="20" /> <?php require 'getNewMessageCount.php' ?></a>
                        <a style="padding-left:20px;" href="/member_follows/<?php echo get_username($ID) ?>"><img src = "/images/follows.png" height="20" width="20" /><?php require 'getNewFollowCount.php' ?></a>
                        <br/><br/>
                        <?php
                        echo "<span style='color:#888888'><img src='images/Playdoe-Logo.png' height='50' width='50' /> Total Money: </span> ". getRedeemPoints($ID, get_username($ID));
                        if (getRedeemPoints($ID, get_username($ID)) > 9) { echo "<br/><a href='/view_messages/redeem'>Redeem My Money</a>"; };
                        ?>
                    </div>

                </div>

                <hr class="hr-line" />

                <?php if (isEmailValidated($ID)) { ?>

                    <?php
                    if (hasHourPast($ID) == false) {
                        echo "<h5 align='center'>Great job! Post again in one hour. <img src='/images/hourglass.gif' height='50' width='50' /></h5>";
                    }
                    else {
                        if (hasTenPost($ID) == false) {
                            ?>
                            <form method="post" enctype="multipart/form-data" action="" onsubmit="return showUploading()" >
                                <img src="/images/image-icon.png" height="30px" width="30px" alt="Photos/Video"/>
                                <strong>Upload Photos/Videos</strong><span style="color:red;padding-left:10px;">* Required</span>
                                <input type="file" width="10px;" name="flPostMedia[]" id="flPostMedia" />
                                <div id="image-holder"> </div>

                                <br/>
                        <textarea name="post" id="post" class="form-control textArea"
                                  placeholder="Add a caption to your photos & videos" ></textarea>
                                <br/>

                                <input type="submit" class="post-button" name="submit" id="submit" value="Post"/>
                            </form>
                            <?php
                        } // hasTenPost
                        else { echo "<h5 align='center'>You have reached your daily post limit, great job!"; }
                    } // hasHourPast
                } // isEmailValidated
                else {
                    echo "You must verify your email before posting <br/>
                            <form method='post' action='' >
                                <input type='hidden' id='id' name='id' value='$ID' />
                                <input type='submit' id='validate' name='validate' value='Send Email Verification' />
                            </form>
                        ";
                }
                ?>


                <div id="progress" style="display:none;padding-top:5px;">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" class="progress-bar">
                            <b>File uploading...please wait</b>
                        </div>
                    </div>
                </div>

                <hr class="hr-line">

            <?php } ?>

            <script>
                // preview image upload
                $("#flPostMedia").on('change', function () {

                    if (typeof (FileReader) != "undefined") {

                        var image_holder = $("#image-holder");
                        image_holder.empty();

                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $("<img />", {
                                "src": e.target.result,
                                "class": "thumb-image"
                            }).appendTo(image_holder);

                            $("<video />", {
                                "src": e.target.result,
                                "class": "thumb-image"
                            }).appendTo(image_holder);
                        }
                        image_holder.show();
                        reader.readAsDataURL($(this)[0].files[0]);
                    } else {
                        alert("This browser does not support image preview.");
                    }
                });

            </script>


            <?php if (!$iPhone && !$iPad && !$Android) { ?>
                <?php require 'profile_menu.php'; ?>
                <a href="/messages/<?php echo $username ?>"><img src = "/images/messages.png" height=20" width="20" /> <?php require 'getNewMessageCount.php' ?></a>
                <a style="padding-left:20px;" href="/member_follows/<?php echo get_username($ID) ?>"><img src = "/images/follows.png" height="20" width="20" /><?php require 'getNewFollowCount.php' ?></a>
                <br/><br/>
                <?php
                echo "<span style='color:#888888'><span style='color:#888888'><img src='images/Playdoe-Logo.png' height='50' width='50' /> Total Money: </span>". getRedeemPoints($ID, get_username($ID));
                if (getRedeemPoints($ID, get_username($ID)) > 9) { echo "<br/><a href='/view_messages/redeem'>Redeem My Money</a>"; };
                ?>
                <hr class="hr-line"/>
            <?php } ?>
            <br/>
            Make <b>$50-$100</b> On Playdoe Now!
            <br/>
            Follow our Twitter page to find out how.
            <a href="https://twitter.com/officialplaydoe" class="twitter-follow-button" data-show-count="false">Follow @officialplaydoe</a>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
        </div>

        <br/>




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



    <!--Middle Column -->

</div>


<br/><br/>

</div>

<?php if ($noPosts == false) { ?>

    <div id="gettingMore" align="center" style="display:block;margin-top:-20px;" ><img src="/images/spinner.gif" height="50" width="50" /></div>

<?php } ?>

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
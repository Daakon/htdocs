<?php

require 'imports.php';
get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];
$hashtag = $_SESSION['Hashtag'];
preg_match("/[^\/]+$/",$url ,$match);
$username = $match[0];

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
            echo "<script>alert('Your comment appears to be empty');</script>";
        } else {
// if photo is provided
            if (isset($_FILES['flCommentMedia']) && strlen($_FILES['flCommentMedia']['name'] > 0)) {

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
                       // echo "<script>alert('File could not be uploaded, try uploading a different file type.');</script>";

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
                        }
                        if (in_array($type, $photoFileTypes)) {
                            $img = '<img src = "' . $mediaPath . $mediaName . '" />';
                            $img = '<a href = "/media.php?id=' . $ID . '&mid=' . $mediaID . '&mediaName=' . $media . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '">' . $img . '</a>';
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
                        } else {
                            // if invalid file type
                            /*echo '<script>alert("Invalid File Type!");</script>';
                            header('Location:home.php');
                            exit; */
                        }
                    }
                }
                $comment = $comment . '<br/>' . $newImage;
                $sql = "INSERT INTO PostComments (Post_ID,   Owner_ID,  Member_ID,   Comment, CommentDate  ) Values
                                                      ('$postID', '$ownerID', '$ID',      '$comment', NOW())";
                mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting comment"));
            }
//----------------------
// if not comment photo
//----------------------
            else {
                $sql = "INSERT INTO PostComments (Post_ID,  Owner_ID,   Member_ID,    Comment, CommentDate ) Values
                                                ('$postID', '$ownerID', '$ID',      '$comment', CURDATE())";
                mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting post comment without media"));
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
    }
    echo "<script>location='/post.php/$username#comment$postID'</script>";
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
    $ownerID = $_POST['ownerID'];
    $reposterID = $_POST['reposterID'];
    $scrollx = $_REQUEST['scrollx'];
    $scrolly = $_REQUEST['scrolly'];
    $_SESSION['ScrollX'] = $scrollx;
    $_SESSION['ScrollY'] = $scrolly;

    $username = get_username($memberID);

    $sql = "INSERT INTO Posts (Member_ID,     Post, Reposter_ID, OrigPost_ID, PostDate) Values
                              ('$memberID', '$post', $ID,        $postID,     '$postDate')";
    mysql_query($sql) or die(mysql_error());

    if (checkActive($memberID)) {
        if (checkEmailActive($memberID)) {
            build_and_send_email($ID, $memberID, 15, $postID);
        }
    }

    echo "<script>alert('Reposted!'); location='/post/$username'</script>";
}


// ----------------------------
//delete post
// ----------------------------
if (isset($_POST['Delete']) && $_POST['Delete'] == "Delete") {
    $postID = $_POST['postID'];
    $repostID = $_POST['repostID'];
    $isRepost = $_POST['isRepost'];
    $deleteCondition = '';

    if ($isRepost == false) {
        // delete original post and all reposts
        $deleteCondition = "Where ID = $postID Or OrigPost_ID = $postID ";
    }
    else {
        // only delete repost
        $deleteCondition = "Where ID = $repostID ";
    }

    $username = get_username($ID);
    $sql = "Update Posts SET IsDeleted = 1 $deleteCondition ";
    mysql_query($sql) or die (logError(mysql_error(), $url, "Deleting Post"));
    echo "<script>alert('Post deleted!'); location='/post/$username'</script>";
}

// delete comment
if (isset($_POST['DeleteComment']) && $_POST['DeleteComment'] == "Delete") {
    $commentID = $_POST['commentID'];
    $sql = "Update PostComments SET IsDeleted = '1' WHERE ID = $commentID";
    mysql_query($sql) or die (logError(mysql_error(), $url, "Deleting comment"));
    echo "<script>location='/manage_posts/$username?scrollx=$scrollx&scrolly=$scrolly'</script>";
}
?>


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
                ID: $(this).closest('div').find('.ID').val()
                //add other properties similarly
            }
            $.ajax({
                type: "post",
                url: "/post_approve.php",
                data: data,
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
                ID: $(this).closest('div').find('.ID').val()
                //add other properties similarly
            }
            $.ajax({
                type: "post",
                url: "/post_disapprove.php",
                data: data,
                success: function (data) {
                    parentDiv.html(data);
                }
            })
        });
    });
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
    // show comment uploading
    function showCommentUploading(comment, theForm) {
        document.getElementById(comment).style.display = "block";
        saveScrollPositions(theForm);
    }
</script>

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
            $('#loadMorePosts').append($("<div>").load("/loadMorePosts.php?lastPostID="+lastPostID+"&username=<?php echo $username ?>"));
            // remove the last post ID input element so we only get the last one created with php
            $("input[id=lastPostID]").remove();

            // if we are still getting posts then display spinner
            document.getElementById('gettingMore').style.display = 'none';

        }
        else {

        }

    });
</script>

<body>

<div class="container containerFlush">


    <div class="row row-padding" >

        <div class="col-lg-offset-3 col-lg-6 col-md-offset-3 col-md-6 roll-call"
             align="left" style="min-height: 10px;">
            <?php

            require 'profile_menu.php';
            ?>

            <?php if (get_id_from_username($username) == $ID) {  ?>
                <img src="<?php echo get_users_photo_by_id(get_id_from_username($username)) ?>" class="profilePhoto-Feed" alt=""
                     title="<?php echo $name ?>" />
                <span style="color:#888888;padding-left:5px;">Your posts</span>
                <?php
            } else { ?>
                <div style="margin-top:-40px"><img src="<?php echo get_users_photo_by_id(get_id_from_username($username)) ?>"  class="profilePhoto-Feed " alt=""
                                                   title="$name ?" />
                    <span style="color:#8888;padding-left:5px;"><?php echo get_users_name_by_id(get_id_from_username($username))?>'s Post</span></div>
            <?php }   ?>
        </div>

        <?php

        ?>

        <?php
        $limit = "10";
        $lastPostCondition = '';

        $username = get_username_from_url();
        $username = explode("?", $username);
        $username = $username[0];
        $profileID = get_id_from_username($username);

        // Display AD if not posts by profile owner
        $sql = "SELECT ID FROM Posts WHERE Member_ID = $profileID And IsDeleted = 0 Limit 1";
        $result = mysql_query($sql) or die(mysql_error());
        $count = mysql_num_rows($result);

        if (mysql_num_rows($result) == 0) { ?>
        <div class="col-lg-offset-3 col-lg-6 col-md-offset-3 col-md-6 col-sm-12 col-xs-12 roll-call-feed" >
            <img src="/ads/payday-loan.jpg" height="50%" width="100%" />
            <p>
                Short term payday loans...$500... fulltime job, checking account required.
                Must be on your job at least a year. 888-415-1593 Innovation Global LLC
            </p>
            </div>
        <?php } else {

        require 'post-feed.php'; ?>
        <div id="gettingMore" align="center" style="float:left;margin-top:-20px;" ><img src="/images/spinner.gif" height="50" width="50" /></div>
<?php } ?>

    </div>


<div id="loadMorePosts">
</div>


</div>

<!--Right Column -->
<br/><br/>


</body>

</html>



<?php
$scrollx = 0;
$scrolly = 0;
if(!empty($_SESSION['ScrollX'])) {
    $scrollx = $_SESSION['ScrollY'];
}
if(!empty($_SESSION['ScrollY'])) {
    $scrolly = $_SESSION['ScrollY'];
}
?>

<script type="text/javascript">
    window.scrollTo(<?php echo "$scrollx" ?>, <?php echo "$scrolly" ?>);
</script>


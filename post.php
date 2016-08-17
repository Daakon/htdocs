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
                postID: $(this).closest('tr').find('.postID').val(),
                ID: $(this).closest('tr').find('.ID').val()
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
                postID: $(this).closest('tr').find('.postID').val(),
                ID: $(this).closest('tr').find('.ID').val()
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

        $ID = $_SESSION['ID'];
        $sql1 = "SELECT BlockedID, BlockerID FROM Blocks WHERE (BlockerID = $ID Or BlockedID = $ID)";
        $result1 = mysql_query($sql1) or die(logError(mysql_error(), $url, ""));

        $blockIDs = array();

        // get blocked IDs
        while ($rows1 = mysql_fetch_assoc($result1)) {
            if ($rows1['BlockedID'] != $ID) {
                array_push($blockIDs, $rows1['BlockedID']);
                if ($rows1['BlockerID'] != $ID) {
                    array_push($blockIDs, $rows1['BlockerID']);
                }
            }
        }

        $username = get_username_from_url();
        $username = explode("?", $username);
        $username = $username[0];
        $profileID = get_id_from_username($username);
        $sql = "SELECT DISTINCT
    Members.ID As MemberID,
    Members.FirstName As FirstName,
    Members.LastName As LastName,
    Members.Username As Username,
    Posts.ID As PostID,
    Posts.Post As Post,
    Posts.Category As Category,
    Posts.PostDate As PostDate,
    Posts.Reposter_ID as ReposterID,
    Posts.OrigPost_ID as OrigPostID,
    Posts.IsSponsored As IsSponsored,
    Profile.ProfilePhoto As ProfilePhoto
    FROM Members,Posts,Profile
    WHERE
    ((Posts.Member_ID = $profileID And (Posts.Reposter_ID = NULL or Posts.Reposter_ID = 0)) Or (Posts.Reposter_ID = $profileID))
    And (Members.IsActive = 1)
    And (Members.IsSuspended = 0)
    And (Members.ID = Posts.Member_ID)
    And (Members.ID = Profile.Member_ID)
    And (Posts.Category = '$hashtag')
    And (Posts.IsDeleted = 0)
    Group By PostID
    Order By PostID DESC ";
        $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting all member posts"));
        if (mysql_num_rows($result) > 0) {
        while ($rows = mysql_fetch_assoc($result)) {
        $memberID = $rows['MemberID'];
        $name = $rows['FirstName'] . ' ' . $rows['LastName'];
        $firstName = $rows['FirstName'];
        $profilePhoto = $rows['ProfilePhoto'];
        $category = $rows['Category'];
        $post = $rows['Post'];
        $postID = $rows['PostID'];
        $postDate = $rows['PostDate'];
        $repostID = $rows['PostID'];
        $reposterID = $rows['ReposterID'];
        $origPostID = $rows['OrigPostID'];
        $isSponsored = $rows['IsSponsored'];
        $postCount = $rows['PostCount'];
        ?>

        <div class="col-lg-offset-3 col-lg-6 col-md-offset-3 col-md-6 col-sm-12 col-xs-12 roll-call-feed" >

            <?php
            $repostText = '';
            $img = '';
            $isRepost = false;
            $prestinePostID = $rows['PostID'];

            // check if post is a repost
            if (!empty($reposterID) && isset($reposterID) && $reposterID != 0) {

                $postID = $origPostID;

                if ($reposterID == $ID) {
                    $img = "<img src='/images/repost_icon.png' style='float:left;' height='20' width='20'/>";
                    $reposterName = get_users_name($ID);
                    $repostText = "$img You reposted <br/><br/>";
                    $reposterUsername = get_username($ID);
                    echo "<div style='margin-left:10px;color:#8899a6;float:left;'><a style='color:#8899a6' href='/$reposterUsername'>$repostText</a></div>";
                }
                else {
                    $img = "<img src='/images/repost_icon.png' style='float:left;' height='20' width='20'/>";
                    $reposterName = get_users_name($reposterID);
                    $reposterUsername = get_username($reposterID);
                    $repostText = $img . $reposterName ." reposted <br/><br/>";


                    echo "<div style='margin-left:10px;color:#8899a6;float:left;'><a style='color:#8899a6' href='/$reposterUsername'>$repostText</a></div>";
                }}

            $profileUrl = "/$username";
            ?>

            <div style="clear:both" class="profileImageWrapper-Feed">
                <a href="<?php echo $profileUrl ?>">
                    <img src="<?php echo $mediaPath. $profilePhoto ?>" class="profilePhoto-Feed " alt=""
                         title="<?php echo $name ?>" />
                </a>
            </div>

            <div class="profileNameWrapper-Feed" >
                <a href="<?php echo $profileUrl ?>">
                    <div class="profileName-Feed"><?php echo $name ?></div>
                </a>
                <div class="date"><?php echo date('l F j, Y',strtotime($postDate)); ?>
                    <?php if ($isSponsored) { echo "<br/>Sponsored"; } ?>
                </div>
            </div>

            <div class="post" style="clear:both;">
                <?php
                // remove excessive white space inside anchor tags
                $post = preg_replace('~>\s+<~', '><', $post);
                // trim white space
                $post = trim($post);
                // remove excessive line breaks
                $post = cleanBrTags($post);

                // check check post length if it has a url in it
                if (strstr($post, "http://") || strstr($post, "https://")) {
                    echo nl2br($post);
                }
                else if (strlen($post) > 700) {
                    $post500 = substr($post, 0, strpos($post, ' ', 700)).'<br/>'; ?>

                    <?php echo nl2br($post500) ?>
                    <br/>
                    <a style="display:block;" style="width:100%;" href="show_post?postID=<?php echo $postID ?>&email=0">
                        <span style="color:black;font-weight: 800">Show More</span>
                    </a>

                    <?php
                }
                else {
                    echo nl2br($post);
                }
                ?>
            </div>

            <?php
            if (isEmailValidated($ID) && hasOnePost($ID)) {
                $disabled = '';
            } else {
                $disabled = 'disabled';
            }
            //check if member has approved this post
            //----------------------------------------------------------------
            //require 'getSessionType.php';
            $sql2 = "SELECT ID FROM PostApprovals WHERE Post_ID = '$postID' AND Member_ID = '$ID'";
            $result2 = mysql_query($sql2) or die(logError(mysql_error(), $url, "Getting member approval"));
            $rows2 = mysql_fetch_assoc($result2);
            // get approvals for each post
            $approvals = mysql_num_rows(mysql_query("SELECT * FROM PostApprovals WHERE Post_ID = $postID "));
            // show disapprove if members has approved the post
            echo '<table class="postApprovalsAlign">';
            echo '<tr>';
            echo '<td>';
            echo "<div id = 'approvals$postID'>";
            if (mysql_num_rows($result2) > 0) {
                echo '<form>';
                echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';
                echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
                echo '<input type ="button" class = "btnDisapprove" />';
                if ($approvals > 0) {
                    echo '&nbsp;<span>' . $approvals . '</font>';
                }
                echo '</form>';
            } else {
                echo '<form>';
                echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';
                echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
                echo '<input type ="button" class = "btnApprove" />';
                if ($approvals > 0) {
                    echo '&nbsp;<span>' . $approvals . '</font>';
                }
                echo '</form>';
            }
            echo '</div>'; // end of approval div
            echo '</td></tr></table>';
            //-------------------------------------------------------------
            // End of approvals
            //-----------------------------------------------------------

            if (isset($ID)) { ?>

                <?php if ($ID != $memberID) {?>
                    <a href="/view_messages/<?php echo $username ?>" class="messageEnvelope"><img src = "/images/messages.png" height="20" width="20" /> </a>
                <?php } ?>

            <?php } ?>


            <?php $optionsID = "options$prestinePostID"; ?>

            <?php if ($ID != $memberID) { ?>

                <?php if ($reposterID == $ID) { } else {
                    if (hasReposted($ID, $postID)) { $repostDisabled = "disabled"; } else { $repostDisabled = ''; }
                    ?>

                    <form class="repostAlign" action="" method="post" onsubmit="return confirm('Are you sure you want to repost this?') && saveScrollPositions(this)" >
                        <input type="image" id="btnRepost" name="btnRepost" value="Repost" src="/images/repost_icon.png" style="margin-left:20px;margin-top:3px;" <?php echo $repostDisabled ?> />
                        <input type="hidden" id="memberID" name="memberID" value="<?php echo $memberID ?>" />
                        <input type="hidden" id="postID" name="postID" value="<?php echo $postID ?>" />
                        <input type="hidden" id="postDate" name="postDate" value="<?php echo $postDate ?>" />
                        <input type="hidden" id="reposterID" name="reposterID" value="<?php echo $ID ?>" />
                        <input type="hidden" name="scrollx" id="scrollx" value="0"/>
                        <input type="hidden" name="scrolly" id="scrolly" value="0"/>
                    </form>

                <?php } ?>

                <a href="javascript:showOptions('<?php echo $optionsID ?>');" class="blockLink">...</a>

            <?php } ?>

            <?php
            $postPath = getPostPath();
            $shareLinkID = "shareLink$prestinePostID"; ?>
            <a class="shareLink" href="javascript:showLink('<?php echo $shareLinkID ?>');">
                <img style="margin-left:20px;" src="/images/share.png" height="25px" width="25px" />
            </a>

            <?php $shareLink = 'show_post?postID='.$postID.'&email=1';
            $shareLink = $postPath.$shareLink;
            $shortLink = shortenUrl($shareLink);
            ?>

            <!--DELETE BUTTON ------------------>
            <?php if ($ID == get_id_from_username($username)) { ?>
                <form class="deleteButtonAlign" action="" method="post" onsubmit="return confirm('Do you really want to delete this post?')">
                    <input type="hidden" name="postID" id="postID" value="<?php echo $postID ?>"/>
                    <input type="hidden" name="repostID" id="repostID" value="<?php echo $repostID ?>" />
                    <input type="hidden" name="isRepost" id="isRepost" value="<?php echo $isRepost ?>" />
                    <input type="image" name="Delete" id="Delete" value="Delete" src="/images/delete.png" style="height:20px;width:20px;" />
                </form>
            <?php } ?>


            <!------------------------------------->
            <?php


            //Detect device
            $iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
            $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
            $iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
            $Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
            $webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");
            ?>


            <hr class="hr-line"/>

            <script>
                function showCommentMentions(e) {
                    $("#comment").on('keydown', function(){

                        var code = (e.keyCode ? e.keyCode : e.which);

                        // clear results if empty
                        if (!this.value.trim()) {
                            $('#commentMentionResult').html('');
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
                                url: "getCommentMentions.php",
                                data: dataString,
                                cache: false,
                                success: function(html)
                                {

                                    $("#commentMentionResult").html(html).show();
                                }
                            });

                        }
                    });
                }
            </script>


            <div id="commentMentionResult"></div>

            <div style="clear:both;margin-top:-20px;margin-bottom:10px;margin-left:10px;">


                <!--Show block button here show it displays clearly between engagement icons and comment box -->
                <div style="display:none;" id="<?php echo $optionsID ?>">
                    <form action="" method="post" onsubmit="return confirm('Do you really want to block this member?') && saveScrollPositions(this) ">
                        <input type="hidden" id="blockedID" name="blockedID" class="blockedID" value="<?php echo $memberID ?>" />
                        <input type="hidden" id="ID" name="ID" class="ID" value="<?php echo $ID ?>" />
                        <input type="hidden" name="scrollx" id="scrollx" value="0"/>
                        <input type="hidden" name="scrolly" id="scrolly" value="0"/>
                        <input type="submit" id="block" name="block" class="btn btn-primary" style="margin-left:10px;background:red;" value="Block This User" />
                    </form>
                </div>

                <input id="<?php echo $shareLinkID ?>" style="display:none;margin-left:10px;" value ="<?php echo $shortLink ?>" />


                <form style="width:100%" class="commentBoxAlign" method="post" action="" enctype="multipart/form-data"
                      onsubmit="showCommentUploading('comment<?php echo $postID?>', this);">


                    <input type="file" style='z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' name="flCommentMedia[]" id="flCommentMedia" multiple onchange='$("#upload-photo-info").html($(this).val());' />

                        <textarea id="comment" onkeydown='showCommentMentions(event, this)' style="margin-top:10px;float:left;border:none;font-size:17px;" name="postComment" id="postComment" onkeyup="this.style.height='24px'; this.style.height = this.scrollHeight + 12 + 'px';"
                                  placeholder="Write a comment" title='' ></textarea>
                    <br/><br/>

                    <label style="float:left;clear:both" for="flCommentMedia">
                        <img src="/images/camera.png" style="height:25px;width:25px;float:left;margin-right:10px;" />
                    </label>
                    <input type="submit" name="btnComment" id="btnComment" class="btn btn-primary" style="float:left;" Value="Comment"  />


                    <input type="hidden" name="postID" id="postID" class="postID" Value="<?php echo $postID ?>"/>
                    <input type="hidden" name="ID" id="ID" class="ID" value="<?php echo $ID ?>"/>
                    <input type="hidden" name="memberID" id="memberID" class="memberID" value="<?php echo $memberID ?>"/>
                    <input type="hidden" name="scrollx" id="scrollx" value="0"/>
                    <input type="hidden" name="scrolly" id="scrolly" value="0"/>

                    <br/><br/>

                    <span style="float:left;clear:both;margin-top:10px;" class='label label-info' id="upload-photo-info"></span>

                    <br/><br/>
                    <div id="comment<?php echo $postID ?>" style="display:none;float:left;clear:both;">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" >
                                <b>File uploading...please wait</b>
                            </div>
                        </div>
                    </div>

                </form>

                <br/>



                <?php
                $sql3 = "SELECT DISTINCT
                        PostComments.Comment As PostComment,
                        PostComments.ID As PostCommentID,
                        PostComments.CommentDate As CommentDate,
                        Members.ID As CommenterID,
                        Members.FirstName as FirstName,
                        Members.LastName As LastName,
                        Members.Username As Username,
                        Profile.ProfilePhoto As ProfilePhoto
                        FROM PostComments,Members, Profile
                        WHERE
                        PostComments.Post_ID = $postID
                        AND Members.ID = Profile.Member_ID
                        And Members.ID = PostComments.Member_ID
                        And (Members.ID Not in ( '" . implode($blockIDs, "', '") . "' ))
                        And PostComments.IsDeleted = 0
                        Group By PostComments.ID
                        Order By PostComments.ID DESC LIMIT 3 ";
                $result3 = mysql_query($sql3) or die(logError(mysql_error(), $url, "Getting first 3 post comments"));
                echo '<br/>';
                if (mysql_num_rows($result3) > 0) {
                    echo '<div class="comment-style commentStyleAlign" >';
                    while ($rows3 = mysql_fetch_assoc($result3)) {

                        $comment = $rows3['PostComment'];
                        $profilePhoto = $rows3['ProfilePhoto'];
                        $commentID = $rows3['PostCommentID'];
                        $commentOwnerID = $rows3['CommenterID'];
                        $commentDate = $rows3['CommentDate'];
                        $commenterUsername = $rows3['Username'];
                        $commenterProfileUrl = "/$commenterUsername";

                        echo '<div class="comment-row">';
                        echo '<div class="profileImageWrapper-Feed">
                        <a href='.$commenterProfileUrl.'>
                        <img src = "' . $mediaPath . $profilePhoto . '" class ="profilePhoto-Feed" />
                        </a>
                        </div>

                         <div class="commentNameWrapper-Feed" >
                          <a href='.$commenterProfileUrl.'>
                            <div class="profileName-Feed"><?php echo $name ?> ' .
                            $rows3['FirstName'] . ' ' . $rows3['LastName'] .
                            '</div>
                         </a>
                          <div class="date">'. date('l F j, Y',strtotime($commentDate)) .'</div>
                         ' . nl2br($comment) . '

                        </div>
                    <div class="comment-content" style="clear:both"></div>';
                        echo '</div>';

                        if ($commentOwnerID == $ID || $ID == $memberID) {
                            //<!--DELETE BUTTON ------------------>
                            echo '<div class="comment-delete" >';
                            echo '<form action="" method="post" onsubmit="return confirm(\'Do you really want to delete this comment?\')">';
                            echo '<input type="hidden" name="commentID" id="commentID" value="' .  $commentID . '" />';
                            echo '<input type ="image" name="DeleteComment" id="DeleteComment" value="Delete" src="/images/delete.png" style="height:20px;width:20px;margin-left:10px;" />';
                            echo '</form>';
                            echo '</div>';
                            //<!------------------------------------->
                        }
                    }

                    echo '</div>';
                }
                ?>
                <!--Show more comments -->

                <?php
                $sql4 = "SELECT DISTINCT
                        PostComments.Comment As PostComment,
                        PostComments.ID As PostCommentID,
                        PostComments.CommentDate As CommentDate,
                        Members.ID As CommenterID,
                        Members.FirstName as FirstName,
                        Members.LastName As LastName,
                        Members.Username As Username,
                        Profile.ProfilePhoto As ProfilePhoto
                        FROM PostComments,Members, Profile
                        WHERE
                        PostComments.Post_ID = $postID
                        And Members.ID = PostComments.Member_ID
                        And (Members.ID Not in ( '" . implode($blockIDs, "', '") . "' ))
                        And PostComments.IsDeleted = 0
                        And Members.ID = Profile.Member_ID
                        Group By PostComments.ID
                        Order By PostComments.ID DESC LIMIT 3, 100 ";
                $result4 = mysql_query($sql4) or die(logError(mysql_error(), $url, "Getting 3 to 100 comments"));
                $moreComments = "moreComments$postID";
                if (mysql_num_rows($result4) > 0) {
                    ?>

                    <a style="padding-left:10px;" href="javascript:showComments('<?php echo $moreComments ?>');">Show More</a>

                    <div id="<?php echo $moreComments ?>" style="display:none;">

                        <div class="comment-style commentStyleAlign">

                            <?php
                            while ($rows4 = mysql_fetch_assoc($result4)) {
                                $comment = $rows4['PostComment'];
                                $profilePhoto = $rows4['ProfilePhoto'];
                                $commentID = $rows4['PostCommentID'];
                                $commentOwnerID = $rows4['CommenterID'];
                                $commenterUsername = $rows4['Username'];
                                $commenterProfileUrl = "/$commenterUsername";
                                $commentDate = $rows4['CommentDate'];
                                ?>

                                <div class="comment-row">
                                    <div class="profileImageWrapper-Feed">
                                        <a href='<?php echo $commenterProfileUrl ?>'>
                                            <img src = "<?php echo $mediaPath . $profilePhoto ?>" class ="profilePhoto-Feed" />
                                        </a>
                                    </div>
                                </div>

                                <div class="commentNameWrapper-Feed">
                                    <a href='<?php echo $commenterProfileUrl ?>'>
                                        <div class="profileName-Feed">
                                            <?php echo $rows4['FirstName'] . ' ' . $rows4['LastName'] ?>
                                        </div>
                                    </a>
                                    <div class="date"><?php echo date('l F j, Y',strtotime($commentDate)) ?></div>
                                    <?php echo nl2br($comment) ?>
                                </div>
                                <div class="comment-content" style="clear:both"></div>

                                <!--DELETE BUTTON ------------------>
                                <?php if ($commentOwnerID == $ID || $memberID == $ID) { ?>
                                    <div class="comment-delete" >
                                        <form action="" method="post" onsubmit="return confirm(\'Do you really want to delete this comment?\')">
                                            <input type="hidden" name="commentID" id="commentID" value="' .  $commentID . '" />
                                            <input type ="image" name="DeleteComment" id="DeleteComment" value="Delete" src="/images/delete.png" style="height:30px;width:30px;margin-left:10px;" />
                                        </form>
                                    </div>
                                <?php } ?>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <!---------------------------------------------------
                          End of comments div
                          ----------------------------------------------------->
    </div>
    <?php
    }
    }
    else { ?>
        <div class="row row-padding">
            <div class="col-lg-offset-3 col-lg-6 col-md-offset-3 col-md-6 roll-call-feed" align="left">
                <?php if ($ID == get_id_from_username($username)) { ?>
                    <div style="padding-left:15px;">You do not have anything posted.</div>
                <?php } else {
                    $firstName = get_user_firstName(get_id_from_username($username));
                    ?>
                    <div style="padding-left:15px;"><?php echo $firstName ?> does not have anything posted.</div>
                <?php } ?>
            </div>
        </div>
    <?php }
    ?>
</div>
<!--Right Column -->


</div>


<br/><br/>

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
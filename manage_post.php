<?php
require 'imports.php';
get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];
preg_match("/[^\/]+$/",$url ,$match);
$username = $match[0];
?>

<?php include('media_sizes.html');  ?>

<?php
//-------------------------------------------------
// handle post comments
//-------------------------------------------------
if (isset($_POST['btnComment']) && ($_POST['btnComment'] == "Comment")) {
    $postID = $_POST['postID'];
    $ownerId = $_POST['memberID'];
    $comment = $_POST['postComment'];
    $comment = mysql_real_escape_string($comment);
    if (strlen($comment) > 0) {
// find urls
        $comment = makeLinks($comment);
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
                // remove ALL SPECIAL CHARACTERS, Images paths are extremely sensitive
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
                    echo "<script>alert('File could not be uploaded, try uploading a different file type.');</script>";
                    header('Location:home.php');
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
                        $img = '<b>'.$audioName.'</b><br/><audio controls>
                            <source src="'.$mediaPath . $mediaName.'" type="'.$mediaType.'">
                            Your browser does not support the audio element.
                            </audio>';
                        $img = '<a href = "media.php?id=' . $ID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '" ><br/>'.$img.'</a><br/><br/>';
                    }
                    if (in_array($type, $photoFileTypes)) {
                        $img = '<img src = "' . $mediaPath . $mediaName .'" />';
                        $img = '<a href = "media?id=' . $ID . '&mid=' . $mediaID . '&mediaName=' . $media . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '">' . $img . '</a>';
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
                    $sql = "INSERT INTO PostComments (Post_ID,     Member_ID,   Comment, CommentDate  ) Values
                                                      ('$postID', '$ID',      '$comment', CURDATE())";
                    mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting comment"));
// create post
                    // get poster data
                    $sqlPoster = "SELECT ID, FirstName, LastName, Gender FROM Members WHERE ID = '$ID' ";
                    $resultPoster = mysql_query($sqlPoster) or die(logError(mysql_error(), $url, "Getting comment poster data"));
                    $rowsPoster = mysql_fetch_assoc($resultPoster);
                    $name = $rowsPoster['FirstName'] . ' ' . $rowsPoster['LastName'];
                    $posterId = $rowsPoster['ID'];
                    $gender = $rowsPoster['Gender'];
                    $nameLink = $name;
// get post owner ID
                    $sql = "SELECT Member_ID FROM Posts WHERE ID = $postID";
                    $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting post owner ID"));
                    $rows = mysql_fetch_assoc($result);
                    $ownerId = $rows['Member_ID'];
                    $sqlOwner = "SELECT ID, FirstName, LastName FROM Members WHERE ID = '$ownerId' ";
                    $resultOwner = mysql_query($sqlOwner) or die(mysql_error());
                    $rowsOwner = mysql_fetch_assoc($resultOwner);
                    $name2 = $rowsOwner['FirstName'] . ' ' . $rowsOwner['LastName'];
                    $name2 = $name2."'s";
                    $ownerId = $rowsOwner['ID'];
                    $name2Link = $name2;
                    $orgPost = "<a href='/show_post.php?postID=$postID'>status</a>";
                    $orgPostSql = "SELECT Category FROM Posts WHERE ID = $postID ";
                    $orgPostResult = mysql_query($orgPostSql) or die(logError(mysql_error(), $url, "Getting original post commented on"));
                    $orgPostRow = mysql_fetch_assoc($orgPostResult);
                    $orgInterest = $orgPostRow['Category'];
                    // determine noun if profile owner commented on their own post and write bulletin
                    if ($ownerId == $ID) {
                        $noun = "a $orgPost they posted.";
                    }
                    else {
                        $noun = $name2 . ' post.';
                    }
                    $post = "$nameLink posted a new $mediaString comment on $noun<br/><br/>$img<br/>";
                    $post = mysql_real_escape_string($post);
                    $sqlInsertPost = "INSERT INTO Posts (Post,     Member_ID,   Category,         PostDate  ) Values
                                                        ('$post', '$ID',      '$orgInterest',    CURDATE() ) ";
                    mysql_query($sqlInsertPost) or die(mysql_error());
                    $newPostID = mysql_insert_id();
// update new photo with bulletin id for commenting later
                    $sql = "UPDATE Media SET Post_ID = '$newPostID' WHERE MediaName = '$mediaName' ";
                    mysql_query($sql) or die(mysql_error());
                }
            }
//----------------------
// if not comment photo
//----------------------
            else {
                $sql = "INSERT INTO PostComments (Post_ID,  Member_ID,    Comment, CommentDate ) Values
                                                ('$postID', '$ID',      '$comment', CURDATE())";
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
    echo "<script>location='/manage_post/$username?scrollx=$scrollx&scrolly=$scrolly'</script>";
}
?>

<?php
// ----------------------------
//delete post
// ----------------------------
if (isset($_POST['Delete']) && $_POST['Delete'] == "Delete") {
    $postID = $_POST['postID'];
    $sql = "Update Posts SET IsDeleted = '1' WHERE ID = $postID And Member_ID = $ID ";
    mysql_query($sql) or die (logError(mysql_error(), $url, "Deleting Post"));
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

<div class="container">


    <div class="row row-padding">

        <div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 roll-call"
             align="left" style="min-height: 10px;">
            <?php require 'profile_menu.php'; ?>
        </div>

        <?php
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
    Profile.ProfilePhoto As ProfilePhoto
    FROM Members,Posts,Profile
    WHERE
    Posts.Member_ID = $profileID
    And(Members.IsActive = 1)
    And (Members.IsSuspended = 0)
    And (Members.ID = Posts.Member_ID)
    And (Members.ID = Profile.Member_ID)
    And (Posts.IsDeleted = 0)
    Group By PostID
    Order By PostID DESC ";
        $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting all member posts"));
        if (mysql_num_rows($result) > 0) {
        while ($rows = mysql_fetch_assoc($result)) {
        $memberID = $rows['MemberID'];
        $name = $rows['FirstName'] . ' ' . $rows['LastName'];
        $firstName = $rows['FirstName'];
        $username = $rows['Username'];
        $profilePhoto = $rows['ProfilePhoto'];
        $category = $rows['Category'];
        $post = $rows['Post'];
        $postID = $rows['PostID'];
        $postDate = $rows['PostDate'];
        ?>

        <div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 roll-call"
             align="left">

            <div class="profileImageWrapper-Feed">
                <a href="<?php echo $profileUrl ?>">
                    <img src="<?php echo $mediaPath. $profilePhoto ?>" class="profilePhoto-Feed " alt=""
                         title="<?php echo $name ?>" />
                </a>
            </div>

            <div class="profileNameWrapper-Feed" >
                <a href="<?php echo $profileUrl ?>">
                    <div class="profileName-Feed"><?php echo $name ?></div>
                </a>
                <div class="date"><?php echo date('l F j, Y',strtotime($postDate)); ?></div>
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

            <hr/>

            <a href='/post-interest.php?interest=<?php echo urlencode($category) ?>' onclick="saveScrollPositionOnLinkClick('/manage_post/<?php echo $username ?>')"><span class="engageText">#<?php echo $category ?></span></a>

            <?php if ($memberID != $ID) { ?>
                | <a href="/view_messages.php/<?php echo $username ?>"><span class="engageText"><img src="/images/messages.png" height="20" width="20" /> Message </span></a>
            <?php } ?>


            <br/><br/>
            <?php
            $postPath = getPostPath();
            $shareLinkID = "shareLink$postID"; ?>
            <a href="javascript:showLink('<?php echo $shareLinkID ?>');">
                <img src="/images/share.gif" height="50px" width="50px" />
                <span style="color:black;font-weight:bold;">Share This Post</span>
            </a>

            <?php $shareLink = 'show_post?postID='.$postID.'&email=1';
            $shortLink = shortenUrl($postPath.$shareLink);
            ?>

            <input id="<?php echo $shareLinkID ?>" style="display:none;" value ="<?php echo $shortLink ?>" />

            <?php if ($_SESSION['ID'] == get_id_from_username($username)) { ?>

                <div class="content-space" style="padding-top:20px;">
                    <!--DELETE BUTTON ------------------>
                    <form action="" method="post" onsubmit="return confirm('Do you really want to delete this post?')">
                        <input type="hidden" name="postID" id="postID" value="<?php echo $postID ?>"/>
                        <input type="submit" name="Delete" id="Delete" value="Delete" class="deleteButton"/>
                    </form>
                </div>
                <!------------------------------------->
                <?php
            }
            echo "<hr/>";
            //check if member has approved this post
            //----------------------------------------------------------------
            //require 'getSessionType.php';
            $sql2 = "SELECT ID FROM PostApprovals WHERE Post_ID = '$postID' AND Member_ID = '$ID'";
            $result2 = mysql_query($sql2) or die(logError(mysql_error(), $url, "Getting member approval"));
            $rows2 = mysql_fetch_assoc($result2);
            // get approvals for each post
            $approvals = mysql_num_rows(mysql_query("SELECT * FROM PostApprovals WHERE Post_ID = $postID "));
            // show disapprove if members has approved the post
            echo '<table>';
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
            ?>
            <div style="padding-top:10px;padding-bottom:10px;margin-top:10px;">
                <form method="post" action="" enctype="multipart/form-data"
                      onsubmit="showCommentUploading('comment<?php echo $postID?>', this);">
                    <input type="text" class="form-control" name="postComment" id="postComment"
                           placeholder="Write a comment" title='' />
                    <h6>Attach A Photo/Video To Your Comment</h6>
                    <input type="file" name="flPostMedia" id="flPostMedia" style="max-width:180px;"/>
                    <br/>
                    <div id="comment<?php echo $postID ?>" style="display:none;">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                <b>File uploading...please wait</b>
                            </div>
                        </div>
                    </div>
                    <input type="submit" name="btnComment" id="btnComment" Value="Comment"/>
                    <input type="hidden" name="postID" id="postID" Value="<?php echo $postID ?>"/>
                    <input type="hidden" name="ID" id="ID" value="<?php echo $ID ?>"/>
                    <input type="hidden" name="ownerId" id="ownerId" value="<?php echo $memberID ?>"/>
                    <input type="hidden" name="scrollx" id="scrollx" value="0"/>
                    <input type="hidden" name="scrolly" id="scrolly" value="0"/>
                </form>
                <?php
                $sql3 = "SELECT DISTINCT
                        PostComments.Comment As PostComment,
                        PostComments.ID As PostCommentID,
                        Members.ID As CommenterID,
                        Members.FirstName as FirstName,
                        Members.LastName As LastName,
                        Profile.ProfilePhoto As ProfilePhoto
                        FROM PostComments,Members, Profile
                        WHERE
                        PostComments.Post_ID = $postID
                        AND Members.ID = Profile.Member_ID
                        And Members.ID = PostComments.Member_ID
                        And PostComments.IsDeleted = 0
                        Group By PostComments.ID
                        Order By PostComments.ID DESC LIMIT 3 ";
                $result3 = mysql_query($sql3) or die(logError(mysql_error(), $url, "Getting first 3 post comments"));
                echo '<br/>';
                if (mysql_num_rows($result3) > 0) {
                    echo '<div class="comment-style">';
                    while ($rows3 = mysql_fetch_assoc($result3)) {
                        $comment = $rows3['PostComment'];
                        $profilePhoto = $rows3['ProfilePhoto'];
                        $commentID = $rows3['PostCommentID'];
                        $commentOwnerID = $rows3['CommenterID'];
                        echo '<div class="comment-row">';
                        echo '<div class="profileImageWrapper-Feed">
                        <a href='.$commenterProfileUrl.'>
                        <img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" class ="img-responsive" />
                        </a>
                        </div>
                         <div class="commentNameWrapper-Feed" style="padding-left:10px">
                          <a href='.$commenterProfileUrl.'>
                            <div class="profileName-Feed"><?php echo $name ?> ' .
                            $rows3['FirstName'] . ' ' . $rows3['LastName'] .
                            '</div>
                         </a>
                         ' . nl2br($comment) . '
                        </div>
                    <div class="comment-content" style="clear:both"></div>';
                        echo '</div>';
                        if ($commentOwnerID == $ID || $ID == $memberID) {
                            //<!--DELETE BUTTON ------------------>
                            echo '<div class="comment-delete">';
                            echo '<form action="" method="post" onsubmit="return confirm(\'Do you really want to delete this comment?\')">';
                            echo '<input type="hidden" name="commentID" id="commentID" value="' .  $commentID . '" />';
                            echo '<input type ="submit" name="DeleteComment" id="DeleteComment" value="Delete" class="deleteButton" />';
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
                        Members.ID As CommenterID,
                        Members.FirstName as FirstName,
                        Members.LastName As LastName,
                        Profile.ProfilePhoto As ProfilePhoto
                        FROM PostComments,Members, Profile
                        WHERE
                        PostComments.Post_ID = $postID
                        And Members.ID = PostComments.Member_ID
                        And PostComments.IsDeleted = 0
                        And Members.ID = Profile.Member_ID
                        Group By PostComments.ID
                        Order By PostComments.ID DESC LIMIT 3, 100 ";
                $result4 = mysql_query($sql4) or die(logError(mysql_error(), $url, "Getting 3 to 100 comments"));
                $moreComments = "moreComments$postID";
                if (mysql_num_rows($result4) > 0) {
                    ?>

                    <a href="javascript:showComments('<?php echo $moreComments ?>');">Show More</a>

                    <div id="<?php echo $moreComments ?>" style="display:none;">

                        <div class="comment-style">

                            <?php
                            while ($rows4 = mysql_fetch_assoc($result4)) {
                                $comment = $rows4['PostComment'];
                                $profilePhoto = $rows4['ProfilePhoto'];
                                $commentID = $rows4['PostCommentID'];
                                $commentOwnerID = $rows4['CommenterID'];
                                $commenterUsername = $rows4['Username'];
                                $commenterProfileUrl = "/$commenterUsername";
                                ?>
                                <div class="comment-row">
                                    <div class="profileImageWrapper-Feed">
                                        <a href='<?php echo $commenterProfileUrl ?>'>
                                            <img src = "<?php echo $mediaPath . $profilePhoto ?>" height = "50" width = "50" class ="enlarge-onhover img-responsive" />
                                        </a>
                                    </div>
                                </div>

                                <div class="commentNameWrapper-Feed">
                                    <a href='<?php echo $commenterProfileUrl ?>'>
                                        <div class="profileName-Feed">
                                            <?php echo $rows4['FirstName'] . ' ' . $rows4['LastName'] ?>
                                        </div>
                                    </a>
                                    <?php echo nl2br($comment) ?>
                                </div>
                                <div class="comment-content" style="clear:both"></div>

                                <!--DELETE BUTTON ------------------>
                                <?php if ($commentOwnerID == $ID || $memberID == $ID) { ?>
                                    <div class="comment-delete">
                                        <form action="" method="post" onsubmit="return confirm(\'Do you really want to delete this comment?\')">
                                            <input type="hidden" name="commentID" id="commentID" value="' .  $commentID . '" />
                                            <input type ="submit" name="DeleteComment" id="DeleteComment" value="Delete" class="deleteButton" />
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
                <!---------------------------------------------------
                                  End of comments div
                                  ----------------------------------------------------->
            </div>
            <?php
            }
            }
            else { ?>
                <div class="row row-padding">
                    <div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 roll-call" align="left">
                        <?php if ($ID == get_id_from_username($username)) { ?>
                            <div>You do not have anything posted.</div>
                        <?php } else {
                            $firstName = get_user_firstName(get_id_from_username($username));
                            ?>
                            <div><?php echo $firstName ?> does not have anything posted.</div>
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
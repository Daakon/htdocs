<?php
require 'imports.php';

get_head_files();
get_header();
$ID = $_SESSION['ID'];


//-------------------------------------------------
// handle post comments
//-------------------------------------------------
if (isset($_POST['btnComment']) && ($_POST['btnComment'] == "Comment")) {
$postID = $_POST['postID'];
$ownerID = $_POST['memberID'];
$comment = $_POST['postComment'];
$hashtag = $_POST['hashtag'];

$comment = mysql_real_escape_string($comment);
if (strlen($comment) > 0) {
// find urls
    $comment = makeLinks($comment);
    $comment = mentionLink($comment, $ID, $postID, 17);

    if ($_SESSION['PostComment'] == $_POST['postComment']) {
        echo "<script>alert('Your comment appears to be empty');</script>";
    } else {
// if photo is provided
        if (file_exists($_FILES['flCommentMedia']['name'] > 0)) {

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
                    $newFileName = $fileName . ".mp4";
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
                        $newImage .= $img . '<br/>';
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
            $ID = $_SESSION['ID'];
            $sql = "INSERT INTO PostComments (Post_ID,  Member_ID,  Comment,  CommentDate ) Values
                                              ($postID, $ID,       '$comment', CURDATE())";

            mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting post comment with no media"));
        }

        $scrollx = $_REQUEST['scrollx'];
        $scrolly = $_REQUEST['scrolly'];

//A comment was just made, we need to send out some notifications.
//The first thing is to identify all of the id's connected with this post
        $user_id = $_SESSION['ID'];
//Get the ids of all the members connected with a post comment
        $sql = "SELECT Member_ID FROM PostComments WHERE Post_ID = $postID And Member_ID != $ID ";
        $result = mysql_query($sql) or die(mysql_error());
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
        $sql = "SELECT Member_ID FROM Posts WHERE ID = '$postID' And Member_ID != $ID ";
        $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting member ID to notify of comment on post"));
        $rows = mysql_fetch_assoc($result);
        if (mysql_num_rows($result) > 0) {
            $creatorID = $rows['Member_ID'];
            if (checkEmailActive($ID)) {
                build_and_send_email($ID, $creatorID, 1, $postID, '');
            }
        }

//------------------

//=========================================================================================================================//
//BELOW IS END OF POST COMMENT HANDLING CODE ==========================================================================//
    }
}
    echo "<script>location='/show_post.php?postID=$postID&email=1&scrollx=$scrollx&scrolly=$scrolly'</script>";
}
?>

<?php

if (isset($_POST['DeleteComment']) && $_POST['DeleteComment'] == "Delete") {
    $commentID = $_POST['commentID'];
    $postID = $_POST['postID'];
    $sql = "Update PostComments SET IsDeleted = '1' WHERE ID = $commentID";
    mysql_query($sql) or die (mysql_error());
    echo "<script>location='/show_post?postID=$postID&scrollx=$scrollx&scrolly=$scrolly'</script>";
}


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

    $sql = "INSERT INTO Posts (Member_ID,     Post, Reposter_ID, OrigPost_ID, PostDate) Values
                              ('$memberID', '$post', $ID,        $postID,     '$postDate')";
    mysql_query($sql) or die(mysql_error());

    echo "<script>location='/show_post?postID=$postID&scrollx=$scrollx&scrolly=$scrolly'</script>";
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
    echo "<script>location='/home';</script>";
}
?>

<script src="/resources/js/site.js"></script>

<script>
    // show comment uploading
    function showCommentUploading(comment, theForm) {
        document.getElementById(comment).style.display = "block";
        saveScrollPositions(theForm);
    }
</script>

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
    // cache the page so we can do a hard redirect and refresh prior page values
    function myFunction() {
        var page = readCookie('Page');
        var scrolly = readCookie('Scrolly');
        window.location=page+"?scrolly="+scrolly;
    }
</script>



<body>

<div class="container containerFlush">


    <div class="row row-padding">




        <?php
        $postID = $_GET['postID'];

        if (isset($_SESSION['ID']) && !empty($_SESSION['ID'])) {
            $blockCondition = " And (Members.ID Not in ( '" . implode($blockIDs, "', '") . "' )) ";
            $ID = $_SESSION['ID'];
            $sql1 = "SELECT BlockedID, BlockerID FROM Blocks WHERE (BlockerID = $ID Or BlockedID = $ID)";
            $result1 = mysql_query($sql1) or die(logError(mysql_error(), $url, ""));

            $blockIDs = array();

            //Get blocked IDS
            while ($rows1 = mysql_fetch_assoc($result1)) {
                if ($rows1['BlockedID'] != $ID) {
                    array_push($blockIDs, $rows1['BlockedID']);
                    if ($rows1['BlockerID'] != $ID) {
                        array_push($blockIDs, $rows1['BlockerID']);
                    }
                }
            }
        }
        else { $blockCondition = ''; }

        $sql = "SELECT DISTINCT
    Members.ID As MemberID,
    Members.FirstName As FirstName,
    Members.LastName As LastName,
    Members.Username As Username,
    Posts.ID As PostID,
    Posts.Post As Post,
    Posts.PostDate As PostDate,
    Posts.Category As Category,
        Posts.Reposter_ID as ReposterID,
        Posts.OrigPost_ID as OrigPostID,
    Posts.IsSponsored As IsSponsored,
    Profile.ProfilePhoto As ProfilePhoto
    FROM Members,Posts,Profile
    WHERE
    Posts.ID = $postID
    And Members.IsActive = 1
    And Members.IsSuspended = 0
    And Members.ID = Posts.Member_ID
    And Members.ID = Profile.Member_ID
    $blockCondition
    And Posts.IsDeleted = 0
    Group By Posts.ID
    Order By Posts.ID DESC ";


        $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting single post data"));

        if (mysql_num_rows($result) == 0) {
            echo "<h1>This post no longer exists</h1>";
            echo '<image src = "'.$imagesPath.'/sad-emoticon.png" height="150" width="150"/>';
        }

        if (mysql_num_rows($result) > 0) {
        while ($rows = mysql_fetch_assoc($result)) {
        $memberID = $rows['MemberID'];
        $firstName = $rows['FirstName'];
        $lastName = $rows['LastName'];
        $name = $rows['FirstName'] . ' ' . $rows['LastName'];
        $name = checkNameLength($name);
        $username = $rows['Username'];
        $profilePhoto = $rows['ProfilePhoto'];
        $category = $rows['Category'];
        $post = $rows['Post'];
        $postID = $rows['PostID'];
        $postDate = $rows['PostDate'];
        $reposterID = $rows['ReposterID'];
        $origPostID = $rows['OrigPostID'];
        $isSponsored = $rows['IsSponsored'];
        ?>

        <div class="row row-padding">
            <div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 roll-call"
                 align="left">

                <?php
                $email = $_GET['email'];
                if ($email == 1) {
                    ?>
                    <li><a href="/home" class="backButton"><img src="/images/back.png" height="20" width="20" /></a></li>
                <?php }

                else { ?>
                    <li><a href="javascript:history.back()" class="backButton"><img src="/images/back.png" height="20" width="20" /></a></li>
                <?php }
                ?>

                <?php
                $repostText = '';
                $img = '';
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
                        <img src="<?php echo $mediaPath. $profilePhoto ?>" class="profilePhoto-Feed enlarge-onhover " alt=""
                             title="<?php echo $name ?>" />
                    </a>
                </div>

                <div class="profileNameWrapper-Feed">
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

                    echo nl2br($post);

                    ?>
                </div>

                <?php
                if (empty($ID) && !isset($ID)) {
                    $disabled = 'readonly';
                }
                else {
                    $disabled = '';
                }

                //check if member has approved this post
                //----------------------------------------------------------------
                //require 'getSessionType.php';
                echo "<div class='content-space'' >";
                $sql2 = "SELECT ID FROM PostApprovals WHERE Post_ID = '$postID' AND Member_ID = '$ID'";
                $result2 = mysql_query($sql2) or die(logError(mysql_error(), $url, "Getting post approvals"));
                $rows2 = mysql_fetch_assoc($result2);


                // get approvals for each post
                $approvals = mysql_num_rows(mysql_query("SELECT * FROM PostApprovals WHERE Post_ID = $postID "));

                // show disapprove if members has approved the post
                echo '<table class="postApprovalsAlign">';
                echo '<tr>';
                echo '<td>';
                echo "<div id = 'approvals$postID'>";

                // re-instantiate session and cookie variables to detect if user is logged in
                require 'getSession.php';


                if (mysql_num_rows($result2) > 0) {

                    echo '<form>';

                    echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';
                    echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
                    echo '<input type ="button" class = "btnDisapprove"'. $disabled.' />';

                    if ($approvals > 0) {


                        echo '&nbsp;' . $approvals;
                    }
                    echo '</form>';
                } else {
                    echo '<form>';

                    echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';
                    echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
                    echo '<input type ="button" class = "btnApprove"'. $disabled.' />';

                    if ($approvals > 0) {


                        echo '&nbsp;' . $approvals;
                    }
                    echo '</form>';
                }
                echo '</div>'; // end of approval div
                echo '</td></tr></table>';

                //-------------------------------------------------------------
                // End of approvals
                //-----------------------------------------------------------
                echo "</div>";


                if ($ID != $memberID) { ?>
                    <a class="messageEnvelope" href="/view_messages/<?php echo $username ?>"><img src="/images/messages.png" height="20" width="20" /></a>

                    <?php

                    if ($reposterID == $ID) { } else {
                        if (hasReposted($ID, $postID)) { $repostDisabled = "disabled"; } else { $repostDisabled = ''; }
                        ?>

                        <form class="repostAlign" style="margin-top:13px;" action="" method="post" onsubmit="return confirm('Are you sure you want to repost this?') && saveScrollPositionOnLinkClick(this)">
                            <input type="image" id="btnRepost" name="btnRepost" value="Repost" src="/images/repost_icon.png" style="margin-left:20px;" <?php echo $repostDisabled ?> />
                            <input type="hidden" id="memberID" name="memberID" value="<?php echo $memberID ?>" />
                            <input type="hidden" id="ownerID" name="ownerID" value="<?php echo $memberID ?>" />
                            <input type="hidden" id="postID" name="postID" value="<?php echo $postID ?>" />
                            <input type="hidden" id="postDate" name="postDate" value="<?php echo $postDate ?>" />
                            <input type="hidden" id="reposterID" name="reposterID" value="<?php echo $ID ?>" />
                            <input type="hidden" name="hashtag" id="hashtag" value ="<?php echo $hashtag ?>" />
                            <input type="hidden" name="scrollx" id="scrollx" value="0"/>
                            <input type="hidden" name="scrolly" id="scrolly" value="0"/>
                        </form>

                    <?php } }

                $optionsID = "options$prestinePostID";

                ?>

                <a href="javascript:showOptions('<?php echo $optionsID ?>');" class="blockLink">...</a>

                <?php

                $postPath = getPostPath();
                $shareLinkID = "shareLink$postID"; ?>
                <a class="shareLink" href="javascript:showLink('<?php echo $shareLinkID ?>');">
                    <img src="/images/share.png" class="shareImageAlign" height="25" width="25" />
                </a>

                <?php $shareLink = 'show_post?postID='.$postID.'&email=1';
                $shareLink = $postPath.$shareLink;
                $shortLink = shortenUrl($shareLink);

                ?>

                <hr class="hr-line" />

                <script>
                    function showCommentMentions(e, id) {


                        var commentMentionResult = "#commentMentionResult"+id;
                        var commentMention = "#commentMention"+id;

                        //alert(commentMention);

                        $(commentMention).on('keydown', function(){

                            var code = (e.keyCode ? e.keyCode : e.which);

                            // clear results if empty
                            if (!this.value.trim()) {

                                $(commentMentionResult).html('');
                                return;
                            }

                            if(code == '50'){
                                // match on last @mention
                                var lastMention = $(this).val().split(' ');
                                var lastType = lastMention[lastMention.length - 1];

                                var searchid = lastType;

                                var dataString = 'search='+ searchid + '&commentID='+commentMention;
                                $.ajax({
                                    type: "POST",
                                    url: "/getCommentMentions.php",
                                    data: dataString,
                                    cache: false,
                                    success: function(html)
                                    {

                                        $(commentMentionResult).html(html).show();
                                    }
                                });

                            }
                        });
                    }
                </script>

                <?php $commentMentionResult = "commentMentionResult$prestinePostID"; ?>
                <div id="<?php echo $commentMentionResult ?>"></div>

                <div class="content-space content-space-align">

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

                    <input id="<?php echo $shareLinkID ?>" style="display:none;" value ="<?php echo $shortLink ?>" />

                    <form style="width:100%" method="post" action="" enctype="multipart/form-data"
                          onsubmit="showCommentUploading('comment<?php echo $postID?>', this);">

                        <input type="file" style='z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' name="flCommentMedia[]" id="flCommentMedia" multiple onchange='$("#upload-photo-info").html($(this).val());' />

                        <?php $commentID = "$prestinePostID"; ?>
                        <textarea id="commentMention<?php echo $commentID ?>" onkeydown='showCommentMentions(event, <?php echo $commentID ?>)'  style="margin-top:10px;float:left;border:none;font-size:17px;" name="postComment" id="postComment" onkeyup="this.style.height='24px'; this.style.height = this.scrollHeight + 12 + 'px';"
                                  placeholder="Write a comment" title='' ></textarea>
                        <br/><br/>

                        <label style="float:left;clear:both" for="flCommentMedia">
                            <img src="/images/camera.png" style="height:25px;width:25px;float:left;margin-right:10px;" />
                        </label>
                        <input type="submit" name="btnComment" id="btnComment" class="btn btn-primary" style="float:left;" Value="Comment"  />


                        <input type="hidden" name="postID" id="postID" class="postID" Value="<?php echo $postID ?>"/>
                        <input type="hidden" name="ID" id="ID" class="ID" value="<?php echo $ID ?>"/>
                        <input type="hidden" name="memberID" id="memberID" class="memberID" value="<?php echo $memberID ?>"/>
                        <input type="hidden" name="hashtag" id="hashtag" class="hashtag" value="<?php echo $hashtag ?>"/>
                        <input type="hidden" name="scrollx" id="scrollx" value="0"/>
                        <input type="hidden" name="scrolly" id="scrolly" value="0"/>


                        <span style="clear:both;float:left;margin-top:10px;" class='label label-info' id="upload-photo-info"></span>

                        <br/><br/>

                        <div id="comment<?php echo $postID ?>" style="display:none;float:left;clear:both;">
                            <br/>
                            <div style="float:left;clear:both;" class="progress">
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
                        PostComments.CommentDate as CommentDate,
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
                        $blockCondition
                        And PostComments.IsDeleted = 0
                        Group By PostComments.ID
                        Order By PostComments.ID DESC LIMIT 3 ";
                    $result3 = mysql_query($sql3) or die(logError(mysql_error(), $url, "Getting first 3 comments"));
                    echo '<br/>';
                    if (mysql_num_rows($result3) > 0) {
                        echo '<div class="comment-style commentStyleAlign">';
                        while ($rows3 = mysql_fetch_assoc($result3)) {
                            $comment = $rows3['PostComment'];
                            $profilePhoto = $rows3['ProfilePhoto'];
                            $commentID = $rows3['PostCommentID'];
                            $commentOwnerID = $rows3['CommenterID'];
                            $commenterUsername = $rows3['Username'];
                            $commenterProfileUrl = "/$commenterUsername";
                            $commentDate = $rows3['CommentDate'];

                            echo '<div class="comment-row">';
                            echo '<div class="profileImageWrapper-Feed">
                        <a href='.$commenterProfileUrl.'>
                        <img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" class ="enlarge-onhover img-responsive" />
                        </a>
                        </div>

                         <div class="commentNameWrapper-Feed">
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
                                echo '<div class="comment-delete">';
                                echo '<form action="" method="post" onsubmit="return confirm(\'Do you really want to delete this comment?\')">';
                                echo '<input type="hidden" name="commentID" id="commentID" value="' .  $commentID . '" />';
                                echo '<input type="hidden" name="postID" id="postID" value="' .  $postID . '" />';
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
                        PostComments.CommentDate as CommentDate,
                        Members.ID As CommenterID,
                        Members.FirstName as FirstName,
                        Members.LastName As LastName,
                        Members.Username As Username,
                        Profile.ProfilePhoto As ProfilePhoto
                        FROM PostComments,Members, Profile
                        WHERE
                        PostComments.Post_ID = $postID
                        And Members.ID = PostComments.Member_ID
                        $blockCondition
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
                                    $commentDate = $rows4['CommentDate'];

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
                                        <div class="date"><?php echo date('l F j, Y',strtotime($commentDate)) ?></div>
                                        <?php echo nl2br($comment) ?>

                                    </div>
                                    <div class="comment-content" style="clear:both"></div>

                                    <!--DELETE BUTTON ------------------>
                                    <?php if ($commentOwnerID == $ID || $memberID == $ID) { ?>
                                        <div class="comment-delete">
                                            <form action="" method="post" onsubmit="return confirm(\'Do you really want to delete this comment?\')">
                                                <input type="hidden" name="commentID" id="commentID" value="' .  $commentID . '" />
                                                <input type ="image" name="DeleteComment" id="DeleteComment" value="Delete" src="/images/delete.png" style="height:20px;width:20px;margin-left:10px;" />
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



                    <!---------------------------------------------------
                                      End of comments div
                                      ----------------------------------------------------->

                </div>
            </div>


            <?php
            }
            }
            ?>


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
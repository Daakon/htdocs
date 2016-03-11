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
    $ownerId = $_POST['memberID'];
    $comment = $_POST['postComment'];
    $comment = mysql_real_escape_string($comment);

    if (strlen($comment) > 0) {
// find urls

        $comment = makeLinks($comment);

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
            // remove ALL SPECIAL CHARACTERS, Images paths are extremely sensitive
            $mediaName = str_replace('/[^A-Za-z0-9\-]/', '', $mediaName);
            // remove ALL WHITESPACE from image name
            $mediaName = preg_replace('/\s+/', '', $mediaName);
            // remove ampersand
            $mediaName = str_replace('&', '', $mediaName);
            $mediaName = trim(uniqid() . $mediaName);
            $mediaFile = $_FILES['flPostMedia']['tmp_name'];
            $type = trim($_FILES["flPostMedia"]["type"]);

            require 'media_post_file_path.php';

            if (in_array($type, $videoFileTypes)) {
                // do nothing here
                $mediaString = 'video';

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
                $cmd = "ffmpeg -i $mediaFile -vf 'transpose=1' $mediaFile";
                exec($cmd);
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

            imagedestroy($src);
            //imagedestroy($tmp);

            // determine which table to put photo pointer in
            // store media pointer
            $sql = "INSERT INTO Media (Member_ID,  MediaName,  MediaType,  MediaDate    ) Values
                                      ('$ID',    '$mediaName', '$type',   CURRENT_DATE())";
            mysql_query($sql) or die(logError(mysql_error(), $url, "Storing media name from comment in media table"));

            // get media ID
            $sqlGetMedia = "SELECT * FROM Media WHERE MediaName = '$mediaName'";
            $mediaResult = mysql_query($sqlGetMedia) or die(logError(mysql_error(), $url, "Getting media ID for link buildng"));
            $mediaRow = mysql_fetch_assoc($mediaResult);
            $mediaID = $mediaRow['ID'];
            $media = $mediaRow['MediaName'];
            $mediaType = $mediaRow['MediaType'];
            $mediaDate = $mediaRow['MediaDate'];


// check if file type is a photo
            if (in_array($type, $photoFileTypes)) {

                $img = '<img src = "' . $postMediaFilePath . '" />';
                $img = '<a href = "/media.php?id=' . $ID . '&mid=' . $mediaID . '&media=' . $media . '&type=' . $mediaType . '&mediaDate=' . $mediaDate . '">' . $img . '</a>';
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
                echo '<script>alert("Invalid File Type!");</script>';
                echo "<script>location= 'home.php'</script>";
                exit;
            }

            $comment = $comment . '<br/><br/>' . $img . '<br/>';

            $sql = "INSERT INTO PostComments (Post_ID,     Member_ID,   Comment  CommentDate ) Values
                                             ('$postID',   $ID',      '$comment', CURDATE() )";

            mysql_query($sql) or die(mysql_error());

// create post

            // get poster data
            $sqlPoster = "SELECT ID, FirstName, LastName, Gender FROM Members WHERE ID = '$ID' ";
            $resultPoster = mysql_query($sqlPoster) or die(logError(mysql_error(), $url, "Getting comment poster data"));
            $rowsPoster = mysql_fetch_assoc($resultPoster);
            $name = $rowsPoster['FirstName'] . ' ' . $rowsPoster['LastName'];
            $posterId = $rowsPoster['ID'];
            $gender = $rowsPoster['Gender'];
            $nameLink = $name;


// get post owner data

            $sql = "SELECT Member_ID FROM Posts WHERE ID = $postID";
            $result = mysql_query($sql) or die(mysql_error());
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
                $noun = "a status they posted";
            }
            else {

                $noun = $name2 . ' post.';
            }

            $post = "$nameLink posted a new $mediaString comment on previous $orgPost.<br/><br/>$img<br/>";
            $post = mysql_real_escape_string($post);

            $sqlInsertPost = "INSERT INTO Posts (Post,     Member_ID,    PostDate  ) Values
                                                ('$post', '$ID',        CURDATE() ) ";
            mysql_query($sqlInsertPost) or die(logError(mysql_error(), $url, "Inserting post triggered by comment"));
            $newPostID = mysql_insert_id();

// update new media with post id for commenting later

            $sql = "UPDATE Media SET Post_ID = '$newPostID' WHERE MediaName = '$mediaName' ";
            mysql_query($sql) or die(logError(mysql_error(), $url, "Update Media table with new post ID triggered from comment"));
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
    }
//------------------

//=========================================================================================================================//
//BELOW IS END OF POST COMMENT HANDLING CODE ==========================================================================//

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

<div class="container" style="margin-top: -20px;">


    <div class="row row-padding">

        <?php
        $email = $_GET['email'];
        if ($email == 1) {
            ?>
            <li><a href="/home" style="margin-left:10px;">Home</a></li>
        <?php }
        else if (isset($_SESSION['ID']) && isset($_COOKIE['Page'])) {
            ?>
            <li><button onclick="myFunction()" style="margin-left:10px;">Go Back</button></li>
        <?php
        }
        else { ?>
            <li><a href="javascript:history.back()" style="margin-left:10px;">Go Back</a></li>
        <?php }
        ?>


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
    $isSponsored = $rows['IsSponsored'];
    ?>

    <div class="row row-padding">
        <div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 roll-call"
              align="left">

            <div class="profileImageWrapper-Feed">
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

        <hr class="hr-line" />



                <?php if (isset($ID)) { ?>

                        <a href='/post-interest?interest=<?php echo urlencode($category) ?>' class='category'><span class="engageText">#<?php echo $category ?></span></a>

                    <?php if ($ID != $memberID) {?>
                            | <a href="/view_messages/<?php echo $username ?>"><span class="engageText"><img src = "/images/messages.png" height="20" width="20" /> Message </span> </a>
                            <?php } ?>

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
                  $shareLink = $postPath.$shareLink;
                  $shortLink = shortenUrl($shareLink);
            ?>
            <input id="<?php echo $shareLinkID ?>" style="display:none;" value ="<?php echo $shortLink ?>" />


            <hr class="hr-line" />



            <?php

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
            echo '<table class="margin-bottom-20">';
            echo '<tr>';
            echo '<td>';
            echo "<div id = 'approvals$postID'>";

            // re-instantiate session and cookie variables to detect if user is logged in
            require 'getSession.php';
            if (empty($ID) && !isset($ID)) {
                $readonly = 'readonly';
            }
            else {
                $readonly = '';
            }

            if (mysql_num_rows($result2) > 0) {

                echo '<form>';

                echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';
                echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
                echo '<input type ="button" class = "btnDisapprove"'. $readonly.' />';

                if ($approvals > 0) {


                    echo '&nbsp;' . $approvals;
                }
                echo '</form>';
            } else {
                echo '<form>';

                echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';
                echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
                echo '<input type ="button" class = "btnApprove"'. $readonly.' />';

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
            ?>




            <div class="content-space">

                <form method="post" action="" enctype="multipart/form-data"
                      onsubmit="showCommentUploading('comment<?php echo $postID?>', this);">

                    <input type="text" class="form-control" name="postComment" id="postComment"
                           placeholder="Write a comment" title='' <?php echo $readonly ?> />

                    <h6>Add Photo/Video</h6>
                    <input type="file" name="flPostMedia" id="flPostMedia" class="flPostMedia"/>

                    <br/>
                    <div id="comment<?php echo $postID ?>" style="display:none;">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" >
                                <b>File uploading...please wait</b>
                            </div>
                        </div>
                    </div>

                    <input type="submit" name="btnComment" id="btnComment" Value="Comment" <?php echo $readonly ?> />
                    <input type="hidden" name="postID" id="postID" Value="<?php echo $postID ?>"/>
                    <input type="hidden" name="ID" id="ID" value="<?php echo $ID ?>"/>
                    <input type="hidden" name="ownerId" id="ownerId" value="<?php echo $MemberID ?>"/>
                    <input type="hidden" name="scrollx" id="scrollx" value="0"/>
                    <input type="hidden" name="scrolly" id="scrolly" value="0"/>

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
                    echo '<div class="comment-style">';
                    while ($rows3 = mysql_fetch_assoc($result3)) {
                        $comment = $rows3['PostComment'];
                        $profilePhoto = $rows3['ProfilePhoto'];
                        $commentID = $rows3['PostCommentID'];
                        $commentOwnerID = $rows3['CommenterID'];
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
                         </a><br/>
                         ' . nl2br($comment) . '
                          <div class="date">'. date('l F j, Y',strtotime($commentDate)) .'</div>
                        </div>

                    <div class="comment-content" style="clear:both"></div>';
                        echo '</div>';

                        if ($commentOwnerID == $ID || $ID == $memberID) {
                            //<!--DELETE BUTTON ------------------>
                            echo '<div class="comment-delete">';
                            echo '<form action="" method="post" onsubmit="return confirm(\'Do you really want to delete this comment?\')">';
                            echo '<input type="hidden" name="commentID" id="commentID" value="' .  $commentID . '" />';
                            echo '<input type="hidden" name="postID" id="postID" value="' .  $postID . '" />';
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
                        PostComments.CommentDate as CommentDate,
                        Members.ID As CommenterID,
                        Members.FirstName as FirstName,
                        Members.LastName As LastName,
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
                                    </a><br/>
                                    <?php echo nl2br($comment) ?>
                                    <div class="date"><?php echo date('l F j, Y',strtotime($commentDate)) ?></div>
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

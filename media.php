<?php

require 'imports.php';

$ID = $_SESSION['ID'];

$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$back = 1;



//-------------------------------------------------
// handle post comments
//-------------------------------------------------
if (isset($_POST['btnComment']) && ($_POST['btnComment'] == "Comment")) {

    $mediaID = $_POST['mediaID'];
    $ownerID = $_POST['ownerID'];
    $comment = $_POST['mediaComment'];
    $comment = mysql_real_escape_string($comment);
    if (strlen($comment) > 0) {
// find urls
        $comment = makeLinks($comment);
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
                // add unique id to image name to make it unique and add it to the file server
                $mediaName = $_FILES["flPostMedia"]["name"];
                $fileName = pathinfo($mediaName, PATHINFO_FILENAME);
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
                    $ffmpeg = '/usr/bin/ffmpeg';
                    exec("$ffmpeg -i $newFileName -vcodec libx264 -pix_fmt yuv420p -profile:v baseline -preset slow -crf 22 -movflags +faststart $newFileName");
                    $mediaName = $newFileName;
                } else {
                    $mediaString = 'photo';
                    if ($type == "image/jpg" || $type == "image/jpeg") {
                        $src = imagecreatefromjpeg($mediaFile);
                    } else if ($type == "image/png") {
                        $src = imagecreatefrompng($mediaFile);
                    } else if ($type == "image/gif") {
                        $src = imagecreatefromgif($mediaFile);
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
                    mysql_query($sql) or die(mysql_error());
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
                        $img = '<a href = "media.php?id=' . $ID . '&mid=' . $mediaID . '&mediaName=' . $media . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '">' . $img . '</a>';
                    } // check if file type is a video
                    elseif (in_array($type, $videoFileTypes)) {
                        // where ffmpeg is located
                        $ffmpeg = '/usr/bin/ffmpeg';
                        // poster file name
                        $posterName = "poster".uniqid().".jpg";
                        //where to save the image
                        $poster = "$posterPath$posterName";
                        //time to take screenshot at
                        $interval = 5;
                        //screenshot size
                        //$size = '440x280'; -s $size
                        //ffmpeg command
                        $cmd = "$ffmpeg -i \"$postMediaFilePath\" -r 1 -ss 5 -t 1  -f image2 $poster 2>&1";
                        exec($cmd);

                        $img = '<video poster="/poster/'.$posterName.'" preload="none" controls>
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

                    // re-declare variable
                    $mediaID = $_POST['mediaID'];
                    $sql = "INSERT INTO MediaComments (Media_ID,     Member_ID,   Comment  ) Values
                                                      ('$mediaID', '$ID',      '$comment')";
                    mysql_query($sql) or die(mysql_error());
                }
            }
//----------------------
// if not comment photo
//----------------------
            else {
                $sql = "INSERT INTO MediaComments (Media_ID,  Member_ID,    Comment ) Values
                                                  ('$mediaID', '$ID',      '$comment')";
                mysql_query($sql) or die(mysql_error());
            }
            $scrollx = $_REQUEST['scrollx'];
            $scrolly = $_REQUEST['scrolly'];
//A comment was just made, we need to send out some notifications.
//The first thing is to identify all of the id's connected with this post
            $user_id = $_SESSION['ID'];
//Get the ids of all the members connected with a post comment
            $sql = "SELECT Member_ID FROM MediaComments WHERE Media_ID = $mediaID And Member_ID != $ID ";
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
                    if ($item != $ID) {
                        // only send email if account & email active
                        if (checkActive($item)) {
                            if (checkEmailActive($item)) {
                                build_and_send_email($user_id, $item, 13, $mediaID);
                            }
                        }
                    }
                }
            }

//------------------
//=========================================================================================================================//
//BELOW IS END OF POST COMMENT HANDLING CODE ==========================================================================//

    }
    $ownerID = $_SESSION['MediaMemberID'];
    $mediaName = $_SESSION['MediaName'];
    $mediaType = $_SESSION['MediaType'];
    $mediaDate = $_SESSION['MediaDate'];
    $mediaID = $_SESSION['MediaID'];
    echo "<script>location='/media.php?id=$ownerID&mid=$mediaID&mediaName=$mediaName&mediaType=$mediaType&mediaDate=$mediaDate&scrollx=$scrollx&scrolly=$scrolly'</script>";

}

// handle photo delete
if (!empty($_GET['btnDelete']) && ($_GET['btnDelete'] == 'Delete Image')) {
    $mediaID = $_GET['mediaID'];
    $sql = "UPDATE Media Set IsDeleted = 1 WHERE ID = '$mediaID' And Member_ID = '$ID' ";

    mysql_query($sql) or die(mysql_error());
    $username = get_username($ID);
    echo "<script>location = '/member_media/$username'</script>";

}
?>


<?php
/***********************************
 * Start to build photo page here
 * *********************************/


$mediaName = $_GET['mediaName'];
$_SESSION['MediaName'] = $mediaName;

$mediaType = $_GET['mediaType'];
$_SESSION['MediaType'] = $mediaType;

$mediaDate = $_GET['mediaDate'];
$_SESSION['MediaDate'] = $mediaDate;

$mediaID = $_GET['mid'];
$_SESSION['MediaID'] = $mediaID;

$memberID = $_GET['id'];
$_SESSION['MediaMemberID'] = $memberID;

// if a postback, the entire file path is already constructed

$mediaFilePath = trim("media/" . $mediaName);

if (!file_exists($mediaFilePath)) {
    echo "<script>alert('This media was not found'); history.back()</script>";
    exit;
}

if (!empty($mediaFilePath)){
if (file_exists($mediaFilePath)) {
// check if file type is a video
$videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
    "video/quicktime", "video/webm", "video/x - matroska",
    "video/x - ms - wmw");



if (in_array($mediaType, $videoFileTypes)) {

    $img = '<a href = "' . $videoPath . $mediaName . '"><img src = "' . $images . 'video-bg.jpg" height="100" width = "100" /></a>';

}

?>

<?php

$sql = "SELECT * FROM Members,Profile
WHERE Members.ID = $memberID
And Profile.Member_ID = $memberID ";
$result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting profile data from media query string"));
$pRows = mysql_fetch_assoc($result);
$profilePhoto = $pRows['ProfilePhoto'];
$name = $pRows['FirstName'] . ' ' . $pRows['LastName'];

?>

<?php
$profileMediaSrc = trim("/media/" . $profilePhoto);
?>


<?php get_head_files() ?>



<script type="text/javascript">
        function confirmPhotoDelete(theForm) {
            if (!confirm('Are you sure you want to delete this photo')) {
                return false;
            }

            return true;
        }
</script>

<script type="text/javascript">

    function showComments(id) {
        var e = document.getElementById('moreComments');
        if (e.style.display == 'none') {
            e.style.display = 'block';
        }
        else
            e.style.display = 'none';
    }
</script>

<script>
    $(document).ready(function () {
        $("body").delegate(".btnApprove", "click", function () {
            var parentDiv = $(this).closest("div[id^=approvals]");
            var data = {
                postID: $(this).closest('tr').find('.postID').val(),
                ID: $(this).closest('tr').find('.ID').val(),
                mediaID: $(this).closest('tr').find('.mediaID').val(),
                mediaName: $(this).closest('tr').find('.mediaName').val(),
                mediaType: $(this).closest('tr').find('.mediaType').val(),
                mediaDate: $(this).closest('tr').find('.mediaDate').val()
                //add other properties similarly
            }

            $.ajax({
                type: "post",
                url: "/media_approve.php",
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
            var currentDiv = $(this).closest("div[id^=approvals]");
            var data = {
                postID: $(this).closest('tr').find('.postID').val(),
                ID: $(this).closest('tr').find('.ID').val(),
                mediaID: $(this).closest('tr').find('.mediaID').val(),
                mediaName: $(this).closest('tr').find('.mediaName').val(),
                mediaType: $(this).closest('tr').find('.mediaType').val(),
                mediaDate: $(this).closest('tr').find('.mediaDate').val()

                //add other properties similarly
            }

            $.ajax({
                type: "post",
                url: "/media_disapprove.php",
                data: data,
                success: function (data) {
                    currentDiv.html(data);
                }

            })
        });
    });
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
    // show comment uploading
    function showCommentUploading(comment, theForm) {
        document.getElementById(comment).style.display = "block";
        saveScrollPositions(theForm);
    }
</script>


<script>
    // for android video playback
    var video = document.getElementById('video');
    video.addEventListener('click',function(){
        video.play();
    },false);
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

<?php include('media_sizes.html'); ?>

<body class="media-bg">

<div class="container media-body">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">


            <?php
            // check if file type is a photo
            $videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
                "video/quicktime", "video/webm", "video/x-matroska",
                "video/x-ms-wmw");
            $photoFileTypes = array("image/jpg", "image/jpeg", "image/png", "image/tiff",
                "image/gif", "image/raw");
            $audioFileTypes = array("audio/wav", "audio/mp3");

            // get post ID from NON key PostID field
            $sqlGetPostID = "SELECT PostID FROM Media WHERE ID = '$mediaID' ";
            $resultGetPostID = mysql_query($sqlGetPostID) or die(logError(mysql_error(), $url, "Getting PostID from non foreign key PostID field"));
            $rowPostID = mysql_fetch_assoc($resultGetPostID);
            $postID = $rowPostID['PostID'];

            /*$sqlPost = "SELECT Post FROM Posts WHERE ID = '$postID' ";
            $resultPost = mysql_query($sqlPost) or die(mysql_error());
            $rowPost = mysql_fetch_assoc($resultPost);
            // remove image from original body
            $post = preg_replace(" /<img[^>]+\> / i", "", $rowPost['Post']);
            $post = preg_replace(" /<video[^>]+\> / i", "", $post);
            $post = "<p>" . $post . "</p>"; */

            $isPost = true;

            // if the

                $sqlPost = "SELECT MediaName, MediaType, Poster, AudioName FROM Media WHERE MediaName = '$mediaName' ";
                $resultPost = mysql_query($sqlPost) or die(logError(mysql_error(), $url, "Getting all media data from query string"));
                $rowPost = mysql_fetch_assoc($resultPost);
                $mediaName = $rowPost['MediaName'];
                $mediaType = $rowPost['MediaType'];
                $posterName = $rowPost['Poster'];
                $audioName = $rowPost['AudioName'];

                if (in_array($mediaType, $photoFileTypes)) {

                    $post = '<img src = "' . $mediaPath . $mediaName . '" class="img-responsive"/>';
                }

                if (in_array($mediaType, $videoFileTypes)) {

                    $post = '<video src = "' . $videoPath . $mediaName . '" poster="/poster/'.$posterName.'" preload="auto" controls />';
                }


            ?>



            <?php echo nl2br($post); ?>

        </div>


        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">

            <img src="/media/<?php echo $profilePhoto ?>" height="100px" width="80px"/>

            <?php echo $name; ?><br/>
            <?php echo date("F j, Y", strtotime($mediaDate)) ?>

            <hr/>
            <?php
            $history = $_GET['h'];
            if ($history=='0') { ?>

                <a href="/home">Home</a>
                <br/><br/><br/>

            <?php } else {

                $isPhotoAlbumOpen = $_GET['photoOpen'];
                if ($isPhotoAlbumOpen == 'true') {
                    $_SESSION['PhotoAlbumOpen'] = true;
                }

                $isVideoAlbumOpen = $_GET['videoOpen'];
                if ($isVideoAlbumOpen == 'true') {
                    $_SESSION['VideoAlbumOpen'] = true;
                }
                ?>

                <a href="javascript:history.go(- <?php echo $back ?>)">Back</a>
                <br/><br/><br/>

                <?php
            }


            // check if user has approved this post

            if ($isPost == true) {

                $sql2 = "SELECT * FROM MediaApprovals WHERE Media_ID = '$mediaID' AND Member_ID = '$ID' ";
                $result2 = mysql_query($sql2) or die(logError(mysql_error(), $url, "Getting member media approvals"));
                $rows2 = mysql_fetch_assoc($result2);

                // get approvals for each bulletin
                $sql3 = "SELECT * FROM MediaApprovals WHERE Media_ID = '$mediaID' ";
                $result3 = mysql_query($sql3) or die(logError(mysql_error(), $url, "Getting all members media approvals"));
                $rows3 = mysql_fetch_assoc($result3);
                $approvals = mysql_num_rows($result3);

                echo '<table><tr>';
                echo "<div id = 'approvals$postID' style='display:inline'>";

                if (mysql_num_rows($result2) > 0) {
                    echo '<td>';
                    echo '<form>';
                    echo '<input type ="hidden" class = "ID" value="' . $ID . '"/>';
                    echo '<input type ="hidden" class = "mediaID" value = "' . $mediaID . '" />';
                    echo '<input type ="hidden" class = "mediaName" value ="' . $mediaName . '" />';
                    echo '<input type ="hidden" class = "mediaType" value = "' . $mediaType . '" />';
                    echo '<input type ="hidden" class = "mediaDate" id = "mediaDate" value = "' . $mediaDate . '" />';
                    echo '<input type ="button" class = "btnDisapprove" />';

                    echo '</form>';
                    echo '</td>';

                    if ($approvals > 0) {
                        //echo '<tr><td>';
                        echo '<td>';
                        echo $approvals;
                        echo '</td>';
                    }

                } else {

                    echo '<td>';
                    echo '<form>';
                    echo '<span style="display: inline;">';
                    echo '<input type ="hidden" class = "ID" value="' . $ID . '"/>';
                    echo '<input type ="hidden" class = "mediaID" value = "' . $mediaID . '" />';
                    echo '<input type ="hidden" class = "mediaName" id = "mediaName" value ="' . $mediaName . '" />';
                    echo '<input type ="hidden" class = "mediaType" id = "type" value = "' . $mediaType . '" />';
                    echo '<input type ="hidden" class = "mediaDate" id = "mediaDate" value = "' . $mediaDate . '" />';
                    echo '<input type ="button" class = "btnApprove" />';
                    echo '</td>';

                    if ($approvals > 0) {

                        echo '<td>';
                        echo $approvals;
                        echo '</td>';
                    }
                    echo '</form>';
                }
                echo "</div>"; // end of approval div
                echo '</tr></table>';
?>

            <div class="content-space">
            <?php if (isset($ID)) { ?>
                <form method="post" action="" enctype="multipart/form-data"
                      onsubmit="saveScrollPositions(this);">

                    <input type="text" class="form-control" name="mediaComment" id="mediaComment"
                           placeholder="Write a comment" title='' class="border"/>

                    <h6>Attach A Photo/Video To Your Comment</h6>
                    <input type="file" name="flPostMedia" id="flPostMedia" class="flPostMedia"/>

                    <br/>
                    <div id="comment<?php echo $postID ?>" style="display:none;">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" >
                                <b>File uploading...please wait</b>
                            </div>
                        </div>
                    </div>
                    <input type="submit" name="btnComment" id="btnComment" Value="Comment"
                           style="border:1px solid black"/>
                    <input type="hidden" name="mediaID" id="mediaID" Value="<?php echo $mediaID ?>"/>
                    <input type="hidden" name="ID" id="ID" value="<?php echo $ID ?>"/>
                    <input type="hidden" name="ownerID" id="ownerID" value="<?php echo $memberID ?>"/>
                    <input type="hidden" name="scrollx" id="scrollx" value="0"/>
                    <input type="hidden" name="scrolly" id="scrolly" value="0"/>
                </form>

                <br/>
            <?php } ?>

            <?php
            $sql3 = "SELECT DISTINCT
                        MediaComments.Comment As MediaComment,
                        MediaComments.ID As MediaCommentID,
                        Members.ID As CommenterID,
                        Members.FirstName as FirstName,
                        Members.LastName As LastName,
                        Members.Username As Username,
                        Profile.ProfilePhoto As ProfilePhoto
                        FROM MediaComments,Members, Profile
                        WHERE
                        MediaComments.Media_ID = $mediaID
                        AND Members.ID = Profile.Member_ID
                        And Members.ID = MediaComments.Member_ID
                        And MediaComments.IsDeleted = 0
                        Group By MediaComments.ID
                        Order By MediaComments.ID DESC LIMIT 3 ";
            $result3 = mysql_query($sql3) or die(logError(mysql_error(), $url, "Getting media comments 0-3"));
            echo '<br/>';
            if (mysql_num_rows($result3) > 0) {
                echo '<div class="comment-style">';
                while ($rows3 = mysql_fetch_assoc($result3)) {
                    $comment = $rows3['MediaComment'];
                    $profilePhoto = $rows3['ProfilePhoto'];
                    $commentID = $rows3['MediaCommentID'];
                    $commentOwnerID = $rows3['CommenterID'];
                    $commenterUsername = $rows3['Username'];
                    $commenterProfileUrl = "/$commenterUsername";

                    echo '<div class="comment-row">';
                    echo '<div class="profileImageWrapper-Feed">
                     <a href='.$commenterProfileUrl.'>
                    <img src = "' . $mediaPath . $profilePhoto . '" class ="enlarge-onhover img-responsive" />
                    </a>
                    </div>

                    <div class="commentNameWrapper-Feed" style="padding-left:5px;">
                     <a href='.$commenterProfileUrl.'>
                    <div class="profileName-Feed">'
                        . $rows3['FirstName'] . ' ' . $rows3['LastName'] . '
                        </div>
                        </a><br/>'
                        .nl2br($comment) . '
                        </div>

                    <div class="comment-content" style="clear:both"></div>
                    </div>';


                    if ($commentOwnerID == $ID) {
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
                        MediaComments.Comment As MediaComment,
                        MediaComments.ID As MediaCommentID,
                        Members.ID As CommenterID,
                        Members.FirstName as FirstName,
                        Members.LastName As LastName,
                        Profile.ProfilePhoto As ProfilePhoto
                        FROM MediaComments,Members, Profile
                        WHERE
                        MediaComments.Media_ID = $mediaID
                        And Members.ID = MediaComments.Member_ID
                        And MediaComments.IsDeleted = 0
                        And Members.ID = Profile.Member_ID
                        Group By MediaComments.ID
                        Order By MediaComments.ID DESC LIMIT 3, 100 ";

            $result4 = mysql_query($sql4) or die(logError(mysql_error(), $url, "Getting media comments 3-100"));

            $moreComments = "moreComments$postID";

            if (mysql_num_rows($result4) > 0) {
            ?>

            <a href="javascript:showComments('<?php echo $moreComments ?>');">Show More</a>

            <div id="<?php echo $moreComments ?>" style="display:none;">

                <div class="comment-style">

                <?php
                while ($rows4 = mysql_fetch_assoc($result4)) {
                    $comment = $rows4['MediaComment'];
                    $profilePhoto = $rows4['ProfilePhoto'];
                    $commentID = $rows4['MediaCommentID'];
                    $commentOwnerID = $rows4['CommenterID'];
                    $commenterUsername = $rows4['Username'];
                    $commenterProfileUrl = "/$commenterUsername";
                ?>

                <div class="comment-row">
                <div class="profileImageWrapper-Feed">
                    <a href='<?php echo $commenterProfileUrl ?>'>
                        <img src = "<?php echo $mediaPath . $profilePhoto ?>" />
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
        </div>
    </div>
            <!---------------------------------------------------
                              End of comments div
                              ----------------------------------------------------->

            <?php
            }



            if ($ID == $memberID) {
            ?>
            <br/><br/>
            <form method="get" action="" onsubmit="return confirm('Are you sure you want to delete this media')">
                <?php
                echo '<input type = "submit" name = "btnDelete" id = "btnDelete" value = "Delete Image" class="deleteButton" /><br/><br/>';
                echo '<input type ="hidden" name = "mediaID" id = "mediaID" value = "' . $mediaID . '" />';
                echo '<input type = "hidden" name = "ID" id = "ID" value="' . $ID . '"/>';
                echo '</form>';
                echo '<hr/>';
                }


                if (empty($profileMediaSrc)) {
                    echo "<script>alert('Image not found');location='home.php'</script>";
                }

                }
                }
                ?>


        </div>
    </div>

</body>

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
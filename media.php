<?php

require 'connect.php';
require 'getSession.php';
require 'mediaPath.php';
require 'model_functions.php';
require 'memory_settings.php';
require 'html_functions.php';
require 'email.php';
require 'findURL.php';

$ID = $_SESSION['ID'];

$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$back = 1;


// handle photo delete
if (!empty($_GET['btnDelete']) && ($_GET['btnDelete'] == 'Delete')) {
    $mediaID = $_GET['mediaID'];
    $sql = "UPDATE Media Set IsDeleted = 1 WHERE ID = '$mediaID' And Member_ID = '$ID' ";

    mysql_query($sql) or die(mysql_error());

    echo "<script>location = 'member_media.php'</script>";

}
?>



<?php
// handle photo comments
if (isset($_POST['btnComment']) && ($_POST['btnComment'] == "Comment")) {
    $postID = $_POST['postID'];
    $photo = $_POST['photo'];
    $type = $_POST['type'];

    $mediaID = $_POST['ID'];
    $mediaDate = $_POST['MediaDate'];

    if ($back == 1) {
        $back = ++$back;
    }


    $comment = mysql_real_escape_string($_POST['postComment']);
$comment = makeLinks($comment);


// if photo is provided
        if (isset($_FILES['flPostMedia']) && strlen($_FILES['flPostMedia']['name']) > 1) {


// check file size
            if ($_FILES['flPostMedia']['size'] > 50000000) {
                echo '<script>alert("File is too large. The maximum file size is 50MB.");</script>';
                exit;
            }

// check if file type is a video
            $videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
                "video/quicktime", "video/webm", "video/x-matroska",
                "video/x-ms-wmw");


// add unique id to image name to make it unique and add it to the file server
            $mediaName = $_FILES["flPostMedia"]["name"];
            $mediaName = trim(uniqid() . $mediaName);
            $mediaFile = $_FILES['flPostMedia']['tmp_name'];
            $type = trim($_FILES["flPostMedia"]["type"]);


            require 'media_post_file_path.php';
// create file instance
            if (in_array($type, $videoFileTypes)) {
                // convert to mp4
                $fileName = pathinfo($mediaName, PATHINFO_FILENAME);
                $newFileName = $fileName.".mp4";
                exec("ffmpeg -i $fileName -vcodec libx264 -pix_fmt yuv420p -profile:v baseline -preset slow -crf 22 -movflags +faststart $newFileName");
                $mediaName = $newFileName;
            }
             else {
                    echo "<script>alert('Invalid File Type'); ";
                    exit;
                }


// save photo/video
            require 'media_post_file_path.php';
            if (in_array($type, $videoFileTypes)) {
                move_uploaded_file($mediaFile, $postMediaFilePath);
            }

            $sql2 = "INSERT INTO Media (Member_ID,  MediaName,   MediaType,   MediaDate     ) Values
                                      ('$ID',      '$mediaName', '$type',     CURRENT_DATE())";
            mysql_query($sql2) or die(mysql_error());

// get photo id
            $sqlGetPhoto = "SELECT * FROM Media WHERE MediaName = '$mediaName'";
            $photoResult = mysql_query($sqlGetPhoto) or die(mysql_error());
            $photoRow = mysql_fetch_assoc($photoResult);
            $newPhotoId = $photoRow['ID'];
            $newPhoto = $photoRow['MediaName'];
            $newPhotoType = $photoRow['MediaType'];
            $newPhotoDate = $photoRow['MediaDate'];

 // check if file type is a video
            if (in_array($type, $videoFileTypes)) {
                $img = '<a href = "' . $videoPath . $mediaName . '"><img src = "' . $images . 'video-bg.jpg" height="100" width = "100" /></a>';
            }

            $comment = $comment . '<br/><br/>' . $img . '<br/>';

            $sql = "INSERT INTO PostComments(Post_ID, Member_ID,   Comment) Values
                                        ('$postID', '$ID',    '$comment')";

            mysql_query($sql) or die(mysql_error());


// create post

            // get poster data
            $sqlPoster = "SELECT ID, FirstName, LastName, Gender FROM Members WHERE ID = '$ID' ";
            $resultPoster = mysql_query($sqlPoster) or die(mysql_error());
            $rowsPoster = mysql_fetch_assoc($resultPoster);
            $name = $rowsPoster['FirstName'] . ' ' . $rowsPoster['LastName'];
            $posterId = $rowsPoster['ID'];
            $gender = $rowsPoster['Gender'];
            $nameLink = $name;


// get photo owner data

            $sqlOwner = "SELECT ID, FirstName, LastName FROM Members WHERE ID = '$ownerId' ";
            $resultOwner = mysql_query($sqlOwner) or die(mysql_error());
            $rowsOwner = mysql_fetch_assoc($resultOwner);
            $name2 = $rowsOwner['FirstName'] . ' ' . $rowsOwner['LastName'];
            $name2 = $name2;
            $name2Link = $name2;

            // determine noun if profile owner commented on their own post and write bulletin

            if ($gender == 1) {
                $noun = 'his';
            } else {
                $noun = 'her';
            }

            $post = "$nameLink posted a new $mediaString comment on $noun post .<br /><br />$img<br />";
            $post = mysql_real_escape_string($post);

            $sqlInsertPost = "INSERT INTO Posts(Post, Member_ID, PostDate) Values
('$post', '$ID', CURDATE()) ";
            mysql_query($sqlInsertPost) or die(mysql_error());
            $newPostID = mysql_insert_id();

// update new photo with bulletin id for commenting later

            $sql = "UPDATE Media SET Post_ID = '$newPostID' WHERE MediaName = '$mediaName' ";
            mysql_query($sql) or die(mysql_error());

        }

//----------------------
// if not comment photo
//----------------------

        else {

            $sql = "INSERT INTO PostComments(Post_ID, Member_ID, Comment) Values
                                        ('$postID', '$ID', '$comment')";
            mysql_query($sql) or die(mysql_error());
        }


        $scrollx = $_REQUEST['scrollx'];
        $scrolly = $_REQUEST['scrolly'];
//Comment  just got inserted lets notify the owner of the bulletin
//A comment was just made, we need to send out some notifications.
//The first thing is to identify all of the id's connected with this bulletin
        $user_id = $ID;

        $postID = $postID;


//Get the ids of all the consumers connected with a bulletin comment
        $sql = "SELECT Member_ID FROM PostComments WHERE ID = $postID ";

        $result = mysql_query($sql) or die(mysql_error());

        $consumer_comment_ids = array();

//Iterate over the results and sort out the biz ids from the consumer ones.
        while ($rows = mysql_fetch_assoc($result)) {
            array_push($consumer_comment_ids, $rows['Member_ID']);
        }

//Boil the id's down to unique values because we dont want to send double emails or notifications
        $comment_ids = array_unique($consumer_comment_ids);

//Send consumer notifications


        foreach ($comment_ids as $item) {
            if (strlen($item) > 0) {
                // only send email if account & email active
                if (checkActive($item)) {
                    if (checkEmailActive($item)) {
                        build_and_send_email($item, $user_id, 6, $postID);
                    }
                }
            }
        }


//Notify the post creator

        $sql = "SELECT ID FROM Posts WHERE ID = '$postID'";

        $result = mysql_query($sql) or die(mysql_error());
        $rows = mysql_fetch_assoc($result);


        if (checkEmailActive($ID)) {
            build_and_send_email($ID, $user_id, 6, $postID, '');
        }

//=========================================================================================================================//
//BELOW IS END OF POST COMMENT HANDLING CODE ==========================================================================//
    echo "<script>location='/media.php?scrollx=$scrollx&scrolly=$scrolly'</script>";
}


/***********************************
 * Start to build photo page here
 * *********************************/


$mediaName = $_GET['mediaName'];
$mediaType = $_GET['mediaType'];
$mediaDate = $_GET['mediaDate'];
$mediaID = $_GET['mid'];
$memberID = $_GET['id'];

// if a postback, the entire file path is already constructed

$mediaFilePath = trim("media/" . $mediaName);


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
WHERE Members.ID = '$memberID'
And Profile.Member_ID = '$memberID' ";
$result = mysql_query($sql) or die(mysql_error());
$pRows = mysql_fetch_assoc($result);
$profilePhoto = $pRows['ProfilePhoto'];
$name = $pRows['FirstName'] . ' ' . $pRows['LastName'];

?>

<?php
$profileMediaSrc = trim("media/" . $profilePhoto);
?>


<?php get_head_files() ?>



<script type="text / javascript">
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

<style>
    html, body {
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    iframe {
        max-width: 100%;
        height: auto;
    }

    img {
        max-width: 100%;
        max-height: 500px;
    }

    video {
        max-width: 100%;
        max-height: 500px;
    }

    embed {
        max-width: 100%;
        max-height: 500px;
    }

    script {
        max-width: 100%;
        max-height: 500px;
    }

    .btnApprove {
        background: url("/images/gray_check.png");
        display: inline-block;
        background-repeat: no-repeat;
        background-color: white;
        background-position: center;
        width: 30px;
        height: 30px;
        border: none;
    }

    .btnDisapprove {
        background: url("/images/red_check.png");
        display: inline-block;
        background-repeat: no-repeat;
        background-color: transparent;
        background-position: center;
        width: 30px;
        height: 30px;
        border: none;
    }

    h1, .h1 {
        font-size: 20px;
        word-wrap: normal;
    }

    h1 a {
        word-break: break-all; /*breaks hyperlinks */
    }

    h2, .h2 {
        font-size: 18px;
        word-wrap: normal;
    }

    h2 a {
        word-break: break-all; /*breaks hyperlinks */
    }

    h3, .h3 {
        font-size: 16px;
        word-wrap: normal;
    }

    h3 a {
        word-break: break-all; /*breaks hyperlinks */
    }

    h4, .h4 {
        font-size: 15px;
        word-wrap: normal;
    }

    h4 a {
        word-break: break-all; /*breaks hyperlinks */
    }

    h5, .h5 {
        font-size: 14px;
        padding-bottom: .4666666666666666em;
        word-wrap: normal;
    }

    h5 a {
        word-break: break-all; /*breaks hyperlinks */
    }

    h6, .h6 {
        font-size: 13px;
        padding-bottom: .4666666666666666em;
        text-transform: uppercase;
        color: #888;
        word-wrap: normal;
    }

    h6 a {
        word-break: break-all; /*breaks hyperlinks */
    }
</style>

<script>
    // for android video playback
    var video = document.getElementById('video');
    video.addEventListener('click',function(){
        video.play();
    },false);
</script>

<?php include('media_sizes.html'); ?>

<body style="background:black;">

<div class="container" style="background:white;margin-top:10px;padding:10px;">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">


            <?php


            $sqlGetPostID = "SELECT Post_ID FROM Media WHERE ID = '$mediaID' ";
            $resultGetPostID = mysql_query($sqlGetPostID) or die(mysql_error());
            $rowPostID = mysql_fetch_assoc($resultGetPostID);
            $postID = $rowPostID['Post_ID'];

            $sqlPost = "SELECT Post FROM Posts WHERE ID = '$postID' ";
            $resultPost = mysql_query($sqlPost) or die(mysql_error());
            $rowPost = mysql_fetch_assoc($resultPost);
            // remove image from original body
            $post = preg_replace(" /<img[^>]+\> / i", "", $rowPost['Post']);
            $post = preg_replace(" /<video[^>]+\> / i", "", $post);
            $post = "<p>" . $post . "</p>";

            $isPost = true;

            // if the
            if ($post == "<p></p>") {
                $isPost = false;
                $sqlPost = "SELECT MediaName, MediaType, Poster, AudioName FROM Media WHERE MediaName = '$mediaName' ";
                $resultPost = mysql_query($sqlPost) or die(mysql_error());
                $rowPost = mysql_fetch_assoc($resultPost);
                $mediaName = $rowPost['MediaName'];
                $mediaType = $rowPost['MediaType'];
                $posterName = $rowPost['Poster'];
                $audioName = $rowPost['AudioName'];


                if (in_array($mediaType, $videoFileTypes)) {

                    $post = '<video src = "' . $videoPath . $mediaName . '" poster="/poster/'.$posterName.'" preload="auto" controls />';
                }
            }
            ?>



            <?php echo nl2br($post); ?>

        </div>


        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">

            <img src=" <?php echo $profileMediaSrc ?>" height="100px" width="100px"/>

            <?php echo $name; ?><br/>
            <?php echo date("F j, Y", strtotime($mediaDate)) ?>

            <hr/>
            <a href="javascript:history.go(- <?php echo $back ?>)">Back</a>
            <br/><br/><br/>

            <?php


            // check if user has approved this post

            if ($isPost == true) {

            $sql2 = "SELECT * FROM PostApprovals WHERE Post_ID = '$postID' AND Member_ID = '$ID' ";
            $result2 = mysql_query($sql2) or die(mysql_error());
            $rows2 = mysql_fetch_assoc($result2);

            // get approvals for each bulletin
            $sql3 = "SELECT * FROM PostApprovals WHERE Post_ID = '$postID' ";
            $result3 = mysql_query($sql3) or die(mysql_error());
            $rows3 = mysql_fetch_assoc($result3);
            $approvals = mysql_numrows($result3);

            echo '<table><tr><td>';
            echo "<div id = 'approvals$postID'>";

            if (mysql_num_rows($result2) > 0) {

                echo '<form>';

                echo '<input type ="hidden" class = "postID" value = "' . $postID . '" />';
                echo '<input type ="hidden" class = "ID" value="' . $ID . '"/>';
                echo '<input type ="hidden" class = "mediaID" value = "' . $mediaID . '" />';
                echo '<input type ="hidden" class = "mediaName" value ="' . $mediaName . '" />';
                echo '<input type ="hidden" class = "mediaType" value = "' . $mediaType . '" />';
                echo '<input type ="hidden" class = "mediaDate" id = "mediaDate" value = "' . $mediaDate . '" />';
                echo '<input type ="button" class = "btnDisapprove" />';


                if ($approvals > 0) {
                    //echo '<tr><td>';

                    echo '&nbsp;<span style = "color:red;font-weight:bold;font-size:16px">' . $approvals . '</font>';
                }
                echo '</form>';
            } else {

                echo '<form>';

                echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';
                echo '<input type ="hidden" class = "ID" value="' . $ID . '"/>';
                echo '<input type ="hidden" class = "mediaID" value = "' . $mediaID . '" />';
                echo '<input type ="hidden" class = "mediaName" id = "mediaName" value ="' . $mediaName . '" />';
                echo '<input type ="hidden" class = "mediaType" id = "type" value = "' . $mediaType . '" />';
                echo '<input type ="hidden" class = "mediaDate" id = "mediaDate" value = "' . $mediaDate . '" />';
                echo '<input type ="button" class = "btnApprove" />';


                if ($approvals > 0) {


                    echo '&nbsp;<span style = "color:red;font-weight:bold;font-size:16px">' . $approvals . '</font>';
                }
                echo '</form>';
            }
            echo "</div>"; // end of approval div
            echo '</td></tr></table>';

            ?>



            <form method="post" action="" enctype="multipart/form-data"
                  onsubmit="return (checkComment(this, btnComment) && saveScrollPositions(this))">

                <input type="text" class="form-control" name="postComment" id="postComment" style="margin-top:10px;"
                       placeholder="Write a comment"/>


                <input type="file" name="flPostMedia" id="flPostMedia"/>
                <br/>
                <input type="hidden" name="postID" id="postID" Value="<?php echo $postID ?>"/>
                <input type="hidden" name="ID" id="ID" value="<?php echo $ID ?>"/>
                <input type="hidden" name="mediaID" id="mediaID" value="<?php echo $mediaID ?>"/>

                <input type="hidden" name="mediaName" id="mediaName" value="<?php echo $mediaName ?>"/>
                <input type="hidden" name="type" id="type" value="<?php echo $type ?>"/>
                <input type="hidden" name="mediaDate" id="mediaDate" value="<?php echo $mediaDate ?>"/>

                <input type="hidden" name="back" id="back" value="<?php echo $back ?>"/>
                <input type="hidden" name="scrollx" id="scrollx" value="0"/>
                <input type="hidden" name="scrolly" id="scrolly" value="0"/>
                <?php if (isset($visitor)) { ?>
                    <input type="button" class="btnComment" Value="Comment" disabled/>
                <?php } else { ?>
                    <input type="submit" class="btnComment" id="btnComment" name="btnComment" Value="Comment"/>
                <?php } ?>
            </form>


            <?php
            // get post comments

            $sql3 = "SELECT DISTINCT
                        PostComments.Comment As Comment,
                        PostComments.ID As CommentID,
                        Members.ID As MemberID,
                        CONCAT(Members.FirstName, ' ', Members.LastName) As Name,
                        Profile.ProfilePhoto As ProfilePhoto
                        FROM PostComments, Members, Profile
                        WHERE
                        PostComments.Post_ID = '$postID '
                        AND PostComments.Member_ID = Members.ID
                        AND Profile.Member_ID = Members.ID
                        Group By PostComments.ID DESC LIMIT 3";

            $result3 = mysql_query($sql3) or die(mysql_error());
            if (mysql_num_rows($result3) > 0) {
                echo '<table class="comment-style">';
                while ($rows3 = mysql_fetch_assoc($result3)) {
                    $comment = $rows3['Comment'];
                    $profilePhoto = $rows3['ProfilePhoto'];
                    echo '<br/>';

                    echo '<tr><td style = "width:15%;" valign = "top">';
                    echo '<img src = "' . $mediaPath . $profilePhoto . '" class="profilePhoto-Feed"  height = "50" width = "50" style = "border:1px solid black" title = "' . $rows3['Name'] . '" />&nbsp;</td><td valign = "top"><b>' . $rows3['Name'] . '</a></b>&nbsp;&nbsp;' . nl2br($comment);
                    echo '</td></tr>';
                }

                echo '</table>';
            }


            ?>

            <?php
            $sql4 = "SELECT DISTINCT
                        PostComments.Comment As Comment,
                        PostComments.ID As CommentID,
                        Members.ID As MemberID,
                        CONCAT(Members.FirstName, ' ', Members.LastName) As Name,
                        Profile.ProfilePhoto As ProfilePhoto
                        FROM PostComments,Members, Profile
                        WHERE
                        PostComments.Post_ID = '$postID'
                        AND PostComments.Member_ID = Members.ID
                        AND Profile.Member_ID = Members.ID
                        Group By PostComments.ID Order By PostComments.ID DESC LIMIT 3, 100";

            $result4 = mysql_query($sql4) or die(mysql_error());
            if (mysql_numrows($result4) > 0) {
            ?>


            <a href="javascript:showComments('moreComments');">Show More</a>

            <div id="moreComments" style="display:none;">

                <?php
                echo '<br/>';
                echo '<table class="comment-style">';
                while ($rows4 = mysql_fetch_assoc($result4)) {
                    $comment = $rows4['Comment'];
                    $profilePhoto = $rows4['ProfilePhoto'];

                    echo '<tr><td style = "width:15%;" valign = "top">';

                    echo '<img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" class="profilePhoto-Feed" style = "border:1px solid black" title = "' . $rows4['Name'] . '" />&nbsp;</td><td valign = "top"><b>' . $rows4['Name'] . '</a></b></a>&nbsp;&nbsp;' . nl2br($comment) . '</span>';
                    echo '</td></tr>';
                }

                echo '</table>';
                echo '</div>'; // end of more comments div
                ?>

                <?php

                echo '<hr/>';
                ?>

                <br/><br/><br/>
                <?php
                echo '<tr><td>';
                /* if the session id is the same as the id related to the folder
                and the is business bit aligns with the session type, picture owner is confirmed
                show delete */
                }
                }

                if ($_SESSION['ID'] == $memberID) {
                ?>
                <br/><br/>
                <form method="get" action="" onsubmit="return confirm('Are you sure you want to delete this photo')">
                    <?php
                    echo '<input type = "submit" name = "btnDelete" id = "btnDelete" value = "Delete" class="deleteButton" /><br/><br/>';
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

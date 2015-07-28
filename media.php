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
$profilePhoto = $pRows['Poster'];
$name = $pRows['FirstName'] . ' ' . $pRows['LastName'];

?>

<?php
$profileMediaSrc = trim("/poster/" . $profilePhoto);
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

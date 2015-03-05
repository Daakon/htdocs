<?php
require 'connect.php';
require 'getSession.php';
require 'html_functions.php';
require 'mediapath.php';

get_head_files();
get_header();
require 'memory_settings.php';
$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];

// handle roll call post

$post = mysql_real_escape_string($_POST['post']);
$category = "";

if (isset($_POST['submit'])) {



    // if photo is provided
    if (strlen($_FILES['flPostMedia']['name']) > 0) {

        // check file size
        if ($_FILES['flPostMedia']['size'] > 500000000) {

            exit();
        }

        // create media type arrays
        $videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
            "video/quicktime", "video/webm", "video/x-matroska",
            "video/x-ms-wmw");
        // video file types
        $photoFileTypes = array("image/jpg", "image/jpeg", "image/png", "image/tiff",
            "image/gif", "image/raw");

        // add unique id to image name to make it unique and add it to the file server
        $mediaName = $_FILES["flPostMedia"]["name"];
        $mediaName = trim(uniqid() . $mediaName);
        $mediaFile = $_FILES['flPostMedia']['tmp_name'];
        $type = $_FILES['flPostMedia']['type'];

        require 'media_post_file_path.php';

        if (in_array($type, $videoFileTypes)) {
            // do nothing

        } else {
            if ($type == "image/jpg" || $type == "image/jpeg") {
                $src = imagecreatefromjpeg($mediaFile);
            } else if ($type == "image/png") {
                $src = imagecreatefrompng($mediaFile);
            } else if ($type == "image/gif") {
                $src = imagecreatefromgif($mediaFile);
            } else {
                echo "<script>alert('Invalid File Type'); location = 'home.php'";
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
        require 'media_post_file_path.php';

// save photo/video
        if (in_array($type, $videoFileTypes)) {
            $cmd = "ffmpeg -i $mediaFile -vf 'transpose=1' $mediaFile";
            exec($cmd);
            move_uploaded_file($mediaFile, $postMediaFilePath);

        } else {

            if (in_array($type, $photoFileTypes)) {

                if ($type == "image/jpg" || $type == "image/jpeg") {
                    imagejpeg($src, $postMediaFilePath, 100);
                } else if ($type == "image/png") {

                    imagepng($src, $postMediaFilePath, 0, NULL);


                } else if ($type == "image/gif") {
                    imagegif($src, $postMediaFilePath, 100);

                } else {
                    echo "<script>alert('Invalid File Type'); location = 'home.php'</script>";
                    exit;
                }
            }
            // if photo didn't get uploaded, notify the user
            if (!file_exists($postMediaFilePath)) {
                echo "<script>alert('File could not be uploaded, try uploading a different file type.'); location= 'home.php'</script>";
            }

            imagedestroy($src);

            // store media pointer
            $sql = "INSERT INTO Media (Member_ID,  MediaName,  MediaType,  MediaDate    ) Values
                                               ('$ID',    '$mediaName', '$type',   CURRENT_DATE())";
            mysql_query($sql) or die(mysql_error());

            // get media ID
            $sqlGetMedia = "SELECT * FROM Media WHERE MediaName = '$mediaName'";
            $mediaResult = mysql_query($sqlGetMedia) or die(mysql_error());
            $mediaRow = mysql_fetch_assoc($mediaResult);
            $mediaID = $mediaRow['ID'];
            $media = $mediaRow['MediaName'];
            $mediaType = $mediaRow['Type'];
            $mediaDate = $mediaRow['MediaDate'];
        }

        // build post links based on media type
        if (in_array($type, $photoFileTypes)) {

            $img = '<img src = "' . $postMediaFilePath .'" />';
            $img = '<a href = "media.php?id=' . $ID . '&pid=' . $mediaID . '&media=' . $mediaName . '&type=' . $mediaType . '&photoDate=' . $mediaDate . '">' . $img . '</a>';
        } // check if file type is a video
        elseif (in_array($type, $videoFileTypes)) {

            $img = '<video src = "' . $postMediaFilePath . '" height = "500px" width = "400px" frameborder = "1" controls preload="none" SCALE="ToFit"></video>';
            $img = '<a href = "media.php?id=' . $ID . '&pid=' . $mediaID . '&photo=' . $mediaName . '&type=' . $mediaType . '&photoDate=' . $mediaDate . '">' . $img . '</a>';
        } else {
            // if invalid file type
            echo '<script>alert("Invalid File Type!");</script>';
            echo "<script>location= 'home.php'</script>";
            exit;
        }

        $post = $post . '<br/><br/>' . $img . '<br/>';
        echo "<script>alert('test');</script>";
        $sql = "INSERT INTO Posts (Post,    Category,  Member_ID,   PostDate) Values
                                      ('$post', '$category', '$ID',       CURDATE())";
        mysql_query($sql) or die(mysql_error());
        $newPostID = mysql_insert_id();

        // update Media table with new post id
        if (isset($_SESSION['ID'])) {
            $sqlUpdateMedia = "UPDATE Media SET Post_ID = '$newPostID' WHERE MediaName = '$mediaName' ";
            mysql_query($sqlUpdateMedia) or die(mysql_error());
        }

    } // if no media
    else {

        $sql = "INSERT INTO Posts (Post,       Category,    Member_ID,   PostDate) Values
                                  ('$post',   '$category',   '$ID',      CURDATE())";
        mysql_query($sql) or die(mysql_error());

    }
}

?>

<style>

    iframe { max-width: 100%; height: auto; }
    img { max-width: 100%; height: auto;}
    video { max-width: 100%; height: auto; }
    embed  { max-width: 100%; height: auto; }
    script { max-width: 100%; height: auto; }

    .btnApprove {
        background: url("company_photos/gray_check.png") no-repeat;
        width: 30px;
        height: 30px;
        border: none;
    }
    .btnDisapprove {
        background: url("company_photos/red_check.png") no-repeat;
        width: 30px;
        height: 30px;
        border: none;
    }
</style>


<style>
    .enlarge-onhover {
        width: 50px;
        height: 50px;
    }
    .enlarge-onhover:hover {
        width: 100px;
        height: 100px;
        position: inherit;

        margin-top: 0px;
        margin-left: 0px;

    }
</style>

<script>
    $(document).ready(function() {
        $("body").delegate(".btnApprove", "click", function() {
            var parentDiv = $(this).closest("div[id^=approvals]");
            var data={
                postID: $(this).closest('tr').find('.postID').val(),
                ID: $(this).closest('tr').find('.ID').val()
                //add other properties similarly
            }

            $.ajax({
                type: "post",
                url: "post_approve.php",
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
    $(document).ready(function() {
        $("body").delegate(".btnDisapprove", "click", function() {
            var parentDiv = $(this).closest("div[id^=approvals]");
            var data={
                postID: $(this).closest('tr').find('.postID').val(),
                ID: $(this).closest('tr').find('.ID').val()
                //add other properties similarly
            }
            $.ajax({
                type: "post",
                url: "post_disapprove.php",
                data: data,
                success: function(data)
                {
                    parentDiv.html(data);
                }

            })
        });
    });
</script>

<body >

<div class="container" style="background:red;padding:100px;" >
    <div class="row">
        <div class="col-xs-12 roll-call center-block" >
            <img src="images/roll-call.gif" height="150px" width="150px" alt="Roll Call" />
            <br/>
            <form  method= "post" enctype ="multipart/form-data" action = "" >
                <img src="images/image-icon.png" height="30px" width="30px" alt="Photos/Video" />
                <strong>Attach Photo/Video To Your Post</strong>
                <input type= "file" width="10px;"  name = "flPostMedia" id = "flPostMedia"  />
                <input type="hidden" name="MAX_FILE_SIZE" value ="500000000"
                <br/>
                <input type="text" name="post" id="post" class="form-control" style="border:1px solid black" placeholder="Share Your Talent"/>
                <br/>
                <input type="submit" class="post-button" name="submit" id="submit" value="Post" />
            </form>
        </div>
    </div>

    <?php
    $sql = "SELECT DISTINCT Members.ID As MemberID, Members.FirstName As FirstName,Members.LastName As LastName,
    Posts.ID As PostID, Posts.Post As Post,Posts.Category As Category,
    Media.MediaName As MediaName
    FROM Members,Posts,Media
    WHERE
    Members.IsActive = 1
    And Members.IsSuspended = 0
    And Members.ID = Posts.Member_ID
    And Members.ID = Media.Member_ID
    AND Media.IsProfilePhoto = 1
    And Posts.IsDeleted = 0
    Group By Posts.ID
    Order By Posts.ID DESC ";


    $result = mysql_query($sql) or die(mysql_error());


    if (mysql_numrows($result) > 0) {
        while ($rows = mysql_fetch_assoc($result)) {
            $memberID = $rows['MembersID'];
            $name = $rows['FirstName'] . ' ' . $rows['LastName'];
            $mediaName = $rows['MediaName'];
            $category = $rows['Category'];
            $post = $rows['Post'];
            $postID = $rows['PostID']
            ?>
            <div class="row">
                <div class="col-xs-12 " style="background:white;border-radius:10px;margin-top:20px;" align="left" >

                    <img src="<?php echo $mediaPath . $mediaName ?>" height="50" width="50" border="" alt=""
                         title="<?php echo $name ?>" class='enlarge-onhover' /> &nbsp <b><font
                            size="4"><?php echo $name ?></font></b>
                    <br/>
                    <p><?php echo nl2br($post);?></p>


                    <?php

                    //check if member has approved this post
                    //----------------------------------------------------------------
                    //require 'getSessionType.php';

                    $sql2 = "SELECT ID FROM PostApprovals WHERE Post_ID = '$postID' AND Member_ID = '$ID'";
                    $result2 = mysql_query($sql2) or die(mysql_error());
                    $rows2 = mysql_fetch_assoc($result2);


                    // get approvals for each post
                    $approvals = mysql_num_rows(mysql_query("SELECT * FROM PostApprovals WHERE Post_ID = '$postID'"));

                    // show disapprove if members has approved the post
                    echo '<table>';
                    echo '<tr>';
                    echo '<td>';
                    echo "<div id = 'approvals$postID'>";

                    if (mysql_numrows($result2) > 0) {

                        echo '<form>';

                        echo '<input type ="hidden" class = "postID" id = "postID" value = "'.$postID.'" />';
                        echo '<input type ="hidden" class = "ID" id = "ID" value = "'.$ID.'" />';
                        echo '<input type ="button" class = "btnDisapprove" />';

                        if ($approvals > 0) {
                            //echo '<tr><td>';

                            echo '&nbsp;<span style = "color:red;font-weight:bold;font-size:16">'.$approvals.'</font>';
                        }
                        echo '</form>';
                    }

                    else {
                        echo '<form>';

                        echo '<input type ="hidden" class = "postID" id = "postID" value = "'.$postID.'" />';
                        echo '<input type ="hidden" class = "ID" id = "ID" value = "'.$ID.'" />';
                        echo '<input type ="button" class = "btnApprove" />';

                        if ($approvals > 0) {
                            //echo '<tr><td>';

                            echo '&nbsp;<span style = "color:red;font-weight:bold;font-size:16">'.$approvals.'</font>';
                        }
                        echo '</form>';
                    }

                    echo '</td></tr></table>';

                    //-------------------------------------------------------------
                    // End of approvals
                    //-----------------------------------------------------------

                    ?>
                </div>
            </div>


        <?php
        }
    }
    ?>



</div>

</body>
</html></html>
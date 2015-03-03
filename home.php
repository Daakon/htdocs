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


if (isset($_POST['post'])) {
    $post = mysql_real_escape_string($_POST['post']);
    $category = "";

    // if photo is provided
    if (isset($_FILES['flPostMedia']) && strlen($_FILES['flPostMedia']['name']) > 1) {

        // check file size
        if ($_FILES['flPostMedia']['size'] > 50000000) {
            echo '<script>alert("File is over 50MB");</script>';
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

                $img = '<embed src = "' . $postMediaFilePath . '" height = "500px" width = "400px" frameborder = "0" AUTOPLAY = "false" CONTROLLER="true" SCALE="ToFit"></embed>';
                $img = '<a href = "media.php?id=' . $ID . '&pid=' . $mediaID . '&photo=' . $mediaName . '&type=' . $mediaType . '&photoDate=' . $mediaDate . '">' . $img . '</a>';
            } else {
                // if invalid file type
                echo '<script>alert("Invalid File Type!");</script>';
                echo "<script>location= 'home.php'</script>";
                exit;
            }

            $post = $post . '<br/><br/>' . $img . '<br/>';

            $sql = "INSERT INTO Posts (Post,    Category,  Member_ID,   PostDate) Values
                                      ('$post', '$category', '$ID',       CURDATE())";
            mysql_query($sql) or die(mysql_error());
            $newPostID = mysql_insert_id();

            // update Media table with new post id
            if (isset($_SESSION['ID'])) {
                $sqlUpdateMedia = "UPDATE Media SET Post_ID = '$newPostID' WHERE MediaName = '$mediaName' ";
                mysql_query($sqlUpdateMedia) or die(mysql_error());
            }
        }
    } // if no media
    else {

        $sql = "INSERT INTO Posts (Post,       Category,    Member_ID,   PostDate) Values
                                  ('$post',   '$category',   '$ID',      CURDATE())";
        mysql_query($sql) or die(mysql_error());
    }
}
?>

<body >
<div class="container" style="background:red;padding:50px;" >
    <div class="row">
        <div class="col-xs-12 roll-call center-block" >
            <img src="images/roll-call.gif" height="150px" width="150px" alt="Roll Call" />
            <br/>
            <form  method= "post" enctype ="multipart/form-data" action = "" >
                <img src="images/image-icon.png" height="30px" width="30px" alt="Photos/Video" />
                <strong>Attach Photo/Video To Your Post</strong>
                <input type= "file" width="10px;"  name = "flPostMedia" id = "flPostMedia"  />
                <br/>
                <input type="text" name="post" id="post" class="form-control" style="border:1px solid black" placeholder="Share Your Talent"/>
                <br/>
                <input type="submit" class="post-button" name="submit" id="submit" value="Post" />
            </form>
        </div>
    </div>

    <?php
    $sql = "SELECT DISTINCT Members.ID As MemberID, Members.FirstName As FirstName,Members.LastName As LastName,
    Posts.Post As Post,Posts.Category As Category,
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
            ?>
            <div class="row">
                <div class="col-xs-12 " style="background:white;border-radius:10px;margin-top:20px;" align="left" >

                    <img src="<?php echo $mediaPath . $mediaName ?>" height="50" width="50" border="" alt=""
                         title="<?php echo $name ?>" class='enlarge-onhover' /> &nbsp <b><font
                            size="4"><?php echo $name ?></font></b>
                    <br/>
                    <p><?php echo nl2br($post);?></p>
                </div>
            </div>


        <?php
        }
    }
    ?>



    </div>

</body>
</html>
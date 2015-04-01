<?php
require 'connect.php';
require 'getSession.php';
require 'html_functions.php';
require 'mediaPath.php';
require 'model_functions.php';

get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];
?>

<?php
// handle photo/video uploads

if (isset($_POST['Upload'])) {


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
            // convert to mp4
            $fileName = pathinfo($mediaName, PATHINFO_FILENAME);
            $newFileName = $fileName.".mp4";
            exec("ffmpeg -i $fileName -vcodec libx264 -pix_fmt yuv420p -profile:v baseline -preset slow -crf 22 -movflags +faststart $newFileName");
            $mediaName = $newFileName;

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


        require 'media_post_file_path.php';

        // save photo/video
        if (in_array($type, $videoFileTypes)) {
            move_uploaded_file($mediaFile, $postMediaFilePath);

        } else {

            if (in_array($type, $photoFileTypes)) {

                // read exif data
                $exif = exif_read_data($mediaFile);

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


// store media pointer
        $sql = "INSERT INTO Media (Member_ID,  MediaName,  MediaType,  MediaDate, Private    ) Values
('$ID',    '$mediaName', '$type',   CURRENT_DATE(), 1)";
        mysql_query($sql) or die(mysql_error());


        ?>

        <?php
        $sql = "SELECT * FROM Members
WHERE
Members.ID = '$ID'
And Members.IsActive = 1 ";

        $result = mysql_query($sql) or die(mysql_error());
        $rows = mysql_fetch_assoc($result);
        $fName = $rows['FirstName'];
        $lName = $rows['LastName'];

        if (mysql_numrows($result) == 0) {
            echo '<script>alert("This profile could not be found");location = "/index.php"</script>';
        }
    }
}
?>

<?php include('media_sizes.html'); ?>

<body>

<div class="container">
    <?php require 'profile_menu.php'; ?>
    <br/><br/>

    <div class="row row-padding">

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call">
            <h2>Photos & Videos</h2>

            <form method="post" enctype="multipart/form-data" action="">
                <img src="/images/image-icon.png" height="30px" width="30px" alt="Photos/Video"/>
                <strong>Upload a Photo or Video to your profile</strong>
                <br/><br/>
                <span style="padding-left:5px;font-style:italic;color:red">
                    (Only people you share your profile with will see the photos and videos you upload here)
                    *Photos are noted as public and private.
                    </span>
                <br/><br/>

                <input type="file" width="10px;" name="flPostMedia" id="flPostMedia"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="500000000"
                <br/><br/>
                <input type="submit" class="post-button" name="Upload" id="Upload" value="Upload"/>
            </form>
            <br/>
            <hr/>
            <br/>
            <?php

            $sql = "SELECT * FROM Media WHERE Member_ID = '$ID' And (IsDeleted IS NULL Or IsDeleted = 0)
            Order By ID DESC ";

            $result = mysql_query($sql) or die(mysql_error());
            while ($rows = mysql_fetch_assoc($result)) {
                $mediaName = $rows['MediaName'];
                $mediaType = $rows['MediaType'];
                $mediaDate = $rows['MediaDate'];
                $mediaID = $rows['ID'];
                $private = $rows['Private'];
                $mediaFilePath = trim("media/" . $mediaName);

                $privateString = "Public";

                if ($private == 1) {
                    $privateString = "Private";
                }

                if (file_exists($mediaFilePath)) {

                    // check if file type is a photo
                    $videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
                        "video/quicktime", "video/webm", "video/x-matroska",
                        "video/x-ms-wmw");
// video file types
                    $photoFileTypes = array("image/jpg", "image/jpeg", "image/png", "image/tiff",
                        "image/gif", "image/raw");

// check if file type is a photo
                    if (in_array($mediaType, $photoFileTypes)) {

                        $img = '<a href = "media.php?id=' . $ID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '" ><img src = "' . $mediaPath . $mediaName . '" style="border:2px solid black;" /></a>
                        <br/>'.$privateString;

                    } // check if file type is a video
                    elseif (in_array($mediaType, $videoFileTypes)) {

                        $img = '<a href = "' . $videoPath . $mediaName . '"><img src = "' . $images . 'video-bg.jpg" height="100" width = "100" /></a>
                        <br/>'.$privateString;

                    }
                    ?>
                    <?php
                    echo "<div>$img</div>";
                    echo "<br/>";
                    ?>
                <?php }
            } ?>
        </div>

    </div>
</div>
</body>
</html>
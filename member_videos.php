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
        if ($_FILES['flPostMedia']['size'] > 1000000000) {

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
            $cmd = "$ffmpeg -i \"$postMediaFilePath\" -r 1 -f image2 $poster 2>&1";

            exec($cmd);

            $poster = imagecreatefromjpeg($poster);
            $exif = @exif_read_data($poster);

            if ( isset($exif['Orientation']) && !empty($exif['Orientation']) ) {

                // Decide orientation
                if ( $exif['Orientation'] == 3 ) {
                    $rotation = 180;
                } else if ( $exif['Orientation'] == 6 ) {
                    $rotation = 90;
                } else if ( $exif['Orientation'] == 8 ) {
                    $rotation = -90;
                } else {
                    $rotation = 0;
                }

                // Rotate the image
                if ( $rotation ) {
                    $img = imagerotate($poster, $rotation, 0);
                    imagejpeg($img, $posterPath.$posterName, 100);
                }
            }
            else {
                // if we cannot determine the exif data
                // then we will rotate the image if it is wider than it is tall
                // this is the best fallback so far.
                $size = getimagesize("$posterPath$posterName");
                $width = $size[0];
                $height = $size[1];
                if ($width > $height) {
                    $img = imagerotate($poster, -90, 0);
                    imagejpeg($img, $posterPath.$posterName, 100);
                }
            }

        } else {
                echo "<script>alert('Invalid File Type'); location = 'home.php'</script>";
                exit;
            }
        }


// if photo didn't get uploaded, notify the user
        if (!file_exists($postMediaFilePath)) {
            echo "<script>alert('File could not be uploaded, try uploading a different video file type.'); location= 'home.php'</script>";
        }


// store media pointer
        $sql = "INSERT INTO Media (Member_ID,  MediaName,  MediaType,  MediaDate, Private,  Poster    ) Values
                                  ('$ID',    '$mediaName', '$type',   CURRENT_DATE(), 1,   '$posterName')";
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
?>

<?php

// handle profile text

require 'class-Clockwork.php';

if (isset($_POST['text']) && $_POST['text'] == "Text") {

    $mediaName = $_POST['mediaName'];
    $mediaType = $_POST['mediaType'];
    $mediaID = $_POST['mediaID'];
    $mediaDate = $_POST['mediaDate'];

    if (strstr($url, "dev")) {
        $link = "http://dev.rapportbook.com/media/$mediaName";
    }
    else {
        $link = "http://rapportbook.com/media/$mediaName";
    }


    $number = $_POST['number'];
    $number = "1".$number;
    $name = get_users_name($ID);
    $API_KEY = '7344d6254838e6d2c917c4cb78305a3235ba951d';
    try
    {
        // Create a Clockwork object using your API key
        $clockwork = new Clockwork( $API_KEY );

        // Setup and send a message
        $text = str_replace(' ', '%20', $link);
        $text = $name .' sent you a video from Rapportbook '. $text;
        $message = array( 'to' => $number, 'message' => $text );
        $result = $clockwork->send( $message );

        // Check if the send was successful
        if($result['success']) {
            //echo 'Message sent - ID: ' . $result['id'];
            echo "<script>alert('SMS Sent'); location='/member_videos.php'</script>";
        } else {
            $error = $result['error_message'];
            echo "<script>alert('Message failed - Error: $error');</script>";
        }
    }
    catch (ClockworkException $e)
    {
        echo 'Exception sending SMS: ' . $e->getMessage();
    }

}
?>

<?php include('media_sizes.html'); ?>



<body>


<script>
    // show uploading
    function showUploading() {
        document.getElementById("progressBar").style.display = "block";
    }
</script>

<div class="container">
    <?php require 'profile_menu.php'; ?>
    <br/><br/>

    <div class="row row-padding">

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call">
            <h2>Videos</h2>

            <form method="post" enctype="multipart/form-data" action="" >
                <img src="/images/image-icon.png" height="30px" width="30px" alt="Video"/>
                <strong>Upload a Video to your media library</strong>
                <br/><br/>
                <span style="padding-left:5px;font-style:italic;color:red">
                    Public videos have been shared in Roll Call.
                    <br/>
                    Private videos have been uploaded only here.
                    </span>
                <br/><br/>

                <input type="file" width="10px;" name="flPostMedia" id="flPostMedia"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />

                <br/><br/>

                <div id="progressBar" style="display:none;">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            <span class="sr-only">Loading</span>
                        </div>
                    </div>
                </div>
                <br/>
                <input type="submit" class="post-button" name="Upload" id="Upload" value="Upload" onclick="showUploading()"/>
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
                $posterName = $rows['Poster'];

                $privateString = "Public";

                if ($private == 1) {
                    $privateString = "Private";
                }

                if (strlen($posterName) == 0) {
                    $posterName = "video-bg.jpg";
                }

                    // check if file type is a photo
                    $videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
                        "video/quicktime", "video/webm", "video/x-matroska",
                        "video/x-ms-wmw");

                    if (in_array($mediaType, $videoFileTypes)) {

                        $img = '<a href = "' . $videoPath . $mediaName . '"><video src = "' . $videoPath . $mediaName . '" poster="/poster/'.$posterName.'" preload="auto" controls /></a>
                        <a href = "media.php?id=' . $ID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '" ><br/>More</a><br/><br/>'
                            .$privateString.'<br/>';


                    echo "
                    <div>
                    $img

                    </div>";

                    ?>

            <form method="post" action="">
                <div class="form-group">
                    <label for="text">Text this video</label>

                      <br/>
                            <input type="hidden" id="mediaName" name="mediaName" value="<?php echo $mediaName ?>" />
                            <input type="hidden" id="mediaID" name="mediaID" value="<?php echo $mediaID ?>" />
                            <input type="hidden" id="mediaType" name="mediaType" value="<?php echo $mediaType ?>" />
                            <input type="hidden" id="mediaDate" name="mediaDate" value="<?php echo $mediaDate ?>" />
                            <input type="text" id="number" name="number" class="form-control text-center" style="width:150px;" placeholder="2125551212"/>
                        </div>
                        <input type="submit" id="text" name="text" value="Text" style="border-radius: 10px" class="btn btn-default" />
                    </form>
                    <br/><br/>


                    <?php

                    echo "<hr/><br/>";
                    ?>

                    <?php

                    } } ?>
        </div>

    </div>
</div>
</body>
</html>
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

        $audioFileTypes = array("audio/wav", "audio/mp3");
        $audioName;
        $mediaName = $_FILES["flPostMedia"]["name"];
        $mediaFile = $_FILES['flPostMedia']['tmp_name'];
        $type = $_FILES['flPostMedia']['type'];
        $fileName = pathinfo($mediaName, PATHINFO_FILENAME);


        if (in_array($type, $audioFileTypes) || in_array($type, $videoFileTypes)) {
            $audioName = $fileName;
        }

        // check file size
        if ($_FILES['flPostMedia']['size'] > 150000000) {

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
        $mediaName = trim(uniqid() . $mediaName);


        require 'media_post_file_path.php';

        if (in_array($type, $videoFileTypes)) {

            // convert to mp4

            $newFileName = $fileName.".mp4";
            $ffmpeg = '/usr/bin/ffmpeg';
            exec("$ffmpeg -i $fileName -vcodec copy $newFileName");
            $mediaName = $newFileName;


        } else {
            if ($type == "image/jpg" || $type == "image/jpeg") {
                $src = imagecreatefromjpeg($mediaFile);
            } else if ($type == "image/png") {
                $src = imagecreatefrompng($mediaFile);
            } else if ($type == "image/gif") {
                $src = imagecreatefromgif($mediaFile);
            } /*else {
                echo "<script>alert('Invalid File Type');</script>";
                header('Location:member_videos.php');
                exit;
            }*/
        }


        require 'media_post_file_path.php';

// save photo/video
        if (in_array($type, $videoFileTypes) || in_array($type, $audioFileTypes)) {

            move_uploaded_file($mediaFile, $postMediaFilePath);

        } else {

            if (in_array($type, $photoFileTypes)) {

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

            // handle transparency
            imagesavealpha($src, true);
            if ($type == "image/jpg" || $type == "image/jpeg") {
                imagejpeg($src, $postMediaFilePath, 100);
            } else if ($type == "image/png") {
                imagepng($src, $postMediaFilePath, 0, NULL);


            } else if ($type == "image/gif") {
                imagegif($src, $postMediaFilePath, 100);

            } /*else {
                echo "<script>alert('Invalid File Type');</script>";
                header('Location:member_videos.php');
                exit;
            }*/
        }


            if (in_array($type, $videoFileTypes)) {

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
                $cmd = "$ffmpeg -i \"$postMediaFilePath\" -r 1  -f image2 $poster 2>&1";

                exec($cmd);

                $poster = imagecreatefromjpeg($poster);
                $exif = @exif_read_data($poster);

                $size = getimagesize("$posterPath$posterName");
                $width = $size[0];
                $height = $size[1];

                if ($width > $height && $height < 1000) {
                    // video shot in landscape, needs to be flipped
                    $img = imagerotate($poster, 180, 0);
                    imagejpeg($img, $posterPath.$posterName, 100);
                }

                if ($width > $height && $height > 1000) {
                    // video shot in portrait, but still needs to be flipped
                    $img = imagerotate($poster, -90, 0);
                    imagejpeg($img, $posterPath.$posterName, 100);
                }

                $img = '<video src = "' . $videoPath . $mediaName . '" poster="/poster/'.$posterName.'" preload="auto" controls />';

            }
    }


// store media pointer
    $sql = "INSERT INTO Media (Member_ID,  MediaName,  MediaType,  MediaDate,         AudioName,  Private,  Poster    ) Values
                                  ('$ID',  '$mediaName', '$type',   CURRENT_DATE(), '$audioName',    1,     '$posterName')";
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
        echo '<script>alert("This profile could not be found");location = "/member_media.php"</script>';
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
            echo "<script>alert('SMS Sent'); location='/member_media.php'</script>";
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
            <h2>Media</h2>

            <form method="post" enctype="multipart/form-data" action="" >
                <img src="/images/image-icon.png" height="30px" width="30px" alt="Video"/>
                <strong>Upload Photos, Videos & Music to your media library</strong>
                <br/><br/>
                <span style="padding-left:5px;font-style:italic;color:red">
                    Public content has been shared in Roll Call.
                    <br/>
                    Private content has been uploaded only here.
                    </span>
                <br/><br/>

                <input type="file" width="10px;" name="flPostMedia" id="flPostMedia"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="1500000000" />

                <br/><br/>

                <div id="progressBar" style="display:none;">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            <b>File Uploading...please wait</b>
                        </div>
                    </div>
                </div>
                <br/>
                <input type="submit" class="post-button" name="Upload" id="Upload" value="Upload" onclick="showUploading()"/>
            </form>
            <br/>
            <hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; display: inline-block; text-align: left; width:100%;' />
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
                $audioName = $rows['AudioName'];

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

                $photoFileTypes = array("image/jpg", "image/jpeg", "image/png", "image/tiff",
                    "image/gif", "image/raw");

                $audioFileTypes = array("audio/wav", "audio/mp3");

                $text;

                // video type
                if (in_array($mediaType, $videoFileTypes)) {
                    $text = "video";
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
                            <label for="text">Text this <?php echo $text ?></label>

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

                }

                // photo type
            if (in_array($mediaType, $photoFileTypes)) {
                $text = "photo";
                $img = '<a href = "media.php?id=' . $ID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '" ><br/><img src = "' . $mediaPath . $mediaName . '" class="img-responsive"/></a><br/><br/>'
                    . $privateString . '<br/>';


                echo "
                    <div>
                    $img

                    </div>";
                ?>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="text">Text this <?php echo $text ?></label>

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
            }

                // audio type
                if (in_array($mediaType, $audioFileTypes)) {
                    $text = "song";
                    $img = '<b>'.$audioName.'</b><br/><audio controls>
                            <source src="'.$mediaPath . $mediaName.'" type="'.$mediaType.'">
                            Your browser does not support the audio element.
                            </audio>
                            <a href = "media.php?id=' . $ID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '" ><br/>More</a><br/><br/>'
                        .$privateString.'<br/>';

                    echo "<div>$img</div>";
                    ?>
            <form method="post" action="">
                <div class="form-group">
                    <label for="text">Text this <?php echo $text ?></label>

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
                }
            } ?>
        </div>

    </div>
</div>
</body>
</html>
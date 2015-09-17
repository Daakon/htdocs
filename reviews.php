<?php
/**
 * Created by PhpStorm.
 * User: chrismoney
 * Date: 9/17/15
 * Time: 12:15 PM
 */

require 'connect.php';
// compress the page
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
require 'model_functions.php';
require 'mediaPath.php';
require 'getSession.php';
require 'html_functions.php';
require 'findURL.php';
require 'email.php';
require 'category.php';
//require 'ads.php';

get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];

$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
preg_match("/[^\/]+$/",$url ,$match);
$username = $match[0];
$providerID = get_id_from_username($username);
?>

<?php

// handle roll call post
$post = mysql_real_escape_string($_POST['post']);

if (isset($_POST['submit'])) {
if (strlen($post) == 0 || $post == '') {
echo "<script>alert('Your post appears to be empty');</script>";
}
else {
if (strlen($post) > 0) {
$post = makeLinks($post);
    $providerID = $_POST['providerID'];

// if photo is provided
if (strlen($_FILES['flPostMedia']['name']) > 0) {
// check file size
if ($_FILES['flPostMedia']['size'] > 2500000000) {
exit();
}
// create media type arrays
$videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
"video/quicktime", "video/webm", "video/x-matroska",
"video/x-ms-wmw");
// video file types
$photoFileTypes = array("image/jpg", "image/jpeg", "image/png", "image/tiff",
"image/gif", "image/raw");
$audioFileTypes = array("audio/wav", "audio/mp3", "audio/x-m4a");
// add unique id to image name to make it unique and add it to the file server
$mediaName = $_FILES["flPostMedia"]["name"];
$fileName = pathinfo($mediaName, PATHINFO_FILENAME);
$mediaName = trim(uniqid() . $mediaName);
$mediaFile = $_FILES['flPostMedia']['tmp_name'];
$mediaFile2 = "";
copy($_FILES['flPostMedia']['tmp_name'], $mediaFile2);
$mediaFile3 = "";
copy($_FILES['flPostMedia']['tmp_name'], $mediaFile3);
$type = $_FILES['flPostMedia']['type'];
require 'media_post_file_path.php';
if (in_array($type, $audioFileTypes) || in_array($type, $videoFileTypes)) {
$audioName = $fileName;
}
if (in_array($type, $videoFileTypes)) {
// convert to mp4 if not already an mp4
if ($type != "video/mp4") {
$audioName = $fileName;
$newFileName = $fileName . ".mp4";
$oggFileName = $fileName . ".ogv";
$webmFileName = $fileName . ".webm";
$ffmpeg = '/usr/bin/ffmpeg';
// convert mp4
exec("$ffmpeg -i $fileName $newFileName");
$mediaName = $newFileName;
// convert ogg
exec("$ffmpeg -i $fileName  $oggFileName");
// convert webm
exec("$ffmpeg -i $fileName  $webmFileName");
}
} else {
if ($type == "image/jpg" || $type == "image/jpeg") {
$src = imagecreatefromjpeg($mediaFile);
} else if ($type == "image/png") {
$src = imagecreatefrompng($mediaFile);
} else if ($type == "image/gif") {
$src = imagecreatefromgif($mediaFile);
} else {
/* echo "<script>alert('Invalid File Type');</script>";
header('Location:home.php');
exit;*/
}
}
require 'media_post_file_path.php';
// save photo/video
if (in_array($type, $videoFileTypes) || in_array($type, $audioFileTypes)) {
move_uploaded_file($mediaFile, $postMediaFilePath);
//copy new mp4 file path to ogg file path
copy($postMediaFilePath, $postOggFilePathTemp);
// overwrite mp4 with real ogg file path
copy($postOggFilePath, $postOggFilePathTemp);
// copy new mp4 file path to webm file path
copy($postMediaFilePath, $postWebmFilePathTemp);
// overwrite mp4 with real webm file path
copy($postWebmFilePath, $postWebmFilePathTemp);
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
imagejpeg($src, $postMediaFilePath, 50);
} else if ($type == "image/png") {
imagepng($src, $postMediaFilePath, 0, NULL);
} else if ($type == "image/gif") {
imagegif($src, $postMediaFilePath, 50);
} else {
/*echo "<script>alert('Invalid File Type');</script>";
header('Location:home.php');
exit;*/
}
}
// if photo didn't get uploaded, notify the user
if (!file_exists($postMediaFilePath)) {
echo "<script>alert('File could not be uploaded, try uploading a different file type.');location='home.php'</script>";
}
else {
// store media pointer
$sql = "INSERT INTO Media (Member_ID,  MediaName,    MediaOgg,     MediaWebm,      MediaType,  MediaDate,  AudioName    ) Values
('$ID',    '$mediaName', '$oggFileName', '$webmFileName',  '$type',   CURRENT_DATE(), '$audioName'  )";
mysql_query($sql) or die(mysql_error());
$mediaID = mysql_insert_id();
// get media ID
$sqlGetMedia = "SELECT * FROM Media WHERE MediaName = '$mediaName'";
$mediaResult = mysql_query($sqlGetMedia) or die(mysql_error());
$mediaRow = mysql_fetch_assoc($mediaResult);
//$mediaID = $mediaRow['ID'];
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
$img = '<img src = "' . $mediaPath . $mediaName . '" />';
$img = '<a href = "media.php?id=' . $ID . '&mid=' . $mediaID . '&mediaName=' . $media . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '">' . $img . '</a>';
} // check if file type is a video
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
$cmd = "$ffmpeg -i \"$postMediaFilePath\" -r 1 -ss 5 -t 1  -f image2 $poster 2>&1";
exec($cmd);
$poster = imagecreatefromjpeg($poster);
$size = getimagesize("$posterPath$posterName");
$width = $size[0];
$height = $size[1];
if ($width > $height && $height < 1000) {
// video shot in landscape, needs to be flipped
$img = imagerotate($poster, 180, 0);
imagejpeg($img, $posterPath.$posterName, 50);
}
// handle images from videos shot with Iphone
if ($width > $height && $height > 700 && $type == "video/quicktime" || $type == "video/mp4") {
// video shot in landscape, needs to be flipped
$img = imagerotate($poster, -90, 0);
imagejpeg($img, $posterPath.$posterName, 50);
}
$img = '<video poster="/poster/'.$posterName.'" preload="none" controls>
    <source src = "' . $videoPath . $mediaName . '" type="video/mp4" />
    <source src = "' . $videoPath . $oggFileName . '" type = "video/ogg" />
    <source src = "' . $videoPath . $webmFileName . '" type = "video/webm" />
</video>';
} else {
// if invalid file type
/*echo '<script>alert("Invalid File Type!");</script>';
header('Location:home.php');
exit;*/
}
if (in_array($type, $audioFileTypes)) {
$post = $post . '<br/>'. $img . '<br/>';
}
elseif (in_array($type, $videoFileTypes)) {
$post = $post . '<br/><br/><a href="'. $videoPath . $mediaName . '" download="'.$audioName.'"">View in native player </a>' . $img . '<br/>';
}
else {
$post = $post . '<br/><br/>' . $img . '<br/>';
}
$sql = "INSERT INTO Reviewss (Review,  Member_ID, Provider_ID    ReviewDate) Values
                              ('$post', '$ID',    '$providerID', CURDATE())";
mysql_query($sql) or die(mysql_error());
$newPostID = mysql_insert_id();
// update Media table with new post id
$sqlUpdateMedia = "UPDATE Media SET Post_ID = $newPostID, Poster='$posterName' WHERE ID = '$mediaID' ";
mysql_query($sqlUpdateMedia) or die(mysql_error());
}
} // if no media
else {
$sql = "INSERT INTO Reviews (Review,    Member_ID,    Provider_ID,   ReviewDate) Values
                          ('$post',   '$ID',        '$providerID',         CURDATE())";
mysql_query($sql) or die(mysql_error());
}
//alert_all_matching_service_providers($category, getMemberState($ID));
}

}
echo "<script>location='/reviews.php/$username'</script>";
}
?>

<script type="text/javascript">
    function showPost(long,short) {
        var longPost = document.getElementById(long);
        var shortPost = document.getElementById(short);
        if (longPost.style.display == 'none') {
            longPost.style.display = 'block';
            shortPost.style.display = 'none';
        }
    }
</script>

<body>
<div class="container">
<div class="row row-padding">

    <div class=" col-md-10  col-lg-10 col-md-offset-2 col-lg-offset-2 ">
        <ul class="list-inline">

            <?php require 'profile_menu.php'; ?>
        </ul>


    <br/>

        <div class=" col-md-9 col-lg-9 roll-call ">

        <form method="post" enctype="multipart/form-data" action="" onsubmit="showUploading()">
            <img src="/images/image-icon.png" height="30px" width="30px" alt="Photos/Video"/>
            <strong>Attach A Photo or Video To Your Review</strong>
            <input type="file" width="10px;" name="flPostMedia" id="flPostMedia"/>
            <br/>
                <textarea name="post" id="post" class="form-control textArea"
                          placeholder="Share Your Thoughts" ></textarea>
            <br/>
            <div id="progress" style="display:none;">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                        <b>File uploading...please wait</b>
                    </div>
                </div>
            </div>
            <input type="hidden" name="providerID" id="providerID" value="<?php echo $providerID ?>" />
            <input type="submit" class="post-button" name="submit" id="submit" value="Post"/>
        </form>

   </div>

        <br/>

        <div style="clear:both">
            <?php
            $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            preg_match("/[^\/]+$/",$url ,$match);
            $username = $match[0];
            ?>
            <h4>Reviews For <?php echo $username ?></h4>
        </div>

        <?php
        $limit = "100";
        require 'review-query.php';
        ?>


    </div>
    </div>
    </body>
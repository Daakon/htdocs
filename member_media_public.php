<?php
require 'connect.php';
require 'getSession_public.php';
require 'html_functions.php';
require 'mediaPath.php';
require 'model_functions.php';

get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];


$username = $_SESSION['Username'];

$sql = "SELECT * FROM Members
WHERE
Members.Username = '$username'
And Members.IsActive = 1 ";

$result = mysql_query($sql) or die(mysql_error());
$rows = mysql_fetch_assoc($result);
$memberID = $rows['ID'];
$fName = $rows['FirstName'];
$lName = $rows['LastName'];

if (mysql_numrows($result) == 0) {
    echo '<script>alert("This profile could not be found");location = "/index.php"</script>';
}
?>

<?php include('media_sizes.html'); ?>

<body>

<div class="container">
    <?php require 'profile_menu_public.php'; ?>
    <br/><br/>

    <div class="row row-padding">

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call">
            <h2>Photos & Videos</h2>


            <?php

            $sql = "SELECT * FROM Media WHERE Member_ID = '$memberID' And (IsDeleted IS NULL Or IsDeleted = 0)
            Order By ID DESC ";

            $result = mysql_query($sql) or die(mysql_error());
            while ($rows = mysql_fetch_assoc($result)) {
                $mediaName = $rows['MediaName'];
                $mediaType = $rows['MediaType'];
                $mediaDate = $rows['MediaDate'];
                $mediaID = $rows['ID'];
                $private = $rows['Private'];
                $mediaFilePath = trim("media/" . $mediaName);
                $poster = $rows['Poster'];

                /*$privateString = "Public";

                if ($private == 1) {
                    $privateString = "Private";
                }*/
                $privateString = "";

                if (strlen($poster) == "" || strlen($poster) == 0) {
                    $poster = "video-bg.jpg";
                }

                if (file_exists($mediaFilePath)) {

                    // check if file type is a photo
                    $videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
                        "video/quicktime", "video/webm", "video/x-matroska",
                        "video/x-ms-wmw");
// video file types
                    $photoFileTypes = array("image/jpg", "image/jpeg", "image/png", "image/tiff",
                        "image/gif", "image/raw");

                    // audio file types
                    $audioFileTypes = array("audio/wav", "audio/mp3");

// check if file type is a photo
                    if (in_array($mediaType, $photoFileTypes)) {
                        if (isset($_SESSION['ID']) && !empty($_SESSION['ID'])) {
                            $img = '<a href = "/media.php?id=' . $memberID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '" ><img src = "' . $mediaPath . $mediaName . '" style="border:2px solid black;" /></a>
                        <br/>' . $privateString;
                        }
                        else {
                            $img = '<img src = "' . $mediaPath . $mediaName . '" style="border:2px solid black;" />
                        <br/>' . $privateString;
                        }

                    } // check if file type is a video
                    elseif (in_array($mediaType, $videoFileTypes)) {

                        if (isset($_SESSION['ID']) && !empty($_SESSION['ID'])) {
                            $img = '<video src = "' . $videoPath . $mediaName . '" poster="/poster/'.$poster.'" preload="auto" controls />
                        <a href = "/media.php?id=' . $ID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '" >More</a><br/>'
                                . $privateString;
                        }
                        else {
                            $img = '<a href = "' . $videoPath . $mediaName . '"><img src = "' . $images . 'video-bg.jpg" height="100" width = "100" /></a>'
                                . $privateString;
                        }
                    }
                    if (in_array($mediaType, $audioFileTypes)) {
                        $text = "song";
                        $img = '<audio controls>
                            <source src="' . $mediaPath . $mediaName . '" type="' . $mediaType . '">
                            Your browser does not support the audio element.
                            </audio>
                            <a href = "media.php?id=' . $ID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '" ><br/>More</a><br/><br/>'
                            . $privateString . '<br/>';
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

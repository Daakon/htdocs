<?php
require 'connect.php';
require 'getSession.php';
require 'html_functions.php';
require 'mediaPath.php';
get_head_files();
get_header();
require 'memory_settings.php';
$ID = $_SESSION['ID'];
?>



<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
preg_match("/[^\/]+$/",$url ,$match);
$username = $match[0];
$token = $match[1];

$sql = "SELECT * FROM Members
WHERE
Members.Username = '$username'
And Members.IsActive = 1 ";

$result = mysql_query($sql) or die(mysql_error());
$rows = mysql_fetch_assoc($result);
$fName = $rows['FirstName'];
$lName = $rows['LastName'];

if (mysql_numrows($result) == 0) {
    echo '<script>alert("This profile could not be found");location = "/index.php"</script>';
}


?>

<?php include('media_sizes.html'); ?>

<body>

<div class="container">
    <ul class="list-inline">
        <li><a href="/profile_public.php/<?php echo $username ?>">Profile</a></li>
        <li><a href="/messages_public.php/<?php echo $username ?>">Messaging</a></li>
    </ul>
    <br/><br/>
    <div class="row row-padding">

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call">
            <h2>Photos</h2>

            <?php

            $sql = "SELECT * FROM Media WHERE Member_ID = '$ID' And (IsDeleted IS NULL Or IsDeleted = 0) ";
            $result = mysql_query($sql) or die(mysql_error());
            while ($rows = mysql_fetch_assoc($result)) {
                $mediaName = $rows['MediaName'];
                $mediaType = $rows['MediaType'];
                $mediaDate = $rows['MediaDate'];
                $mediaID = $rows['ID'];

                $mediaFilePath = trim("media/".$mediaName);

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

                        $img = '<a href = "media.php?id='.$ID.'&mediaName='.$mediaName.'&mid='.$mediaID.'&mediaType='.$mediaType.'&mediaDate='.$mediaDate.'" ><img src = "'.$mediaPath . $mediaName.'" style="border:2px solid black;" /></a>';

                    }
// check if file type is a video
                    elseif (in_array($mediaType, $videoFileTypes)) {

                        $img = '<a " href = "media.php?id='.$id.'&mediaName='.$mediaName.'&mid='.$mediaID.'&mediaType='.$type.'&mediaDate='.$mediaDate.'" ><video src = "'.$mediaPath . $mediaName.'" style="border:2px solid black;"></video></a>';

                    }
                    ?>
                    <?php
                    echo "<div>$img</div>";
                    echo "<br/>";
                    ?>
                <?php } }?>
        </div>

    </div>
</div>
</body>
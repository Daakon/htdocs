<?php
require 'imports.php';

get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];
?>


<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
preg_match("/[^\/]+$/",$url ,$match);
$username = $match[0];

$profileID = get_id_from_username($username);


$sql = "SELECT * FROM Members
WHERE
Members.ID = '$profileID'
And Members.IsActive = 1 ";

$result = mysql_query($sql) or die(mysql_error());
$rows = mysql_fetch_assoc($result);
$fName = $rows['FirstName'];
$lName = $rows['LastName'];

if (mysql_num_rows($result) == 0) {
    echo '<script>alert("This profile could not be found");location = "/home.php"</script>';
}

?>

<?php
// update profile pic
if (isset($_POST['MakeProfilePhoto']) && $_POST['MakeProfilePhoto'] == "Make Profile Photo") {
    $newProfilePhoto = $_POST['newProfilePhoto'];
    $sql = "Update Profile Set ProfilePhoto = '$newProfilePhoto' Where Member_ID = $ID ";
    mysql_query($sql);
    echo "<script>alert('Profile Photo Updated Successfully');location='/member_media/$username'</script>";
}

// update profile video
if (isset($_POST['MakeProfileVideo']) && $_POST['MakeProfileVideo'] == "Make Profile Video") {
    $mediaName = $_POST['newProfileVideo'];
    $sql = "Update Profile Set ProfileVideo = '$mediaName' Where Member_ID = $ID ";
    mysql_query($sql);

    require 'media_post_file_path.php';
    // poster file name
    $posterName = "poster" . uniqid() . ".jpg";
    //where to save the image
    $poster = "$posterPath$posterName";
    //time to take screenshot at
    //$interval = 3;
    //screenshot size
    //$size = '440x280'; -s $size
    //ffmpeg command

    // create the poster from the file location where this video ALREADY EXISTS
    $cmd = "$ffmpeg -i \"$postMediaFilePath\" -r 1 -ss 3 -t 1  -f image2 $poster 2>&1";
    exec($cmd);

    $sql = "Update Profile Set Poster = '$posterName' Where Member_ID = $ID ";
    mysql_query($sql) or die(mysql_error());

    echo "<script>alert('Profile Video Updated Successfully');location='/member_media/$username'</script>";
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

    <div class="row row-padding" >

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call member-media" >

            <?php require 'profile_menu.php'; ?>

            <h2>Media</h2>

            <?php
            $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            preg_match("/[^\/]+$/",$url ,$match);
            $username = $match[0];

            $profileID = get_id_from_username($username);

            $sql = "SELECT * FROM Media WHERE Member_ID = $profileID And (IsDeleted IS NULL Or IsDeleted = 0)
            Order By ID DESC ";

            $result = mysql_query($sql) or die(mysql_error());
            $count = mysql_num_rows($result);
            ?>

            <div class="option_image" id="showImage" onclick="document.getElementById('photoAlbum').style.display = 'block';"><img src="/images/album.png" height="50" width="50"/>
            <!-- adding onclick to show element #targetid when you click this -->
            View Photo Album
            </div>

            <?php
            if ($_SESSION['PhotoAlbumOpen'] == 'true') { ?>

                <div id="photoAlbum" style="display:block;">

            <?php } else { ?>

            <div id="photoAlbum" style="display:none;">

            <?php } ?>

                <br/>

                <div id="targetid" class="image_one" onclick="document.getElementById('photoAlbum').style.display = 'none';"><img src="/images/close.png" height="50" width="50"/>
                <!-- adding onclick to hide this element when you click it -->
                Close Photo Album
                </div>

            <?php
            while ($rows = mysql_fetch_array($result)) {
                $mediaName = $rows['MediaName'];
                $mediaType = $rows['MediaType'];
                $mediaDate = $rows['MediaDate'];
                $mediaID = $rows['ID'];
                $private = $rows['Private'];
                $mediaFilePath = trim("media/" . $mediaName);
                $posterName = $rows['Poster'];


                // check if file type is a photo
                $videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
                    "video/quicktime", "video/webm", "video/x-matroska",
                    "video/x-ms-wmw");
                $photoFileTypes = array("image/jpg", "image/jpeg", "image/png", "image/tiff",
                    "image/gif", "image/raw");
                $audioFileTypes = array("audio/wav", "audio/mp3");
                $text;

                // photo type
                if (in_array($mediaType, $photoFileTypes)) {
                    $text = "photo";
                    $img = '<a href = "/media.php?id=' . $profileID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '&photoOpen=true" ><br/><img src = "' . $mediaPath . $mediaName . '" class="img-responsive"/></a>';
                    echo "<hr class=\"hr-line\"/>";
                    echo "<div>$img</div>";

                    if (!isProfilePhoto($mediaName) && $ID == $profileID) { ?>

                        <br/>
                        <form method="post" enctype="multipart/form-data" action="" >
                            <input type="hidden" id="newProfilePhoto" name="newProfilePhoto" value="<?php echo $mediaName ?>" />
                            <input type="submit" id="MakeProfilePhoto" name="MakeProfilePhoto" value="Make Profile Photo"/>
                        </form>

                        <?php
                    }
                    ?>



                    <?php
                }

            } ?>

</div>
            <!-------------------------
            End of Videos
            --------------------------->
            <hr class="hr-line" />


            <div class="option_image" id="showImage" onclick="document.getElementById('videoAlbum').style.display = 'block';"><img src="/images/album.png" height="50" width="50"/>
            <!-- adding onclick to show element #targetid when you click this -->
            View Video Album
            </div>

                    <?php
                    if ($_SESSION['VideoAlbumOpen'] == 'true') { ?>

                    <div id="videoAlbum" style="display:block;">

                        <?php } else { ?>

                        <div id="videoAlbum" style="display:none;">

                            <?php } ?>

                <br/>

                <div id="targetid" class="image_one" onclick="document.getElementById('videoAlbum').style.display = 'none';"><img src="/images/close.png" height="50" width="50"/>
                <!-- adding onclick to hide this element when you click it -->
                Close Video Album
                </div>

                <?php

            $sql = "SELECT * FROM Media WHERE Member_ID = $profileID And (IsDeleted IS NULL Or IsDeleted = 0)
                        Order By ID DESC ";

            $result = mysql_query($sql) or die(mysql_error());
            $count = mysql_num_rows($result);

            while ($rows = mysql_fetch_array($result)) {
            $mediaName = $rows['MediaName'];
            $mediaType = $rows['MediaType'];
            $mediaDate = $rows['MediaDate'];
            $mediaID = $rows['ID'];
            $private = $rows['Private'];
            $mediaFilePath = trim("media/" . $mediaName);
            $posterName = $rows['Poster'];


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
                        <a href = "/media.php?id=' . $profileID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '&videoOpen=true" ><br/>More</a><br/><br/>';
                    echo "<hr class=\"hr-line\"/>";
                    echo "<div>$img</div>";

                    if (!isProfileVideo($mediaName) && $ID == $profileID) { ?>

                        <br/>
                        <form method="post" enctype="multipart/form-data" action="" >
                            <input type="hidden" id="newProfileVideo" name="newProfileVideo" value="<?php echo $mediaName ?>" />
                            <input type="submit" id="MakeProfileVideo" name="MakeProfileVideo" value="Make Profile Video"/>
                        </form>

                        <?php
                    }
                    ?>

                <hr class="hr-line"/>

            <?php
            }

            } ?>
            </div>

            <!-------------------------
            End of Videos
            --------------------------->

        </div>

    </div>
</div>

<?php //clear album open session
$_SESSION['PhotoAlbumOpen'] = null;
$_SESSION['VideoAlbumOpen'] = null;
?>


<?php get_footer_files(); ?>
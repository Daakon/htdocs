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

        $content;

        if (strstr($mediaType, "video")) {
            $content = 'video';
        }
        elseif (strstr($mediaType, "image")) {
            $content = 'photo';
        }
        elseif (strstr($mediaType, "audio")) {
            $content = 'song';
        }

        // Setup and send a message
        $text = str_replace(' ', '%20', $link);
        $text = $name .' sent you a '.$content.' from Rapportbook '. $text;
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

    <div class="row row-padding" >

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call member-media" >

            <?php require 'profile_menu.php'; ?>

            <h2>Media</h2>

            <?php
            $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            preg_match("/[^\/]+$/",$url ,$match);
            $username = $match[0];

            $profileID = get_id_from_username($username);

            $sql = "SELECT * FROM Media WHERE Member_ID = '$profileID' And (IsDeleted IS NULL Or IsDeleted = 0)
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
                        <a href = "/media.php?id=' . $profileID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '" ><br/>More</a><br/><br/>';
                    echo "<div>$img</div>";
                    ?>



                    <?php
                    echo "<hr class='hr-line/>";
                    ?>

                    <?php
                }
                // photo type
                if (in_array($mediaType, $photoFileTypes)) {
                    $text = "photo";
                    $img = '<a href = "/media.php?id=' . $profileID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '" ><br/><img src = "' . $mediaPath . $mediaName . '" class="img-responsive"/></a>';
                    echo "<div>$img</div>";
                    ?>

                    <hr class="hr-line"/>

                    <?php
                }
                // audio type
                if (in_array($mediaType, $audioFileTypes)) {
                    $text = "song";
                    $img = '<b>'.$audioName.'</b><br/><audio controls>
                            <source src="'.$mediaPath . $mediaName.'" type="'.$mediaType.'">
                            Your browser does not support the audio element.
                            </audio>
                            <a href = "/media.php?id=' . $profileID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '" ><br/>More</a><br/><br/>';
                    echo "<div>$img</div>";
                    ?>
                    <hr class="hr-line"/>

                    <?php
                }
            } ?>
        </div>

    </div>
</div>

<?php get_footer_files(); ?>
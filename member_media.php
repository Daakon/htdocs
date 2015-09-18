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
    $sql = "SELECT * FROM Members
WHERE
Members.ID = '$ID'
And Members.IsActive = 1 ";

    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);
    $fName = $rows['FirstName'];
    $lName = $rows['LastName'];

    if (mysql_num_rows($result) == 0) {
        echo '<script>alert("This profile could not be found");location = "/member_media.php"</script>';
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

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 ">
            <ul class="list-inline">

                <?php require 'profile_menu.php'; ?>
            </ul>
        </div>

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call" style="margin-right:5px;background-size:100% auto">
            <h2>Video Book</h2>

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


                // check if file type is a photo
                $videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
                    "video/quicktime", "video/webm", "video/x-matroska",
                    "video/x-ms-wmw");


                $text;

                // video type
                if (in_array($mediaType, $videoFileTypes)) {
                    $text = "video";
                    $img = '<a href = "' . $videoPath . $mediaName . '"><video src = "' . $videoPath . $mediaName . '" poster="/poster/'.$posterName.'" preload="auto" controls /></a>
                        <a href = "media.php?id=' . $ID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '" ><br/>More</a><br/>';

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

            } ?>
        </div>

    </div>
</div>
</body>
</html>
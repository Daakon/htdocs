<?php
require 'connect.php';
require 'model_functions.php';
require 'mediapath.php';
require 'getSession.php';
require 'html_functions.php';
require 'findURL.php';
require 'email.php';
require 'category.php';
require 'getState.php';
get_head_files();
get_header();
$ID = $_SESSION['ID'];
?>

<?php

if (isset($_POST['Submit']) && $_POST['Submit'] == "Submit AD") {
    // submit AD
    $adTitle = $_POST['AdTitle'];
    $adText = $_POST['AdText'];
    $ageStart = $_POST['AgeStartDate'];
    $ageEnd = $_POST['AgeEndDate'];



    // if photo is provided
    if (strlen($_FILES['flPostMedia']['name']) > 0) {



        // check file size
        if ($_FILES['flPostMedia']['size'] > 600000000) {

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
                echo "<script>alert('Invalid File Type');</script>";
                header('Location:home.php');
                exit;
            }
        }


        require 'media_post_file_path.php';

// save photo/video
        if (in_array($type, $videoFileTypes)) {
            $cmd = "ffmpeg -i $mediaFile -vf 'transpose=1' $mediaFile";
            exec($cmd);
            move_uploaded_file($mediaFile, $adFilePath);

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
                imagejpeg($src, $adMediaFilePath, 100);
            } else if ($type == "image/png") {
                imagepng($src, $adMediaFilePath, 0, NULL);


            } else if ($type == "image/gif") {
                imagegif($src, $adMediaFilePath, 100);

            } else {
                echo "<script>alert('Invalid File Type');</script>";
                header('Location:ad-manager.php');
                exit;
            }
        }


        // if photo didn't get uploaded, notify the user
        if (!file_exists($adMediaFilePath)) {
            echo "<script>alert('File could not be uploaded, try uploading a different file type.');location='home.php'</script>";
        }
        else {

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
            $mediaType = $mediaRow['MediaType'];
            $mediaDate = $mediaRow['MediaDate'];


            // build post links based on media type
            if (in_array($type, $photoFileTypes)) {

                $img = '<img src = "' . $mediaPath . $mediaName . '" class="img-responsive"/>';
                $img = '<a href = "media.php?id=' . $ID . '&mid=' . $mediaID . '&mediaName=' . $media . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '">' . $img . '</a>';
            } // check if file type is a video
            elseif (in_array($type, $videoFileTypes)) {

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
                $cmd = "$ffmpeg -i \"$adMediaFilePath\" -r 1  -f image2 $poster 2>&1";

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
                $img = '<video src = "' . $videoPath . $mediaName . '" poster="/poster/'.$posterName.'" preload="none" controls />';

            } else {
                // if invalid file type
                echo '<script>alert("Invalid File Type!");</script>';
                header('Location:ad-manager.php');
                exit;
            }

            $adTitle = mysql_real_escape_string($adTitle);
            $ad = mysql_real_escape_string("<h3>" . $adTitle . "</h3>" . $adText . "<br/><br/>" . $img . "<br/>");

            $ad = makeLinks($ad);
            $adText = makeLinks($adText);
            $adTitle = makeLinks($adTitle);

            // insert ad
        $sqlInsertPost = "INSERT INTO DisplayAd (Post,  Member_ID,   MediaSource,         AdTitle,    AdText,        PostDate,    AdEnd ) Values
                                                ('$ad',  '$ID',       '$img',            '$adTitle',  '$adText',    CURDATE(),  ADDDATE(CURDATE(), INTERVAL 30 DAY) ) ";
            mysql_query($sqlInsertPost) or die(mysql_error());
            $newPostID = mysql_insert_id();

            // update Media table with new post id
            if (isset($_SESSION['ID'])) {
                $sqlUpdateMedia = "UPDATE Media SET Post_ID = '$newPostID', Poster='$posterName' WHERE MediaName = '$mediaName' ";
                mysql_query($sqlUpdateMedia) or die(mysql_error());
            }
        }
        // redirect to manage-ad
        echo "<script>alert('Ad Submitted'); location='manage_ad.php?adID=$newPostID'</script>";
    } // if no media
    else {

        // build ad
        $adTitle = mysql_real_escape_string($adTitle);
        $ad = mysql_real_escape_string("<span style='color:red;font-weight:bold;'><h3>" . $adTitle . "</h3>" . $adText);

        $ad = makeLinks($ad);

        // insert ad
        $sqlInsertAd = "INSERT INTO Posts (Post,     Member_ID,     AdTitle,  AdText,        PostDate,    AdEnd ) Values
                                            ('$ad',    '$ID',       '$adTitle',  '$adText',      CURDATE(),  ADDDATE(CURDATE(), INTERVAL 30 DAY) ) ";
        mysql_query($sqlInsertAd) or die(mysql_error());
        $newPostID = mysql_insert_id();
        // redirect to manage-ad
        echo "<script>alert('Ad Submitted'); location='manage_ad.php?adID=$newPostID'</script>";
    }
}
?>

<script>
    function checkAd() {
        var adTitle = document.getElementById('AdTitle').value;
        var adText = document.getElementById('AdText').value;
        var transID = document.getElementById('TransID').value;

        if (adTitle.length == 0) {
            alert('You must provide an Ad Title');
            return false;
        }
        if (adText.length == 0) {
            alert('You must provide Ad Text');
            return false
        }

        if (transID.length < 5) {
            alert('You must provide a Pay Pal Transaction ID');
            return false;
        }
        return true;
    }
</script>

<?php include('media_sizes.html'); ?>

<body>


<div class="containerFlush">


    <div class="col-xs-12 col-md-12 col-lg-12 roll-call">

                <a href="/home">Home</a>
<br/><br/>

            <div class="row" style="padding:10px;padding-left:20px;">

                Need Help? Contact us: <a href="mailto:ads@playdoe.com">ads@playdoe.com</a>



                <?php
                $sql = "SELECT ID, AdTitle, AdStartDate, AdEndDate FROM DisplayAds WHERE Member_ID = $ID ";
                $result = mysql_query($sql) or die(mysql_error());

                if (mysql_numrows($result) > 0) {
                    echo "<h3 style='color:red;'>Manage Existing Ads</h3>";
                    while ($rows = mysql_fetch_assoc($result)) {
                        $adID = $rows['ID'];
                        $adLink = $rows['AdTitle'];
                        $adStart = $rows['AdStartDate'];
                        $adEnd = $rows['AdEndDate'];

                        if ($adEnd < date('Y-m-d H:i:s')) {
                            echo "<a href='manage_ad.php?adID=$adID' style='display:block'>$adLink <span style='color:red;'>(Ad Ended on ". date("l M d Y", strtotime($adEnd)).")</span></a>";
                        }
                        else {
                            echo "<a href='manage_ad.php?adID=$adID' style='display:block'>$adLink</a>";
                        }

                    }
                }
                else {
                    echo "<br/>You currently have not created any ads";
                }
                ?>

                </div>

        <?php
            if (empty($_POST['AdTitle'])) {
                $_POST['AdTitle'] = $_GET['AdTitle'];
            }
            if (empty($_POST['AdText'])) {
                $_POST['AdText'] = $_GET['AdText'];
            }

        ?>

            <hr class="hr-line" style="margin:0px;padding:0px;" />

            <div class="row" style="padding:10px;padding-left:20px;">

                <h3 style="color:green;">Create An Ad </h3>

                <img src="<?php echo $imagesPath ?>bullseye.png" style="margin-top:-50px;margin-bottom:-50px;" />

                <form id="" method="post" enctype="multipart/form-data" action = "" onsubmit="return checkAd()" >

                    <strong>Attach Photo/Video To Your Ad</strong>
                    <input type="file" width="10px;" name="flPostMedia" id="flPostMedia"/>
                    <input type="hidden" name="MAX_FILE_SIZE" value="500000000"

                    <br/><br/>

                    <div class="form-group">
                        <label for="AdTitle">Ad Title</label>
                        <br/>
                        <input type ="text" class="form-control" id="AdTitle" name="AdTitle"  value = "<?php echo $_POST['AdTitle'] ?>" />
                    </div>

                    <div class="form-group">
                        <label for="AdText">Ad Text</label>
                        <br/>
                        <textarea type ="text" class="form-control" id="AdText" name="AdText"><?php echo $_POST['AdText'] ?></textarea>
                    </div>


                    <input type = "submit" value = "Submit AD" name = "Submit" id = "Submit" class="btn btn-default" />
                </form>

                <h3>Get Unlimited AD impressions for just $5 a month</h3>
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
                    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                </form>



            </div>

            </div>

        </div>

<?php get_footer_files() ?>
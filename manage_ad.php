<?php
require 'connect.php';
require 'model_functions.php';
require 'mediaPath.php';
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

if (isset($_POST['Submit']) && $_POST['Submit'] == "Update AD") {

// update ad
    $adID = $_POST['AdID'];
    $adTitle = $_POST['AdTitle'];
    $adText = $_POST['AdText'];
    $adPosition = $_POST['AdPosition'];
    $adCategory = $_POST['AdCategory'];
    $ageStart = $_POST['AgeStart'];
    $ageEnd = $_POST['AgeEnd'];
    $adState = $_POST['AdState'];
    $interests = $_POST['Interests'];
    $mediaExist = $_POST['MediaExist'];

    $talentFeed;
    $rightCol;

    if ($adPosition == '1') {
        $talentFeed = 1;
    } else {
        $rightCol = 1;
    }

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
            $newFileName = $fileName . ".mp4";

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

            } else {
                echo "<script>alert('Invalid File Type');</script>";
                header('Location:ad-manager.php');
                exit;
            }
        }


        // if photo didn't get uploaded, notify the user
        if (!file_exists($postMediaFilePath)) {
            echo "<script>alert('File could not be uploaded, try uploading a different file type.');location='home.php'</script>";
        } else {


            // build post links based on media type
            if (in_array($type, $photoFileTypes)) {

                $img = '<img src = "' . $mediaPath . $mediaName . '" class="img-responsive"/>';
                $img = '<a href = "media.php?id=' . $ID . '&mid=' . $mediaID . '&mediaName=' . $media . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '">' . $img . '</a>';
            } // check if file type is a video
            elseif (in_array($type, $videoFileTypes)) {

                // where ffmpeg is located
                $ffmpeg = '/usr/bin/ffmpeg';

                // poster file name
                $posterName = "poster" . uniqid() . ".jpg";

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

                if (isset($exif['Orientation']) && !empty($exif['Orientation'])) {

                    // Decide orientation
                    if ($exif['Orientation'] == 3) {
                        $rotation = 180;
                    } else if ($exif['Orientation'] == 6) {
                        $rotation = 90;
                    } else if ($exif['Orientation'] == 8) {
                        $rotation = -90;
                    } else {
                        $rotation = 0;
                    }

                    // Rotate the image
                    if ($rotation) {
                        $img = imagerotate($poster, $rotation, 0);
                        imagejpeg($img, $posterPath . $posterName, 100);
                    }
                } else {
                    // if we cannot determine the exif data
                    // then we will rotate the image if it is wider than it is tall
                    // this is the best fallback so far.
                    $size = getimagesize("$posterPath$posterName");
                    $width = $size[0];
                    $height = $size[1];
                    if ($width > $height) {
                        $img = imagerotate($poster, -90, 0);
                        imagejpeg($img, $posterPath . $posterName, 100);
                    }
                }
                $img = '<video src = "' . $videoPath . $mediaName . '" poster="/poster/' . $posterName . '" preload="none" controls />';

            } else {
                // if invalid file type
                echo '<script>alert("Invalid File Type!");</script>';
                header('Location:ad-manager.php');
                exit;
            }

            $adTitle = mysql_real_escape_string($adTitle);
            $ad = mysql_real_escape_string("<span style='color:red;font-weight:bold;'><h3>" . $adTitle . "</h3>" . $adText . "<br/><br/>" . $img . "<br/>");

            $ad = makeLinks($ad);
            $adText = makeLinks($adText);
            $adTitle = makeLinks($adTitle);

            // update ad
            $sqlUpdatePost = "Update Posts SET
              Post = '$ad',  AdTitle = '$adTitle',
              AdText = '$adText',
              MediaSource = '$img',
              Interests = '$interests',
              TalentFeed = '$talentFeed',
              RightColumn =  '$rightCol',
              AgeStart = '$ageStart',
              AgeEnd = '$ageEnd',
              AdState = '$adState',
              AdCategory =  '$adCategory'
              WHERE ID = '$adID' ";
            mysql_query($sqlUpdatePost) or die(mysql_error());

            // update Media table with new image
            if (isset($_SESSION['ID'])) {
                $sqlUpdateMedia = "UPDATE Media SET Poster='$posterName', MediaName='$mediaName' WHERE Post_ID = $adID ";
                mysql_query($sqlUpdateMedia) or die(mysql_error());
            }
        }
        // redirect to manage-ad
        echo "<script>alert('Ad Updated'); location='manage_ad.php?adID=$adID'</script>";
    } // if no media
    else {

        // build ad
        if ($mediaExist == 1) {
            $sql = "SELECT MediaSource FROM Posts WHERE ID = $adID ";
            $result = mysql_query($sql) or die(mysql_error());
            $rows = mysql_fetch_assoc($result);
            $mediaSrc = $rows['MediaSource'];

            $adTitle = mysql_real_escape_string($adTitle);
            $ad = mysql_real_escape_string("<h3>" . $adTitle . "</h3>" . $adText . "<br/><br/>" . $mediaSrc . "<br/>");
            $ad = makeLinks($ad);
            $adText = makeLinks($adText);
            $adTitle = makeLinks($adTitle);
        }
        else {
            $adTitle = mysql_real_escape_string($adTitle);
            $ad = mysql_real_escape_string("<h3>" . $adTitle . "</h3>" . $adText);
            $ad = makeLinks($ad);
            $adText = makeLinks($adText);
            $adTitle = makeLinks($adTitle);
        }

        // update ad
        $sqlUpdatePost = "Update Posts SET
              Post = '$ad',  AdTitle = '$adTitle',
              AdText = '$adText',
              Interests = '$interests',
              TalentFeed = '$talentFeed',
              RightColumn =  '$rightCol',
              AgeStart = '$ageStart',
              AgeEnd = '$ageEnd',
              AdState = '$adState',
              AdCategory =  '$adCategory'
              WHERE ID = '$adID' ";
        mysql_query($sqlUpdatePost) or die(mysql_error());

        echo "<script>alert('Ad Updated');location='manage_ad.php?adID=$adID'</script>";
    }
}
?>

<?php include('media_sizes.html'); ?>

    <script>
        function checkAd() {
            var adTitle = document.getElementById('AdTitle').value;
            var adText = document.getElementById('AdText').value;

            if (adTitle.length == 0) {
                alert('You must provide an Ad Title');
                return false;
            }
            if (adText.length == 0) {
                alert('You must provide Ad Text');
                return false
            }
            return true;
        }
    </script>

    <body>

<div class="container">


    <div class="col-xs-12 col-md-10 col-lg-10 col-md-offset-2 roll-call">

        <a href="/home.php">Back to Roll Call</a>
        <br/><br/>

        <div class="row" style="padding:10px;">

            Need Help? Contact us: <a href="mailto:ads@rapportbook.com">ads@rapportbook.com</a>

            <br/><br/>
            <a href="/ad-manager.php">Back to Ad Manager</a>

            <?php
            $adID = $_GET['adID'];
            $sql = "SELECT * FROM Posts WHERE ID = $adID ";
            $result = mysql_query($sql) or die(mysql_error());

            $rows = mysql_fetch_assoc($result);
            $adID = $rows['ID'];
            $adTitle = $rows['AdTitle'];
            $adText = $rows['AdText'];
            $mediaSrc = $rows['MediaSource'];
            $talentFeed = $rows['TalentFeed'];
            $rightColumn = $rows['RightColumn'];
            $adCategory = $rows['AdCategory'];
            $ageStart = $rows['AgeStart'];
            $ageEnd = $rows['AgeEnd'];
            $adState = $rows['AdState'];
            $interests = $rows['Interests'];
            $transID = $rows['TransID'];
            $impressions = $rows['Impressions'];
            $adEnd = $rows['AdEnd'];
            ?>

        </div>


        <div class="row" style="padding:10px;">
<?php


            $mediaExist = 0;
            if (strlen($mediaSrc) > 0) {
                $mediaExist = 1;
                echo $mediaSrc;
            }

             ?>

            <form id="" method="post" enctype="multipart/form-data" action = "" onsubmit="return checkAd()">
                <input type="hidden" name="AdID" id="AdID" value="<?php echo $adID ?>" />
                <input type="hidden" name="MediaExist" id="MediaExist" value="<?php echo $mediaExist ?>" />

                <strong>Update your ad Photo/Video</strong>
                <input type="file" width="10px;" name="flPostMedia" id="flPostMedia"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="500000000"

                       <br/><br/>

                <div class="form-group">
                    <label for="AdTitle">Ad Title</label>
                    <br/>
                    <input type ="text" class="form-control" id="AdTitle" name="AdTitle"  value = "<?php echo $adTitle ?>" />
                </div>



                <div class="form-group">
                    <label for="AdText">Ad Text</label>
                    <br/>
                    <textarea type ="text" class="form-control" id="AdText" name="AdText" class="textArea"><?php echo $adText ?></textarea>
                </div>

                <?php
                $check0 = $check1 = "";
                if ($talentFeed == 1) { $check0 = "CHECKED"; } else { $check1 = "CHECKED"; }
                ?>

                <div class="form-group">
                    <label for="AdPosition">Ad Position</label>
                    <br/>
                    Talent Feed&nbsp;<input type="radio" name="AdPosition" id="AdPosition" value="1"<?php echo $check0 ?>>
                    <br/>
                    Right Column&nbsp;<input type="radio" name="AdPosition" id="AdPosition" value="0"<?php echo $check1 ?>>
                </div>

                <br/>

                <h3>Demographics</h3>

                <br/>

                <div class="form-group">
                    <label for="AdTitle">Age Group (Optional)</label>
                    <br/>
                    <select id="AgeStart" name="AgeStart">
                        <option value="<?php echo $ageStart ?>"><?php echo $ageStart ?></option>
                        <?php age() ?>
                    </select>
                    To
                    <select id="AgeEnd" name="AgeEnd">
                        <option value="<?php echo $ageEnd ?>"><?php echo $ageEnd ?></option>
                        <?php age() ?>
                    </select>
                </div>

                <br/>

                <label for="AdTitle">State (Optional)</label>
                <select id="AdState" name="AdState"  class="form-control">
                    <option value="<?php echo $adState ?>"><?php echo $adState ?></option>
                    <?php getState() ?>
                </select>

                <br/>

                <div class="form-group">
                    <label for="Interests">Interests (Optional - <span style="font-style:italic;color:red;">Add up to 5 Interests</span>)</label>
                    <br/>
                    <input type ="text" class="form-control" id="Interests" name="Interests" value = "<?php echo $interests ?>"  />
                </div>

                <label for="AdTitle">Ad Category (Optional)</label>
                <select class="form-control input-lg" id="AdCategory" name="AdCategory">
                    <option value=""><?php echo $adCategory ?></option>
                    <?php echo category() ?>
                </select>

                <br/>

                <label for="TransID">PayPal Transaction ID</label>
                <br/>
                <input type ="text" class="form-control" id="TransID" name="TransID" value="<?php echo $transID ?>" readonly />
                <small>Please complete the PayPal process first, then provide the Transaction ID to submit your ad:</small>

                <br/><br/>

                <input type = "submit" value = "Update AD" name = "Submit" id = "Submit" class="btn btn-default" />
            </form>

            <?php
            if (date('Y-m-d H:i:s') < $adEnd) { ?>
            <h3>This ad expires on <?php echo date("l M d Y", strtotime($adEnd)); ?></h3>
            <?php } else { ?>
            <h3>This ad expired on <?php echo date("l M d Y", strtotime($adEnd)); ?></h3>
            <br/>
                <a href="/ad-manager.php?AdTitle=<?php echo $adTitle ?>&AdText=<?php echo $adText ?>&AgeStart=<?php echo $ageStart ?>&AgeEnd=<?php echo $ageEnd ?>&AdState=<?php echo $adState ?>&AdCategory=<?php echo $adCategory ?>&Interests=<?php echo $interests ?>">Rerun Ad</a>
            <?php } ?>

        </div>

    </div>

</div>

<?php get_footer_files() ?>
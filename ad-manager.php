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

if (isset($_POST['Submit']) && $_POST['Submit'] == "Submit AD") {
    // submit AD
    $adTitle = $_POST['AdTitle'];
    $adText = $_POST['AdText'];
    $adPosition = $_POST['AdPosition'];
    $ageStart = $_POST['AgeStart'];
    $ageEnd = $_POST['AgeEnd'];
    $state = $_POST['AdState'];
    $interests = mysql_real_escape_string($_POST['Interests']);
    $adCategory = $_POST['AdCategory'];
    $transID = $_POST['TransID'];

    // check transaction ID
    /*if (strlen($transID) == 0 || strlen($transID) == "") {
        echo "<script>alert('Your ad must have a PayPal transaction ID to be sumbitted');</script>";
        exit;
    }*/

    $talentFeed;
    $rightCol;

    if ($adPosition == '1') {
        $talentFeed = 1;
    }
    else {
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
                $cmd = "$ffmpeg -i \"$postMediaFilePath\" -r 1  -f image2 $poster 2>&1";

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
            $ad = mysql_real_escape_string("<span style='color:red;font-weight:bold;'><h3>" . $adTitle . "</h3>" . $adText . "<br/><br/>" . $img . "<br/>");
            echo "<script>alert('$ad');</script>";
            // insert ad
            $sqlInsertPost = "INSERT INTO Posts (Post,     Member_ID,   Category,   AdTitle,    AdText,   Interests,    TalentFeed,     RightColumn,   AgeStart,    AgeEnd,    AdState,    AdCategory,    TransID,   PostDate,    AdEnd ) Values
                                                ('$ad',    '$ID',      'Sponsored', '$adTitle', '$adText', '$interests', '$talentFeed',  '$rightCol', '$ageStart', '$ageEnd', '$state', '$adCategory', '$transID',   CURDATE(),  ADDDATE(CURDATE(), INTERVAL 30 DAY) ) ";
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


        // insert ad
        $sqlInsertPost = "INSERT INTO Posts (Post,     Member_ID,   Category,   AdTitle,    AdText,   Interests,    TalentFeed,     RightColumn,   AgeStart,    AgeEnd,    AdState,    AdCategory,    TransID,   PostDate,    AdEnd ) Values
                                        ('$ad',    '$ID',      'Sponsored', '$adTitle', '$adText', '$interests', '$talentFeed',  '$rightCol', '$ageStart', '$ageEnd', '$state', '$adCategory', '$transID',   CURDATE(),  ADDDATE(CURDATE(), INTERVAL 30 DAY) ) ";
        mysql_query($sqlInsertPost) or die(mysql_error());
        $newPostID = mysql_insert_id();
        // redirect to manage-ad
        echo "<script>alert('Ad Submitted'); location='manage_ad.php?adID=$newPostID'</script>";
    }





}
?>

<body>


<div class="container">


    <div class="col-xs-12 col-md-10 col-lg-10 col-md-offset-2 roll-call">

                <a href="/home.php">Back to Roll Call</a>
<br/><br/>

            <div class="row" style="padding:10px;">

                Need Help? Contact us: <a href="mailto:ads@rapportbook.com">ads@rapportbook.com</a>

            <h3 style="color:red;">Manage Existing Ads</h3>

                <?php
                $sql = "SELECT ID, AdTitle, AdEnd FROM Posts WHERE Member_ID = $ID ";
                $result = mysql_query($sql) or die(mysql_error());

                if (mysql_numrows($result) > 0) {
                    while ($rows = mysql_fetch_assoc($result)) {
                        $adID = $rows['ID'];
                        $adLink = $rows['AdTitle'];
                        $adEnd = $rows['AdEnd'];

                        if ($adEnd > date('now')) {
                            echo "<a href='manage_ad.php?adID=$adID' style='display:block'>$adLink (Ad Over)</a>";
                        }
                        else {
                            echo "<a href='manage_ad.php?adID=$adID' style='display:block'>$adLink</a>";
                        }

                    }
                }
                else {
                    echo "You currently have not created any ads";
                }
                ?>

                </div>


            <div class="row" style="padding:10px;">

                <h3 style="color:green;">Create An Ad </h3>

                <img src="<?php echo $images ?>bullseye.png" style="margin-top:-50px;margin-bottom:-50px;" />

                <form id="" method="post" enctype="multipart/form-data" action = "" >

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
                        <input type ="text" class="form-control" id="AdText" name="AdText" value = "<?php echo $_POST['AdText'] ?>"  />
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
                            <option value="<?php echo $_POST['AgeStart'] ?>"><?php echo $_POST['AgeStart'] ?></option>
                            <?php age() ?>
                        </select>
                        To
                        <select id="AgeEnd" name="AgeEnd">
                            <option value="<?php echo $_POST['AgeEnd'] ?>"><?php echo $_POST['AgeEnd'] ?></option>
                            <?php age() ?>
                        </select>
                    </div>

                    <br/>

                    <label for="AdTitle">State (Optional)</label>
                        <select id="AdState" name="AdState"  class="form-control">
                            <option value="<?php echo $_POST['AdState'] ?>"><?php echo $_POST['AdState'] ?></option>
                            <?php getState() ?>
                        </select>

                    <br/>

                    <div class="form-group">
                        <label for="AdTitle">Interests (Optional)</label>
                        <br/>
                        <input type ="text" class="form-control" id="Interests" name="Interests" value = "<?php echo $_POST['Interests'] ?>"  />
                    </div>

                    <label for="AdTitle">Ad Category (Optional)</label>
                    <select class="form-control input-lg" id="AdCategory" name="AdCategory">
                        <option value="<?php echo $_POST['AdCategory'] ?>"><?php echo $_POST['AdCategory'] ?></option>
                        <?php echo category() ?>
                    </select>

                    <br/>

                    <label for="TransID">PayPal Transaction ID</label>
                    <br/>
                    <input type ="text" class="form-control" id="TransID" name="TransID" />
                    <small>Please complete the PayPal process first, then provide the Transaction ID to submit your ad:</small>

                    <br/><br/>

                    <input type = "submit" value = "Submit AD" name = "Submit" id = "Submit" class="btn btn-default" />
                </form>

                <h3>Get Unlimited AD impressions for just $10 a month</h3>
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
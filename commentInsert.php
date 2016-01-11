<?php

function insertComment($postMediaFilePath, $mediaPath, $posterPath, $videoPath, $oggFileName, $webmFileName, $ID, $url)
{
//-------------------------------------------------
// handle post comments
//-------------------------------------------------



    if (isset($_POST['btnComment']) && ($_POST['btnComment'] == "Comment")) {
        $postID = $_POST['postID'];
        $ownerId = $_POST['memberID'];
        $comment = $_POST['postComment'];
        $comment = mysql_real_escape_string($comment);
        if (strlen($comment) > 0) {
// find urls
            $comment = makeLinks($comment);
            if ($_SESSION['PostComment'] == $_POST['postComment']) {
                echo "<script>alert('Your comment appears to be empty');</script>";
            } else {
// if photo is provided
                if (isset($_FILES['flPostMedia']) && strlen($_FILES['flPostMedia']['name']) > 1) {
// check file size
                    if ($_FILES['flPostMedia']['size'] > 25000000000) {
                        echo '<script>alert("File is too large. The maximum file size is 50MB.");</script>';
                        header('Location:home.php');
                        exit;
                    }
// check if file type is a photo
                    $videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
                        "video/quicktime", "video/webm", "video/x-matroska",
                        "video/x-ms-wmw");
// video file types
                    $photoFileTypes = array("image/jpg", "image/jpeg", "image/png", "image/tiff",
                        "image/gif", "image/raw");
                    $audioFileTypes = array("audio/wav", "audio/mp3");

                    $mediaName = $_FILES["flPostMedia"]["name"];
                    // remove ALL WHITESPACE from image name
                    $mediaName = preg_replace('/\s+/', '', $mediaName);
                    $mediaName = str_replace('/[^A-Za-z0-9\-]/', '', $mediaName);
                    $fileName = pathinfo($mediaName, PATHINFO_FILENAME);
                    // add unique id to image name to make it unique and add it to the file server
                    $mediaName = trim(uniqid() . $mediaName);
                    $mediaFile = $_FILES['flPostMedia']['tmp_name'];
                    $type = trim($_FILES["flPostMedia"]["type"]);
                    require 'media_post_file_path.php';
                    // create file type instance
                    if (in_array($type, $audioFileTypes) || in_array($type, $videoFileTypes)) {
                        $audioName = $fileName;
                    }
                    if (in_array($type, $videoFileTypes)) {
                        // convert to mp4
                        $newFileName = $fileName . ".mp4";
                        $audioName = $fileName;
                        $ffmpeg = '/usr/local/bin/ffmpeg';
                        exec("$ffmpeg -i $newFileName -vcodec libx264 -pix_fmt yuv420p -profile:v baseline -preset slow -crf 22 -movflags +faststart $newFileName");
                        $mediaName = $newFileName;
                    } else {
                        $mediaString = 'photo';
                        if ($type == "image/jpg" || $type == "image/jpeg") {
                            $src = imagecreatefromjpeg($mediaFile);
                        } else if ($type == "image/png") {
                            $src = imagecreatefrompng($mediaFile);
                        } else if ($type == "image/gif") {
                            // must save gifs as jpeg
                            $src = imagecreatefromjpeg($mediaFile);
                        } else {
                            /*echo "<script>alert('Invalid File Type');</script>";
                            header('Location:home.php');
                            exit;*/
                        }
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
// save photo/video
                    require 'media_post_file_path.php';
                    if (in_array($type, $videoFileTypes) || in_array($type, $audioFileTypes)) {
                        move_uploaded_file($mediaFile, $postMediaFilePath);
                    } else {
                        // handle transparency
                        imagesavealpha($src, true);
                        if ($type == "image/jpg" || $type == "image/jpeg") {
                            imagejpeg($src, $postMediaFilePath, 50);
                        } else if ($type == "image/png") {
                            imagepng($src, $postMediaFilePath, 0, NULL);
                        } else {
                            imagegif($src, $postMediaFilePath, 50);
                        }
                    }
// if photo didn't get uploaded, notify the user
                    if (!file_exists($postMediaFilePath)) {
                        echo "<script>alert('File could not be uploaded, try uploading a different file type.');</script>";
                        header('Location:home.php');
                    } else {
                        // determine which table to put photo pointer in
                        // store media pointer
                        $sql = "INSERT INTO Media (Member_ID,  MediaName,  MediaType,  MediaDate,     AudioName    ) Values
                                              ('$ID',    '$mediaName', '$type',   CURRENT_DATE(), '$audioName')";
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
                        if (in_array($type, $audioFileTypes)) {
                            $img = '<b>' . $audioName . '</b><br/><audio controls>
                            <source src="' . $mediaPath . $mediaName . '" type="' . $mediaType . '">
                            Your browser does not support the audio element.
                            </audio>';
                            $img = '<a href = "media.php?id=' . $ID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '" ><br/>' . $img . '</a><br/><br/>';
                        }
                        if (in_array($type, $photoFileTypes)) {
                            $img = '<img src = "' . $mediaPath . $mediaName . '" />';
                            $img = '<a href = "media?id=' . $ID . '&mid=' . $mediaID . '&mediaName=' . $media . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '">' . $img . '</a>';
                        } // check if file type is a video
                        elseif (in_array($type, $videoFileTypes)) {
                            // where ffmpeg is located
                            $ffmpeg = '/usr/local/bin/ffmpeg';
                            // poster file name
                            $posterName = "poster" . uniqid() . ".jpg";
                            //where to save the image
                            $poster = "$posterPath$posterName";
                            //time to take screenshot at
                            $interval = 3;
                            //screenshot size
                            //$size = '440x280'; -s $size
                            //ffmpeg command
                            $cmd = "$ffmpeg -i \"$postMediaFilePath\" -r 1 -ss 3 -t 1  -f image2 $poster 2>&1";
                            exec($cmd);

                            $img = '<video poster="/poster/' . $posterName . '" preload="none" autoplay="autoplay" muted controls>
                                <source src = "' . $videoPath . $mediaName . '" type="video/mp4" />
                                <source src = "' . $videoPath . $oggFileName . '" type = "video/ogg" />
                                <source src = "' . $videoPath . $webmFileName . '" type = "video/webm" />
                                </video>';

                        } else {
                            // if invalid file type
                            /*echo '<script>alert("Invalid File Type!");</script>';
                            header('Location:home.php');
                            exit; */
                        }
                        $comment = $comment . '<br/><br/>' . $img . '<br/>';


                        $sql = "INSERT INTO PostComments (Post_ID,     Member_ID,   Comment  ) Values
                                                      ('$postID', '$ID',      '$comment')";
                        mysql_query($sql) or die(mysql_error());

                        if (strstr($url, "home")) {
// create post
                            // get poster data
                            $sqlPoster = "SELECT ID, FirstName, LastName, Gender FROM Members WHERE ID = '$ID' ";
                            $resultPoster = mysql_query($sqlPoster) or die(mysql_error());
                            $rowsPoster = mysql_fetch_assoc($resultPoster);
                            $name = $rowsPoster['FirstName'] . ' ' . $rowsPoster['LastName'];
                            $posterId = $rowsPoster['ID'];
                            $gender = $rowsPoster['Gender'];
                            $nameLink = $name;
// get photo owner data
                            $sql = "SELECT Member_ID FROM Posts WHERE ID = $postID";
                            $result = mysql_query($sql) or die(mysql_error());
                            $rows = mysql_fetch_assoc($result);
                            $ownerId = $rows['Member_ID'];
                            $sqlOwner = "SELECT ID, FirstName, LastName FROM Members WHERE ID = '$ownerId' ";
                            $resultOwner = mysql_query($sqlOwner) or die(mysql_error());
                            $rowsOwner = mysql_fetch_assoc($resultOwner);
                            $name2 = $rowsOwner['FirstName'] . ' ' . $rowsOwner['LastName'];
                            $name2 = $name2 . "'s";
                            $ownerId = $rowsOwner['ID'];
                            $name2Link = $name2;
                            // determine noun if profile owner commented on their own post and write bulletin
                            if ($ownerId == $ID) {
                                if ($gender == 1) {
                                    $noun = 'his';
                                } else {
                                    $noun = 'her';
                                }
                            } else {
                                $noun = $name2;
                            }

                            $orgPost = "<a href='/show_post.php?postID=$postID'>post</a>";
                            $orgPostSql = "SELECT Category FROM Posts WHERE ID = $postID ";
                            $orgPostResult = mysql_query($orgPostSql);
                            $orgPostRow = mysql_fetch_assoc($orgPostResult);
                            $orgInterest = $orgPostRow['Category'];

                            $post = "$nameLink posted a new $mediaString comment on $noun $orgPost.<br/><br/>$img<br/>";
                            $post = mysql_real_escape_string($post);
                            $sqlInsertPost = "INSERT INTO Posts (Post,     Member_ID,   Category,         PostDate  ) Values
                                                        ('$post', '$ID',      '$orgInterest',    CURDATE() ) ";
                            mysql_query($sqlInsertPost) or die(mysql_error());
                            $newPostID = mysql_insert_id();
// update new photo with bulletin id for commenting later
                            $sql = "UPDATE Media SET Post_ID = '$newPostID' WHERE MediaName = '$mediaName' ";
                            mysql_query($sql) or die(mysql_error());
                        }

                    }
                }
//----------------------
// if not comment photo
//----------------------
                else {
                    $sql = "INSERT INTO PostComments (Post_ID,  Member_ID,    Comment ) Values
                                        ('$postID', '$ID',      '$comment')";
                    mysql_query($sql) or die(mysql_error());
                }
                $scrollx = $_REQUEST['scrollx'];
                $scrolly = $_REQUEST['scrolly'];
//A comment was just made, we need to send out some notifications.
//The first thing is to identify all of the id's connected with this post
                $user_id = $_SESSION['ID'];
//Get the ids of all the members connected with a post comment
                $sql = "SELECT Member_ID FROM PostComments WHERE Post_ID = $postID And Member_ID != $ID ";
                $result = mysql_query($sql) or die(mysql_error());
                $comment_ids = array();
//Iterate over the results
                while ($rows = mysql_fetch_assoc($result)) {
                    array_push($comment_ids, $rows['Member_ID']);
                }
//Boil the id's down to unique values because we dont want to send double emails or notifications
                $comment_ids = array_unique($comment_ids);
//Send consumer notifications
                foreach ($comment_ids as $item) {
                    if (strlen($item) > 0) {
                        // only send email if account & email active
                        if (checkActive($item)) {
                            if (checkEmailActive($item)) {
                                build_and_send_email($user_id, $item, 1, $postID);
                            }
                        }
                    }
                }
//Notify the post creator

                $sql = "SELECT Member_ID FROM Posts WHERE ID = $postID And Member_ID != $ID ";
                $result = mysql_query($sql) or die(mysql_error());
                $rows = mysql_fetch_assoc($result);
                $creatorID = $rows['Member_ID'];
                if ($ID != $creatorID) {
                    if (checkEmailActive($ID)) {
                        build_and_send_email($ID, $creatorID, 1, $postID, '');
                    }
                }
//------------------
//=========================================================================================================================//
//BELOW IS END OF POST COMMENT HANDLING CODE ==========================================================================//
            }
        }
        return true;
    }
}


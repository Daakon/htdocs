<?php
require 'connect.php';
require 'model_functions.php';
require 'mediaPath.php';
require 'getSession.php';
require 'html_functions.php';

require 'findURL.php';

require 'email.php';
require 'category.php';

get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];

// handle roll call post

$post = mysql_real_escape_string($_POST['post']);
$category = $_POST['category'];

if (isset($_POST['submit'])) {

    if ($_SESSION['Post'] == $_POST['post']) {
        echo "<script>alert('Your post appears to be empty');</script>";
    }
    else if ($category == "") {
        echo "<script>alert('Your post needs a category');</script>";
    }

    else {
        if (strlen($post) > 0) {


            $post = makeLinks($post);

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
                        header('Location:home.php');
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
                        header('Location:home.php');
                        exit;
                    }

                    $post = $post . '<br/><br/>' . $img . '<br/>';

                    $sql = "INSERT INTO Posts (Post,    Poster,	      Category,  Member_ID,   PostDate) Values
                                              ('$post', '$posterName', '$category', '$ID',       CURDATE())";
                    mysql_query($sql) or die(mysql_error());
                    $newPostID = mysql_insert_id();

                    // update Media table with new post id
                    if (isset($_SESSION['ID'])) {
                        $sqlUpdateMedia = "UPDATE Media SET Post_ID = '$newPostID', Poster='$posterName' WHERE MediaName = '$mediaName' ";
                        mysql_query($sqlUpdateMedia) or die(mysql_error());
                    }
                }
            } // if no media
            else {

                $sql = "INSERT INTO Posts (Post,       Category,    Member_ID,   PostDate) Values
                                          ('$post',   '$category',   '$ID',      CURDATE())";
                mysql_query($sql) or die(mysql_error());

            }
        }

    }
    echo '<meta http-equiv="Location" content="/home.php">';
}

?>


<?php

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
                if ($_FILES['flPostMedia']['size'] > 50000000) {
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

                // add unique id to image name to make it unique and add it to the file server
                $mediaName = $_FILES["flPostMedia"]["name"];
                $mediaName = trim(uniqid() . $mediaName);
                $mediaFile = $_FILES['flPostMedia']['tmp_name'];
                $type = trim($_FILES["flPostMedia"]["type"]);

                require 'media_post_file_path.php';

                // create file type instance
                if (in_array($type, $videoFileTypes)) {
                    // convert to mp4
                    $fileName = pathinfo($mediaName, PATHINFO_FILENAME);
                    $newFileName = $fileName.".mp4";
                    exec("ffmpeg -i $newFileName -vcodec libx264 -pix_fmt yuv420p -profile:v baseline -preset slow -crf 22 -movflags +faststart $newFileName");
                    $mediaName = $newFileName;
                } else {

                    $mediaString = 'photo';
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
                if (in_array($type, $videoFileTypes)) {
                    move_uploaded_file($mediaFile, $postMediaFilePath);
                } else {
                    // handle transparency
                    imagesavealpha($src, true);
                    if ($type == "image/jpg" || $type == "image/jpeg") {
                        imagejpeg($src, $postMediaFilePath, 100);

                    } else if ($type == "image/png") {
                        imagepng($src, $postMediaFilePath, 0, NULL);

                    } else {
                        imagegif($src, $postMediaFilePath, 100);

                    }
                }

// if photo didn't get uploaded, notify the user
                if (!file_exists($postMediaFilePath)) {
                    echo "<script>alert('File could not be uploaded, try uploading a different file type.');</script>";
                    header('Location:home.php');
                } else {

                    // determine which table to put photo pointer in
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


// check if file type is a photo
                    if (in_array($type, $photoFileTypes)) {

                        $img = '<img src = "' . $mediaPath . $mediaName .'" class="img-responsive"/>';
                        $img = '<a href = "media.php?id=' . $ID . '&mid=' . $mediaID . '&mediaName=' . $media . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '">' . $img . '</a>';
                    } // check if file type is a video
                    elseif (in_array($type, $videoFileTypes)) {
                        //$img = '<a href = "' . $videoPath . $mediaName . '"><video src = "' . $videoPath . $mediaName . '" poster="'.$images.'video-bg.jpg" preload="auto" controsl /></a>';
                        $img = '<video src = "' . $videoPath . $mediaName . '" poster="/media/shot.jpg" preload="auto" controls />';
                    } else {
                        // if invalid file type
                        echo '<script>alert("Invalid File Type!");</script>';
                        header('Location:home.php');
                        exit;
                    }

                    $comment = $comment . '<br/><br/>' . $img . '<br/>';

                    $sql = "INSERT INTO PostComments (Post_ID,     Member_ID,   Comment  ) Values
                                                      ('$postID', '$ID',      '$comment')";

                    mysql_query($sql) or die(mysql_error());

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
                    $name2 = $name2."'s";
                    $ownerId = $rowsOwner['ID'];
                    $name2Link = $name2;

                    // determine noun if profile owner commented on their own post and write bulletin

                    if ($ownerId == $ID) {
                        if ($gender == 1) {
                            $noun = 'his';
                        } else {
                            $noun = 'her';
                        }
                    }
                    else {

                        $noun = $name2;
                    }

                    $post = "$nameLink posted a new $mediaString comment on $noun post.<br/><br/>$img<br/>";
                    $post = mysql_real_escape_string($post);

                    $sqlInsertPost = "INSERT INTO Posts (Post,     Member_ID,    PostDate  ) Values
                                                ('$post', '$ID',        CURDATE() ) ";
                    mysql_query($sqlInsertPost) or die(mysql_error());
                    $newPostID = mysql_insert_id();

// update new photo with bulletin id for commenting later

                    $sql = "UPDATE Media SET Post_ID = '$newPostID' WHERE MediaName = '$mediaName' ";
                    mysql_query($sql) or die(mysql_error());

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
            $sql = "SELECT Member_ID FROM PostComments WHERE Post_ID = $postID ";

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

            $sql = "SELECT Member_ID FROM Posts WHERE ID = '$postID';";

            $result = mysql_query($sql) or die(mysql_error());
            $rows = mysql_fetch_assoc($result);
            $creatorID = $rows['Member_ID'];

            if (checkEmailActive($ID)) {
                build_and_send_email($ID, $creatorID, 1, $postID, '');
            }

//------------------

//=========================================================================================================================//
//BELOW IS END OF POST COMMENT HANDLING CODE ==========================================================================//

        }
    }
    echo '<meta http-equiv="Location" content="/home.php">';
}

if (isset($_POST['DeleteComment']) && $_POST['DeleteComment'] == "Delete") {
    $commentID = $_POST['commentID'];
    $sql = "Update PostComments SET IsDeleted = '1' WHERE ID = $commentID";
    mysql_query($sql) or die (mysql_error());

}

?>

<?php include('media_sizes.html'); ?>


<script type="text/javascript">
    function saveScrollPositions(theForm) {
        if(theForm) {
            var scrolly = typeof window.pageYOffset != 'undefined' ? window.pageYOffset : document.documentElement.scrollTop;
            var scrollx = typeof window.pageXOffset != 'undefined' ? window.pageXOffset : document.documentElement.scrollLeft;
            theForm.scrollx.value = scrollx;
            theForm.scrolly.value = scrolly;
        }
    }
</script>


<script>
    $(document).ready(function () {
        $("body").delegate(".btnApprove", "click", function () {
            var parentDiv = $(this).closest("div[id^=approvals]");
            var data = {
                postID: $(this).closest('div').find('.postID').val(),
                ID: $(this).closest('div').find('.ID').val()
                //add other properties similarly
            }

            $.ajax({
                type: "post",
                url: "/post_approve.php",
                data: data,
                success: function (data) {
                    parentDiv.html(data);
                }

            })
        });
    });
</script>

<script>
    $(document).ready(function () {
        $("body").delegate(".btnDisapprove", "click", function () {
            var parentDiv = $(this).closest("div[id^=approvals]");
            var data = {
                postID: $(this).closest('div').find('.postID').val(),
                ID: $(this).closest('div').find('.ID').val()
                //add other properties similarly
            }
            $.ajax({
                type: "post",
                url: "/post_disapprove.php",
                data: data,
                success: function (data) {
                    parentDiv.html(data);
                }

            })
        });
    });
</script>

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

<script type="text/javascript">

    function showComments(id) {
        var e = document.getElementById(id);
        if (e.style.display == 'none') {
            e.style.display = 'block';
        }
        else
            e.style.display = 'none';
    }

</script>


<script>
    // show uploading
    function showUploading() {
        document.getElementById("progress").style.display = "block";
    }
</script>

<script>
    // show comment uploading
    function showCommentUploading(comment, theForm) {
        document.getElementById(comment).style.display = "block";

        saveScrollPositions(theForm);
    }
</script>

<script type = "text/javascript">
            function getGenre() {
               var selection = document.getElementById('genre');
               var genre = selection.options[selection.selectedIndex].value;
               window.location = "/home.php?genre="+encodeURIComponent(genre);
            }
        </script>

<body>

<div class="container">


    <div class="row row-padding">

        <ul class="list-inline">
            <li><a href="/profile.php/<?php echo get_username($ID) ?>">Go To Your Profile <?php require 'getNewMessageCount.php' ?></a></li>
        </ul>

       <!-- SMARTADDON BEGIN -->
<script type="text/javascript">
(function() {
var s=document.createElement('script');s.type='text/javascript';s.async = true;
s.src='http://s1.smartaddon.com/share_addon.js';
var j =document.getElementsByTagName('script')[0];j.parentNode.insertBefore(s,j);
})();
</script>

<a href="http://www.smartaddon.com/?share" title="Share Button" onclick="return sa_tellafriend('','bookmarks')"><img alt="Share" src="http://s1.smartaddon.com/s12.png" border="0" /></a>
<!-- SMARTADDON END -->
<br/><br/>

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call ">
            <img src="/images/roll-call.gif" height="150px" width="150px" alt="Roll Call"/>
            <br/>

            <form method="post" enctype="multipart/form-data" action="" onsubmit="showUploading()">
                <img src="/images/image-icon.png" height="30px" width="30px" alt="Photos/Video"/>
                <strong>Attach Photo/Video To Your Post</strong>
                <input type="file" width="10px;" name="flPostMedia" id="flPostMedia"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="500000000"
                <br/>
                <textarea name="post" id="post" class="form-control textArea"
                       placeholder="Share Your Talent" ></textarea>
                <br/>
                <div id="progress" style="display:none;">
                    <div class="progress">
                      <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                        <span class="sr-only">Loading</span>
                      </div>
                    </div>
                </div>
                <br/>
                    <select class="form-control input-lg" id="category" name="category">
                            <option value="">Category</option>
                            <?php echo category() ?>
                        </select>
                        <br/>
                    <input type="submit" class="post-button" name="submit" id="submit" value="Post"/>
            </form>
        </div>
    </div>

<div align = "center">
<select id="genre" name="genre" onchange="getGenre()">
            <option value="">Show Post By Category</option>
            <option value="Show-All">Show All</option>
                            <?php echo category() ?>
                        </select>
</div>

    <?php
$genre = $_GET['genre'];
if (!empty($genre) && $genre != "Show-All") {
    $genreCondition = "And Posts.Category = '$genre' ";
}
else if($genre = "Show-All") {
    $genreCondition = " ";
}
else { $genreCondition = " "; }

$sql = "SELECT DISTINCT
    Members.ID As MemberID,
    Members.FirstName As FirstName,
    Members.LastName As LastName,
    Posts.ID As PostID,
    Posts.Post As Post,
    Posts.Category As Category,
    Profile.ProfilePhoto As ProfilePhoto
    FROM Members,Posts,Profile
    WHERE
    Members.IsActive = 1
    And Members.IsSuspended = 0
    And Members.ID = Posts.Member_ID
    And Members.ID = Profile.Member_ID
    And Posts.IsDeleted = 0
    $genreCondition
    Group By Posts.ID
    Order By Posts.ID DESC ";


$result = mysql_query($sql) or die(mysql_error());


if (mysql_numrows($result) > 0) {
    while ($rows = mysql_fetch_assoc($result)) {
        $memberID = $rows['MemberID'];
        $name = $rows['FirstName'] . ' ' . $rows['LastName'];
        $profilePhoto = $rows['ProfilePhoto'];
        $category = $rows['Category'];
        $post = $rows['Post'];
        $postID = $rows['PostID'];
        $postOwner = $memberID;
        ?>
        <div class="row row-padding">
        <div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 "
             style="background:white;border-radius:10px;margin-top:20px;border:2px solid black;" align="left">

            <img src="<?php echo $mediaPath. $profilePhoto ?>" class="profilePhoto-Feed" alt=""
                 title="<?php echo $name ?>" class='enlarge-onhover img-responsive'/> &nbsp <b><font
                    size="4"><?php echo $name ?></font></b>


            <div class="post">
                <?php

                if (strlen($post) > 500) {

                    $post500 = mb_substr($post, 0, 500); ?>

                    <div id="short<?php echo $postID ?>">
                        <?php echo nl2br($post500) ?>...<a href="javascript:showPost('long<?php echo $postID ?>', 'short<?php echo $postID ?>');">Show More</a>
                    </div>
                    <?php
                    echo "<div id='long$postID' style='display:none;'>";
                    echo nl2br($post);

                    echo "</div>";
                }
                else {
                    echo nl2br($post);

                }
                ?>

            </div>

            <a href='/post-cat.php?cat=<?php echo urlencode($category) ?>' class='category'><h5><?php echo $category ?></h5></a>
            <br/><br/>
            <?php

            //check if member has approved this post
            //----------------------------------------------------------------
            //require 'getSessionType.php';

            $sql2 = "SELECT ID FROM PostApprovals WHERE Post_ID = '$postID' AND Member_ID = '$ID'";
            $result2 = mysql_query($sql2) or die(mysql_error());
            $rows2 = mysql_fetch_assoc($result2);


            // get approvals for each post
            $approvals = mysql_num_rows(mysql_query("SELECT * FROM PostApprovals WHERE Post_ID = '$postID'"));

            // show disapprove if members has approved the post
            echo '<div class="post-approvals">';
            echo "<div id = 'approvals$postID'>";

            if (mysql_numrows($result2) > 0) {

                echo '<form>';

                echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';
                echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
                echo '<input type ="button" class = "btnDisapprove" />';

                if ($approvals > 0) {


                    echo '&nbsp;<span style = "color:red;font-weight:bold;font-size:16">' . $approvals . '</font>';
                }
                echo '</form>';
            } else {
                echo '<form>';

                echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';
                echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
                echo '<input type ="button" class = "btnApprove" />';

                if ($approvals > 0) {


                    echo '&nbsp;<span style = "color:red;font-weight:bold;font-size:16">' . $approvals . '</font>';
                }
                echo '</form>';
            }
            echo '</div>'; // end of approval div
            echo '</div>';

            //-------------------------------------------------------------
            // End of approvals
            //-----------------------------------------------------------

            ?>

            <div style="padding-top:10px;padding-bottom:10px;margin-top:10px;">
                <form method="post" action="" enctype="multipart/form-data"
                      onsubmit="showCommentUploading('comment<?php echo $postID?>', this);">

                    <input type="text" class="form-control" name="postComment" id="postComment"
                           placeholder="Write a comment" title='' style="border:1px solid black"/>


                    <input type="file" name="flPostMedia" id="flPostMedia" style="max-width:180px;"/>
                    <br/>
                    <div id="comment<?php echo $postID ?>" style="display:none;">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                <span class="sr-only">Loading</span>
                            </div>
                        </div>
                    </div>
                    <input type="submit" name="btnComment" id="btnComment" Value="Comment"
                           style="border:1px solid black"/>
                    <input type="hidden" name="postID" id="postID" Value="<?php echo $postID ?>"/>
                    <input type="hidden" name="ID" id="ID" value="<?php echo $ID ?>"/>
                    <input type="hidden" name="ownerId" id="ownerId" value="<?php echo $MemberID ?>"/>
                    <input type="hidden" name="scrollx" id="scrollx" value="0"/>
                    <input type="hidden" name="scrolly" id="scrolly" value="0"/>
                </form>

                <br/>

                <?php if ($memberID != $ID) { ?>
                    <a href="/view_messages.php?id=<?php echo $memberID ?>">Direct Message <?php echo $rows['FirstName'] ?></a>
                <?php } ?>
                <br/>


                <?php
                $sql3 = "SELECT DISTINCT
                        PostComments.Comment As PostComment,
                        PostComments.ID As PostCommentID,
                        Members.ID As MemberID,
                        Members.FirstName as FirstName,
                        Members.LastName As LastName,
                        Profile.ProfilePhoto As ProfilePhoto
                        FROM PostComments,Members, Profile
                        WHERE
                        PostComments.Post_ID = $postID
                        AND Members.ID = Profile.Member_ID
                        And Members.ID = PostComments.Member_ID
                        And PostComments.IsDeleted = 0
                        Group By PostComments.ID
                        Order By PostComments.ID DESC LIMIT 3 ";


                $result3 = mysql_query($sql3) or die(mysql_error());
                echo '<br/>';
                if (mysql_numrows($result3) > 0) {
                    echo '<div class="comment-style">';
                    while ($rows3 = mysql_fetch_assoc($result3)) {
                        $comment = $rows3['PostComment'];
                        $profilePhoto = $rows3['ProfilePhoto'];
                        $commentID = $rows3['PostCommentID'];
                        $commentOwner = $rows3['MemberID'];

                        echo '<div class="comment-row">';

                        echo '<div class="user-icon"><img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" style = "border:1px solid black" class ="enlarge-onhover img-responsive" /><div class="user-name">' . $rows3['FirstName'] . ' ' . $rows3['LastName'] . '</div></div><div class="comment-content">' . nl2br($comment) . '</div>';
                        echo '</div>';

                        if ($commentOwner == $ID || $postOwner == $ID) {
                            //<!--DELETE BUTTON ------------------>

                            echo '<div class="comment-delete">';
                            echo '<form action="" method="post" onsubmit="return confirm(\'Do you really want to delete this comment?\')">';
                            echo '<input type="hidden" name="commentID" id="commentID" value="' .  $commentID . '" />';
                            echo '<input type ="submit" name="DeleteComment" id="DeleteComment" value="Delete" class="deleteButton" />';
                            echo '</form>';
                            echo '</div>';
                            //<!------------------------------------->
                        }
                    }
                    echo '</div>';
                }



                ?>

                <!--Show more comments -->
                <?php

                $sql4 = "SELECT DISTINCT
                        PostComments.Comment As PostComment,
                        PostComments.ID As PostCommentID,
                        Members.ID As MemberID,
                        Members.FirstName as FirstName,
                        Members.LastName As LastName,
                        Profile.ProfilePhoto As ProfilePhoto
                        FROM PostComments,Members, Profile
                        WHERE
                        PostComments.Post_ID = $postID
                        And Members.ID = PostComments.Member_ID
                        And PostComments.IsDeleted = 0
                        And Members.ID = Profile.Member_ID


                        Group By PostComments.ID
                        Order By PostComments.ID DESC LIMIT 3, 100 ";

                $result4 = mysql_query($sql4) or die(mysql_error());
                if (mysql_numrows($result4) > 0) {
                $moreComments = "moreComments$postID";
                ?>

                <a href="javascript:showComments('<?php echo $moreComments ?>');">Show More</a>

                <div id="<?php echo $moreComments ?>" style="display:none;">


                    <?php
                    echo '<br/>';
                    echo '<div class="comment-style">';
                    while ($rows4 = mysql_fetch_assoc($result4)) {
                        $comment = $rows4['PostComment'];
                        $profilePhoto = $rows4['ProfilePhoto'];
                        $commentID = $rows4['PostCommentID'];
                        $commentOwner = $rows4['MemberID'];
                        echo '<div class="user-icon">';
                        echo '<img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" style = "border:1px solid black" class ="enlarge-onhover img-responsive" /><div class="user-name">' . $rows4['FirstName'] . $rows['LastName'] . '</div></div><div class="comment-content">' . nl2br($comment) . '</div>';

                        echo '</td></tr>';

                    }
                    echo '</div>';
                    if ($commentOwner == $ID || $postOwner == $ID) {
                        //<!--DELETE BUTTON ------------------>

                        echo '<div class="comment-delete">';
                        echo '<form action="" method="post" onsubmit="return confirm(\'Do you really want to delete this comment?\')">';
                        echo '<input type="hidden" name="commentID" id="commentID" value="' .  $commentID . '" />';
                        echo '<input type ="submit" name="DeleteComment" id="DeleteComment" value="Delete" class="deleteButton" />';
                        echo '</form>';
                        echo '</div>';
                        //<!------------------------------------->
                    }
                    echo '</div>'; //end of more comments div
                    }
                    ?>


                </div>
                <!---------------------------------------------------
                                  End of comments div
                                  ----------------------------------------------------->

            </div>
        </div>


    <?php
    }
}
else {
    echo "<div align ='center' class='notFound'><h2>No $genre Posts Found</h2></div>";
}
?>


    </div>

    <br/><br/>

</body>
</html>

<?php

$scrollx = 0;
$scrolly = 0;

if(!empty($_REQUEST['scrollx'])) {
    $scrollx = $_REQUEST['scrollx'];
}

if(!empty($_REQUEST['scrolly'])) {
    $scrolly = $_REQUEST['scrolly'];
}
?>

<script type="text/javascript">

    window.scrollTo(<?php echo "$scrollx" ?>, <?php echo "$scrolly" ?>);

</script>

<?php

get_footer_files();
?>
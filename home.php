<!------------------------------------------------------
ALWAYS COMPRESS THIS FILE BEFORE PUSHING TO PRODUCTION
IT WILL INCREASE THE RENDERING TIME OF HTML ELEMENTS
------------------------------------------------------->

<?php
require 'connect.php';
// compress the page
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
require 'model_functions.php';
require 'mediaPath.php';
require 'getSession.php';
require 'html_functions.php';
require 'findURL.php';
require 'email.php';
require 'category.php';
require 'ads.php';
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
                if ($_FILES['flPostMedia']['size'] > 2500000000) {
                    exit();
                }

                ?>

                <?php
                // create media type arrays
                $videoFileTypes = array("video/mpeg", "video/mpg", "video/ogg", "video/mp4",
                    "video/quicktime", "video/webm", "video/x-matroska",
                    "video/x-ms-wmw");

                // add unique id to image name to make it unique and add it to the file server
                $mediaName = $_FILES["flPostMedia"]["name"];
                $fileName = pathinfo($mediaName, PATHINFO_FILENAME);
                $mediaName = trim(uniqid() . $mediaName);
                $mediaFile = $_FILES['flPostMedia']['tmp_name'];
                $type = $_FILES['flPostMedia']['type'];

                $ffmpeg = '/usr/bin/ffmpeg';


                require 'media_post_file_path.php';



                if (in_array($type, $videoFileTypes)) {
                    // convert to mp4 if not already an mp4
                    if ($type != "video/mp4") {
                        $audioName = $fileName;
                        $newFileName = $fileName . ".mp4";
                        $oggFileName = $fileName . ".ogv";
                        $webmFileName = $fileName . ".webm";


                        // convert mp4
                        exec("$ffmpeg -i $fileName $newFileName");
                        $mediaName = $newFileName;

                        // convert ogg
                        exec("$ffmpeg -i $fileName  $oggFileName");
                        // convert webm
                        exec("$ffmpeg -i $fileName  $webmFileName");

                    }
                } else {

                        echo "<script>alert('Invalid File Type'); location='/home.php'</script>";
                        exit;
                    }

                require 'media_post_file_path.php';
// save photo/video



                if (in_array($type, $videoFileTypes) || in_array($type, $audioFileTypes)) {
                    move_uploaded_file($mediaFile, $postMediaFilePath);


                    //copy new mp4 file path to ogg file path
                    copy($postMediaFilePath, $postOggFilePathTemp);
                    // overwrite mp4 with real ogg file path
                    copy($postOggFilePath, $postOggFilePathTemp);
                    // copy new mp4 file path to webm file path
                    copy($postMediaFilePath, $postWebmFilePathTemp);
                    // overwrite mp4 with real webm file path
                    copy($postWebmFilePath, $postWebmFilePathTemp);
                }
                // if photo didn't get uploaded, notify the user
                if (!file_exists($postMediaFilePath)) {
                    echo "<script>alert('File could not be uploaded, try uploading a different file type.');location='home.php'</script>";
                }
                else {
                    // store media pointer
                    $sql = "INSERT INTO Media (Member_ID,  MediaName,    MediaOgg,     MediaWebm,      MediaType,  MediaDate,  AudioName    ) Values
                                              ('$ID',    '$mediaName', '$oggFileName', '$webmFileName',  '$type',   CURRENT_DATE(), '$audioName'  )";
                    mysql_query($sql) or die(mysql_error());
                    $mediaID = mysql_insert_id();
                    // get media ID
                    $sqlGetMedia = "SELECT * FROM Media WHERE MediaName = '$mediaName'";
                    $mediaResult = mysql_query($sqlGetMedia) or die(mysql_error());
                    $mediaRow = mysql_fetch_assoc($mediaResult);
                    //$mediaID = $mediaRow['ID'];
                    $media = $mediaRow['MediaName'];
                    $mediaType = $mediaRow['MediaType'];
                    $mediaDate = $mediaRow['MediaDate'];
                    // build post links based on media type
                    if (in_array($type, $audioFileTypes)) {
                        $img = '<b>'.$audioName.'</b><br/><audio controls>
                            <source src="'.$mediaPath . $mediaName.'" type="'.$mediaType.'">
                            Your browser does not support the audio element.
                            </audio>';
                        $img = '<a href = "media.php?id=' . $ID . '&mediaName=' . $mediaName . '&mid=' . $mediaID . '&mediaType=' . $mediaType . '&mediaDate=' . $mediaDate . '" ><br/>'.$img.'</a><br/><br/>';
                    }

                     // check if file type is a video
                    if (in_array($type, $videoFileTypes)) {
                        // where ffmpeg is located
                        $ffmpeg = '/usr/bin/ffmpeg';
                        // poster file name
                        $posterName = "poster".uniqid().".jpg";
                        //where to save the image
                        $poster = "$posterPath$posterName";
                        //time to take screenshot at
                        //$interval = 5;
                        //screenshot size
                        //$size = '440x280'; -s $size
                        //ffmpeg command
                        $cmd = "$ffmpeg -i \"$postMediaFilePath\" -r 1 -ss 3 -t 1  -f image2 $poster 2>&1";
                        exec($cmd);
                        $poster = imagecreatefromjpeg($poster);

                        /*$white = imagecolorallocate($poster, 255, 255, 255);
                        $text="Rapportbook.com";
                        $font="/stocky.ttf";*/

                        //imagettftext($poster, 20, 0, 20, 20, $white, $font, $text);


                            $size = getimagesize("$posterPath$posterName");
                            $width = $size[0];
                            $height = $size[1];




                        // mobile Iphone landscape tends to be vertical, flip counter clockwise
                        if ($width > $height && $height < 1000 && $type!="video/quicktime") {
                            // video shot in landscape, needs to be flipped
                            $img = imagerotate($poster, -90, 0);
                            imagejpeg($img, $posterPath . $posterName, 50);
                        }

                        // MPGs tend to be upside down, flip 180
                            elseif ($width > $height && $height < 1000 && $type=="video/mpg") {
                                // video shot in landscape, needs to be flipped
                                $img = imagerotate($poster, 180, 0);
                                imagejpeg($img, $posterPath . $posterName, 50);
                            }

                            // portrait images are created sideways, rotate 90 degrees
                            // landscape images are created upside but have the same size
                            // and needs to be rotated 180 but still cannot tell how to tell the difference
                            // both orientations have the same size
                            elseif ($width > $height && $height > 700 && $type == "video/quicktime" || $type == "video/mp4") {
                                    $img = imagerotate($poster, 90, 0);
                                    imagejpeg($img, $posterPath . $posterName, 50);
                            }


                        $img = '<video poster="/poster/'.$posterName.'" preload="none" controls>
                                <source src = "' . $videoPath . $mediaName . '" type="video/mp4" />
                                <source src = "' . $videoPath . $oggFileName . '" type = "video/ogg" />
                                <source src = "' . $videoPath . $webmFileName . '" type = "video/webm" />
                                </video>';

                    }


                    if (in_array($type, $videoFileTypes)) {
                        $post = $post . '<br/><br/><a href="'. $videoPath . $mediaName . '" download="'.$audioName.'"">View in native player </a>' . $img . '<br/>';
                    }

                    $sql = "INSERT INTO Posts (Post,    Poster,	      Category,  Member_ID,   PostDate) Values
                                              ('$post', '$posterName', '$category', '$ID',       CURDATE())";
                    mysql_query($sql) or die(mysql_error());
                    $newPostID = mysql_insert_id();

                    // update Media table with new post id
                        $sqlUpdateMedia = "UPDATE Media SET Post_ID = $newPostID, Poster='$posterName' WHERE ID = '$mediaID' ";
                        mysql_query($sqlUpdateMedia) or die(mysql_error());
                }
            } // if no media
            else {
                echo "<script>alert('Your post must have a video attached to it.'); location = '/home.php'</script>";
            }
        }
    }
    echo "<script>location='/home.php?scrollx=$scrollx&scrolly=$scrolly'</script>";
}
?>


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
            function updateFeed() {
               var selection = document.getElementById('genre');
               var genre = selection.options[selection.selectedIndex].value;

                var ageStartSelect = document.getElementById('AgeStart');
                var ageStart = ageStartSelect.options[ageStartSelect.selectedIndex].value;

                var ageEndSelect = document.getElementById('AgeEnd');
                var ageEnd = ageEndSelect.options[ageEndSelect.selectedIndex].value;

                var stateSelect = document.getElementById('AgeEnd');
                var state = stateSelect.options[stateSelect.selectedIndex].value;

                window.location = "/home.php?genre="+encodeURIComponent(genre)+"&ageStart="+encodeURIComponent(ageStart)+"&ageEnd="+encodeURIComponent(ageEnd)+"&state="+encodeURIComponent(state);
            }
        </script>


<body>


<div class="container">
<?php

?>

    <div class="row row-padding">
<div class=" col-md-10  col-lg-10 col-md-offset-2 col-lg-offset-2 ">


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

    <?php
    $ageStart = $_GET['ageStart'];
    $ageEnd = $_GET['ageEnd'];
    if (!empty($ageStart)) {
        $_SESSION['ageStart'] = $ageStart;
        $ageStart = $_SESSION['ageStart'];
    }
    else {
        $ageStart = 18;
    }

    if (!empty($ageEnd)) {
        $_SESSION['ageEnd'] = $ageEnd;
        $ageEnd = $_SESSION['ageEnd'];
    }
    else {
        $ageEnd = 50;
    }
    ?>

    <?php
    $state = $_GET['state'];
    if (!empty($ageStart)) {
        $_SESSION['state'] = $state;
        $state = $_SESSION['state'];
    }
    ?>

<!--Middle Column -->
    <form>
        Select Age Range
        <select id="AgeStart" name="AgeStart" onchange="updateFeed()">
            <option value="<?php echo $ageStart ?>"><?php echo $ageStart ?></option>
            <?php age() ?>
        </select>
        To
        <select id="AgeEnd" name="AgeEnd" onchange="updateFeed()">
            <option value="<?php echo $ageEnd ?>"><?php echo $ageEnd ?></option>
            <?php age() ?>
        </select>
    </form>
<br/>

        <div class=" col-md-9 col-lg-9 roll-call ">



            <img src="/images/roll-call.gif" height="150px" width="150px" alt="Roll Call"/>
            <br/>

            <form method="post" enctype="multipart/form-data" action="" onsubmit="showUploading()">
                <img src="/images/image-icon.png" height="30px" width="30px" alt="Photos/Video"/>
                <strong>Attach Your Video</strong>
                <br/>

                <input type="file" width="10px;" name="flPostMedia" id="flPostMedia"/>
                <br/>
                <textarea name="post" id="post" class="form-control textArea"
                       placeholder="Share a cool video" ></textarea>
                <br/>
                <div id="progress" style="display:none;">
                    <div class="progress">
                      <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                        <b>File uploading...please wait</b>
                      </div>
                    </div>
                </div>
                <br/>
                    <select class="form-control input-lg" id="category" name="category">
                            <option value="">Select A Post Category</option>
                            <?php echo category() ?>
                        </select>
                        <br/>
                    <input type="submit" class="post-button" name="submit" id="submit" value="Post"/>
            </form>

<br/><br/>
<div align = "center">
<select id="genre" name="genre" onchange="updateFeed()">
            <option value="">Show Posts By Category</option>
            <option value="Show-All">Show All</option>
                            <?php echo category() ?>
                        </select>
</div>
        </div>


<?php
$limit = "100";
require 'roll-call.php'
?>

        </div>


</div> <!--Middle Column -->

    </div>


    <br/><br/>

            </div>

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
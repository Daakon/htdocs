<?php

require 'connect.php';
require 'getSession.php';
require 'html_functions.php';
require 'mediaPath.php';
require 'findURL.php';
require 'model_functions.php';
require 'category.php';
require 'ads.php';
get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];
?>

<?php include('media_sizes.html');  ?>



<?php

// ----------------------------
//delete post
// ----------------------------

if (isset($_POST['Delete']) && $_POST['Delete'] == "Delete") {
    $postID = $_POST['postID'];
    $sql = "Update Posts SET IsDeleted = '1' WHERE ID = $postID And Member_ID = $ID ";
    mysql_query($sql) or die (mysql_error());

}
if (isset($_POST['DeleteComment']) && $_POST['DeleteComment'] == "Delete") {
    $commentID = $_POST['commentID'];
    $sql = "Update PostComments SET IsDeleted = '1' WHERE ID = $commentID";
    mysql_query($sql) or die (mysql_error());

}
?>

<style>

</style>

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
                postID: $(this).closest('tr').find('.postID').val(),
                ID: $(this).closest('tr').find('.ID').val()
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
                postID: $(this).closest('tr').find('.postID').val(),
                ID: $(this).closest('tr').find('.ID').val()
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

    function showComments(id) {
        var e = document.getElementById(id);
        if (e.style.display == 'none') {
            e.style.display = 'block';
        }
        else
            e.style.display = 'none';
    }

</script>

<body>

<div class="container">


    <div class="row row-padding">
        <div class=" col-md-10  col-lg-10 col-md-offset-2 col-lg-offset-2 ">

            <ul class="list-inline">
        <?php require 'profile_menu.php'; ?>
            </ul>

    <?php
    // ad demographics
    $age = getAge($ID);
    $state =  getMemberState($ID);
    $interests = getInterests($ID);
    $interests = strtolower($interests);
    $gender = getGender($ID);
    $ads = getAds($genre, $age, $state, $interests, $gender);
    $sql = "SELECT DISTINCT
    Members.ID As MemberID,
    Members.FirstName As FirstName,
    Members.LastName As LastName,
    Members.Username As Username,
    Posts.ID As PostID,
    Posts.Post As Post,
    Posts.Category As Category,
    Profile.ProfilePhoto As ProfilePhoto
    FROM Members,Posts,Profile
    WHERE
    Posts.Member_ID = $ID
    And Members.IsActive = 1
    And Members.IsSuspended = 0
    And Members.ID = Posts.Member_ID
    And Members.ID = Profile.Member_ID
    And Posts.IsDeleted = 0
    UNION
    $ads
    Group By PostID
    Order By PostID DESC ";


    $result = mysql_query($sql) or die(mysql_error());


    if (mysql_num_rows($result) > 0) {
    while ($rows = mysql_fetch_assoc($result)) {
    $memberID = $rows['MemberID'];
    $name = $rows['FirstName'] . ' ' . $rows['LastName'];
    $profilePhoto = $rows['ProfilePhoto'];
    $category = $rows['Category'];
    $post = $rows['Post'];
    $postID = $rows['PostID']
    ?>

        <div class="col-lg-9 col-md-9 roll-call" >

            <img src="/poster/<?php echo $profilePhoto ?>" class="profilePhoto-Feed" alt=""
                 title="<?php echo $name ?>" class='enlarge-onhover img-responsive'/> &nbsp <b><font
                    size="4"><?php echo $name ?></font></b>

            <div class="post"><?php echo nl2br($post); ?></div>

            <br/>
            <a href='/post-interest.php?interest=<?php echo urlencode($category) ?>' class='category'><h5><?php echo $category ." ". interestGlyphs($category) ?></h5></a>

            <br/><br/>

            <!--DELETE BUTTON ------------------>
            <form action="" method="post" onsubmit="return confirm('Do you really want to delete this post?')">
                <input type="hidden" name="postID" id="postID" value="<?php echo $postID ?>" />
            <input type ="submit" name="Delete" id="Delete" value="Delete" class="deleteButton" />
            </form>
            <br/><br/>
            <!------------------------------------->

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
            echo '<table>';
            echo '<tr>';
            echo '<td>';
            echo "<div id = 'approvals$postID'>";

            if (mysql_num_rows($result2) > 0) {

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
            echo '</td></tr></table>';

            //-------------------------------------------------------------
            // End of approvals
            //-----------------------------------------------------------

            ?>


            </div>
        <?php
            }
        }
        else { ?>
        <div class="col-lg-9 col-md-9 roll-call "
             style="background:white;border-radius:10px;margin-top:20px;border:2px solid black;" align="left">
            <div style='font-weight:bold;'>You do not have anything posted.</div>
        </div>
            <?php }
        ?>
    </div>
        <!--Right Column -->


    </div> <!--End Middle Column -->

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


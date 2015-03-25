<?php

require 'connect.php';
require 'getSession.php';
require 'html_functions.php';
require 'mediaPath.php';
require 'findURL.php';
require 'model_functions.php';
get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];
?>

<?php include('media_sizes.html');  ?>

<?php
// delete post
if (isset($_POST['Delete']) && $_POST['Delete'] == "Delete") {
    $postID = $_POST['postID'];
    $sql = "Update Posts SET IsDeleted = '1' WHERE ID = $postID And Member_ID = $ID ";
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

        <?php require 'profile_menu.php'; ?>


    </div>

    <?php


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
    Posts.Member_ID = $ID
    And Members.IsActive = 1
    And Members.IsSuspended = 0
    And Members.ID = Posts.Member_ID
    And Members.ID = Profile.Member_ID
    And Posts.IsDeleted = 0
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
    $postID = $rows['PostID']
    ?>
    <div class="row row-padding">
        <div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 "
             style="background:white;border-radius:10px;margin-top:20px;border:2px solid black;" align="left">

            <img src="<?php echo $mediaPath. $profilePhoto ?>" class="profilePhoto-Feed" alt=""
                 title="<?php echo $name ?>" class='enlarge-onhover'/> &nbsp <b><font
                    size="4"><?php echo $name ?></font></b>
            <br/>

            <p><?php echo nl2br($post); ?></p>

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
            echo '</td></tr></table>';

            //-------------------------------------------------------------
            // End of approvals
            //-----------------------------------------------------------

            ?>

            <div style="padding-top:10px;padding-bottom:10px;margin-top:10px;">
                <form method="post" action="" enctype="multipart/form-data"
                      onsubmit="return saveScrollPositions(this);">

                    <input type="text" class="form-control" name="postComment" id="postComment"
                           placeholder="Write a comment" title='' style="border:1px solid black"/>


                    <input type="file" name="flPostMedia" id="flPostMedia" style="max-width:180px;"/>
                    <br/>
                    <input type="submit" name="btnComment" id="btnComment" Value="Comment"
                           style="border:1px solid black"/>
                    <input type="hidden" name="postID" id="postID" Value="<?php echo $postID ?>"/>
                    <input type="hidden" name="ID" id="ID" value="<?php echo $ID ?>"/>
                    <input type="hidden" name="ownerId" id="ownerId" value="<?php echo $MemberID ?>"/>
                    <input type="hidden" name="scrollx" id="scrollx" value="0"/>
                    <input type="hidden" name="scrolly" id="scrolly" value="0"/>
                </form>

                <br/>
                <?php
                // get bulletin comments
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
                        Group By PostComments.ID
                        Order By PostComments.ID ASC LIMIT 3 ";


                $result3 = mysql_query($sql3) or die(mysql_error());
                if (mysql_numrows($result3) > 0) {
                    echo '<br/>';
                    echo '<table style = "background:#E0EEEE;width:100%">';
                    while ($rows3 = mysql_fetch_assoc($result3)) {
                        $comment = $rows3['PostComment'];
                        $profilePhoto = $rows3['ProfilePhoto'];

                        echo '<tr><td style="width:60px;padding-bottom:10px;" valign = "top">';

                        echo '<img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" style = "border:1px solid black" class ="enlarge-onhover" />&nbsp;</td><td valign = "top"><b>' . $rows3['FirstName'] . ' ' . $rows3['LastName'] . '</b>&nbsp;&nbsp;' . nl2br($comment) . '</span>';
                        echo '</td></tr>';
                    }
                    echo '</table>';
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
                        And Members.ID = Profile.Member_ID
                        Group By PostComments.ID
                        Order By PostComments.ID ASC LIMIT 3, 100 ";

                $result4 = mysql_query($sql4) or die(mysql_error());
                if (mysql_numrows($result4) > 0) {
                $moreComments = "moreComments$postID";
                ?>

                <a href="javascript:showComments('<?php echo $moreComments ?>');">Show More</a>

                <div id="<?php echo $moreComments ?>" style="display:none;">


                    <?php
                    echo '<br/>';
                    echo '<table style = "background:#E0EEEE;width:100%">';
                    while ($rows4 = mysql_fetch_assoc($result4)) {
                        $comment = $rows4['PostComment'];
                        $profilePhoto = $rows4['ProfilePhoto'];

                        echo '<tr><td style = "width:60px;padding-bottom:10px;" valign = "top">';
                        echo '<img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" style = "border:1px solid black" class ="enlarge-onhover" />&nbsp;</td><td valign = "top"><b>' . $rows4['FirstName'] . $rows['LastName'] . '</b>&nbsp;&nbsp;' . nl2br($comment) . '</span>';

                        echo '</td></tr>';

                    }
                    echo '</table>';
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


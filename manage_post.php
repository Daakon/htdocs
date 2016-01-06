<?php

require 'imports.php';
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

        <div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 roll-call"
             align="left" style="min-height: 10px;">
            <?php require 'profile_menu.php'; ?>
        </div>

        <?php
        $username = get_username_from_url();
        $username = explode("?", $username);
        $username = $username[0];
        $profileID = get_id_from_username($username);

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
    Posts.Member_ID = $profileID
    And(Members.IsActive = 1)
    And (Members.IsSuspended = 0)
    And (Members.ID = Posts.Member_ID)
    And (Members.ID = Profile.Member_ID)
    And (Posts.IsDeleted = 0)
    Group By PostID
    Order By PostID DESC ";


        $result = mysql_query($sql) or die(mysql_error());


        if (mysql_num_rows($result) > 0) {
        while ($rows = mysql_fetch_assoc($result)) {
        $memberID = $rows['MemberID'];
        $name = $rows['FirstName'] . ' ' . $rows['LastName'];
        $firstName = $rows['FirstName'];
        $username = $rows['Username'];
        $profilePhoto = $rows['ProfilePhoto'];
        $category = $rows['Category'];
        $post = $rows['Post'];
        $postID = $rows['PostID']
        ?>

        <div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 roll-call"
             align="left">

            <div class="profileImageWrapper-Feed">
                <a href="<?php echo $profileUrl ?>">
                    <img src="<?php echo $mediaPath. $profilePhoto ?>" class="profilePhoto-Feed " alt=""
                         title="<?php echo $name ?>" />
                </a>
            </div>

            <div class="profileNameWrapper-Feed" >
                <a href="<?php echo $profileUrl ?>">
                    <div class="profileName-Feed"><?php echo $name ?></div>
                </a>
            </div>

            <div class="post" style="clear:both;">
                <?php
                // check check post length if it has a url in it
                if (strstr($post, "http://") || strstr($post, "https://")) {
                    echo nl2br($post);

                }
                else if (strlen($post) > 700) {
                    $post500 = substr($post, 0, strpos($post, ' ', 700)).'<br/>'; ?>

                    <?php echo nl2br($post500) ?>
                    <br/>
                    <a style="display:block;" style="width:100%;" href="show_post?postID=<?php echo $postID ?>&email=0">
                        <span style="color:black;font-weight: 800">Show More</span>
                    </a>

                    <?php
                }
                else {
                    echo nl2br($post);
                }
                ?>
            </div>

            <hr/>

            <a href='/post-interest.php?interest=<?php echo urlencode($category) ?>' onclick="saveScrollPositionOnLinkClick('/manage_post/<?php echo $username ?>')"><span class="engageText">#<?php echo $category ?></span></a>

            <?php if ($memberID != $ID) { ?>
                | <a href="/view_messages.php/<?php echo $username ?>"><span class="engageText"><img src="/images/messages.png" height="20" width="20" /> Message </span></a>
            <?php } ?>

            <?php if ($_SESSION['ID'] == get_id_from_username($username)) { ?>

                <div class="content-space" style="padding-top:20px;">
                    <!--DELETE BUTTON ------------------>
                    <form action="" method="post" onsubmit="return confirm('Do you really want to delete this post?')">
                        <input type="hidden" name="postID" id="postID" value="<?php echo $postID ?>"/>
                        <input type="submit" name="Delete" id="Delete" value="Delete" class="deleteButton"/>
                    </form>
                </div>
                <!------------------------------------->

                <?php
            }

            echo "<hr/>";

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


                    echo '&nbsp;<span>' . $approvals . '</font>';
                }
                echo '</form>';
            } else {
                echo '<form>';

                echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';
                echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
                echo '<input type ="button" class = "btnApprove" />';

                if ($approvals > 0) {


                    echo '&nbsp;<span>' . $approvals . '</font>';
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
                      onsubmit="showCommentUploading('comment<?php echo $postID?>', this);">

                    <input type="text" class="form-control" name="postComment" id="postComment"
                           placeholder="Write a comment" title='' />

                    <h6>Attach A Photo/Video To Your Comment</h6>
                    <input type="file" name="flPostMedia" id="flPostMedia" style="max-width:180px;"/>

                    <br/>
                    <div id="comment<?php echo $postID ?>" style="display:none;">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                <b>File uploading...please wait</b>
                            </div>
                        </div>
                    </div>
                    <input type="submit" name="btnComment" id="btnComment" Value="Comment"/>
                    <input type="hidden" name="postID" id="postID" Value="<?php echo $postID ?>"/>
                    <input type="hidden" name="ID" id="ID" value="<?php echo $ID ?>"/>
                    <input type="hidden" name="ownerId" id="ownerId" value="<?php echo $MemberID ?>"/>
                    <input type="hidden" name="scrollx" id="scrollx" value="0"/>
                    <input type="hidden" name="scrolly" id="scrolly" value="0"/>
                </form>


                <?php
                $sql3 = "SELECT DISTINCT
                        PostComments.Comment As PostComment,
                        PostComments.ID As PostCommentID,
                        Members.ID As CommenterID,
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
                if (mysql_num_rows($result3) > 0) {
                    echo '<div class="comment-style">';
                    while ($rows3 = mysql_fetch_assoc($result3)) {
                        $comment = $rows3['PostComment'];
                        $profilePhoto = $rows3['ProfilePhoto'];
                        $commentID = $rows3['PostCommentID'];
                        $commentOwnerID = $rows3['CommenterID'];
                        echo '<div class="comment-row">';
                        echo '<div class="profileImageWrapper-Feed">
                        <a href='.$commenterProfileUrl.'>
                        <img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" class ="img-responsive" />
                        </a>
                        </div>

                         <div class="commentNameWrapper-Feed" style="padding-left:10px">
                          <a href='.$commenterProfileUrl.'>
                            <div class="profileName-Feed"><?php echo $name ?> ' .
                            $rows3['FirstName'] . ' ' . $rows3['LastName'] .
                            '</div>
                         </a>
                        </div>

                    <div class="comment-content" style="clear:both">' . nl2br($comment) . '</div>';
                        echo '</div>';

                        if ($commentOwnerID == $ID) {
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
                        Members.ID As CommenterID,
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
                if (mysql_num_rows($result4) > 0) {
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
                        $commentOwnerID = $rows4['CommenterID'];
                        echo '<div class="user-icon">';
                        echo '<a href='.$commenterProfileUrl.'>';
                        echo '<div class="profileImageWrapper-Feed">';
                        echo '<img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" class ="img-responsive" />
                        </a></div>

                        <div class="commentNameWrapper-Feed">
                        <a href='.$commenterProfileUrl.'>
                        <div class="profileName-Feed">' . $rows4['FirstName'] .' '. $rows4['LastName'] .
                            '</div></div><div class="comment-content" style="clear:both"></a>' . nl2br($comment) .
                            '</div>

                        </div>';
                        echo '</td></tr>';
                    }
                    echo '</div>';


                    if ($commentOwnerID == $ID) {
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
            <?php
            }
            }
            else { ?>
                <div class="row row-padding">
                    <div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 roll-call" align="left">
                        <?php if ($ID == get_id_from_username($username)) { ?>
                            <div>You do not have anything posted.</div>
                        <?php } else {
                            $firstName = get_user_firstName(get_id_from_username($username));
                            ?>
                            <div><?php echo $firstName ?> does not have anything posted.</div>
                        <?php } ?>
                    </div>
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


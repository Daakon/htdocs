
<?php
// pre-load Roll Call
// get genre selection


if (!empty($genre) && $genre != "Show All") {
    $genreCondition = "And Posts.Category = '$genre' ";
}
else if($genre = "Show All") {
    $genre = '';
    $genreCondition = "And Posts.Category > '' ";
}
else { $genreCondition = "And Posts.Category = '$genre' "; }

if (!empty($searchState)) {
    $stateCondition = "AND (Profile.State = '$searchState' AND Profile.City = '$searchCity')";
}
else {
    $stateCondition = "";
}


//$ads = getAds($genre, $age, $state, $interests, $gender);
$sqlRollCall = " SELECT DISTINCT
    Posts.Post As Post,
    Members.ID As MemberID,
    Members.FirstName As FirstName,
    Members.LastName As LastName,
    Members.Username As Username,
    Posts.ID As PostID,
    Posts.Category As Category,
    Profile.ProfilePhoto As ProfilePhoto
    FROM Members,Posts,Profile
    WHERE
    Members.IsActive = 1
    And (Members.IsSuspended = 0)
    And (Members.ID = Posts.Member_ID)
    And (Members.ID = Profile.Member_ID)
    And (Posts.IsDeleted = 0)
    AND (Posts.Category <> 'Sponsored')
    $genreCondition
    $stateCondition
    Group By PostID
    Order By PostID DESC LIMIT $limit ";
$rollCallResult = mysql_query($sqlRollCall) or die(mysql_error());
// if no results
if (mysql_num_rows($rollCallResult) == 0) {
    ?>
    <div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 "
          align="left">
        <!--add bravery image -->
        <img src="/images/bravery.jpg" style="height:250px;width: 450px;" />
    </div>
<?php }
if (mysql_num_rows($rollCallResult) > 0) {
while ($rows = mysql_fetch_assoc($rollCallResult)) {
$memberID = $rows['MemberID'];
$name = $rows['FirstName'] . ' ' . $rows['LastName'];
    $username = $rows['Username'];
$profilePhoto = $rows['ProfilePhoto'];
$category = $rows['Category'];
$post = $rows['Post'];
$postID = $rows['PostID'];
$postOwner = $memberID;
?>




<div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 roll-call"
     align="left">

    <?php
        $profileUrl = "/$username";
    ?>

    <a href="<?php echo $profileUrl ?>">
    <img src="<?php echo $mediaPath. $profilePhoto ?>" class="profilePhoto-Feed enlarge-onhover " alt=""
         title="<?php echo $name ?>" /> &nbsp <b><font size="2"><?php echo $name ?></font></b>
</a>

    <div class="post">
        <?php
        if (strlen($post) > 700) {
            $post500 = substr($post, 0, 700); ?>

                <?php echo nl2br($post500) ?>
                <span style="margin-top:5px">
                <a href="show_post?postID=<?php echo $postID ?>&email=0">Show More</a>
</span>
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

    <?php if (isset($ID)) { ?>
    <a href='/post-interest?interest=<?php echo urlencode($category) ?>' class='category'><h4>#<?php echo $category ?></h4></a>


    <div style="padding-top:10px;padding-bottom:10px;margin-top:10px;">

        <br/>
            <?php if ($ID != $memberID) {?>
            <a href="/view_messages/<?php echo $username ?>">Message <?php echo $rows['FirstName'] ?> </a>
            <?php } ?>
        <br/>
<?php } ?>

        <br/><br/>

        <?php

        if (!isset($ID)) {
            $readonly = "readonly";
        }
        else {
            $readonly = "";
        }

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
        if (mysql_num_rows($result2) > 0) {
            echo '<form>';
            echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';
            echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
            echo '<input type ="button" class = "btnDisapprove"'. $readonly.' />';
            if ($approvals > 0) {
                echo '&nbsp;<span style = "color:red;font-weight:bold;font-size:16">' . $approvals . '</font>';
            }
            echo '</form>';
        } else {
            echo '<form>';
            echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';
            echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
            echo '<input type ="button" class = "btnApprove"'. $readonly.' />';
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
            <?php if (isset($ID)) { ?>
            <form method="post" action="" enctype="multipart/form-data"
                  onsubmit="showCommentUploading('comment<?php echo $postID?>', this);">

                <input type="text" class="form-control" name="postComment" id="postComment"
                       placeholder="Write a comment" title='' style="border:1px solid black"/>

                <h6 style="color:red">Attach A Photo/Video To Your Comment</h6>
                <input type="file" name="flPostMedia" id="flPostMedia" style="max-width:180px;"/>

                <br/>
                <div id="comment<?php echo $postID ?>" style="display:none;">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                            <b>File uploading...please wait</b>
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
<?php } ?>

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
                    echo '<div class="user-icon"><img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" style = "border:1px solid black" class ="enlarge-onhover img-responsive" /><div class="user-name">' . $rows3['FirstName'] . ' ' . $rows3['LastName'] . '</div></div><div class="comment-content">' . nl2br($comment) . '</div>';
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
                    echo '<img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" style = "border:1px solid black" class ="enlarge-onhover img-responsive" /><div class="user-name">' . $rows4['FirstName'] .' '. $rows4['LastName'] . '</div></div><div class="comment-content">' . nl2br($comment) . '</div>';
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
</div>
    <?php
    }
    }
    ?>


<!--Right Column -->

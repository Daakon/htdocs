
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
    <div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 roll-call"
          align="left">
        <h3>Make The First <?php echo $genre ?> Connection in <?php echo $searchCity ?></h3>
    </div>
<?php }
if (mysql_num_rows($rollCallResult) > 0) {
while ($rows = mysql_fetch_assoc($rollCallResult)) {
$memberID = $rows['MemberID'];
$firstName = $rows['FirstName'];
$lastName = $rows['LastName'];
$name = $rows['FirstName'] . ' ' . $rows['LastName'];
    $username = $rows['Username'];
$profilePhoto = $rows['ProfilePhoto'];
$category = $rows['Category'];
$post = $rows['Post'];
$postID = $rows['PostID'];
$postOwner = $memberID;
?>




<div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 roll-call">

    <?php
        $profileUrl = "/$username";
    if (strlen($name) > 70) {
        $name = checkNameLength($name,$firstName,$lastName);
    }
    ?>

    <a href="<?php echo $profileUrl ?>">
    <img src="<?php echo $mediaPath. $profilePhoto ?>" class="profilePhoto-Feed enlarge-onhover " alt=""
         title="<?php echo $name ?>" /> &nbsp <span class="profileName-Feed"><?php echo $name ?></span>
</a>

    <div class="post">

        <?php
        if (preg_match('/^.{1,260}\b/s', $body, $match))
        {
            $line=$match[0];
        }

        if (strlen($post) > 700) {
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



    <?php if (isset($ID)) { ?>

    <a href='/post-interest?interest=<?php echo urlencode($category) ?>' class='category'><h4>#<?php echo $category ?></h4></a>


    <div class="content-space">

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
                echo '&nbsp;<span>' . $approvals . '</font>';
            }
            echo '</form>';
        } else {
            echo '<form>';
            echo '<input type ="hidden" class = "postID" id = "postID" value = "' . $postID . '" />';
            echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
            echo '<input type ="button" class = "btnApprove"'. $readonly.' />';
            if ($approvals > 0) {
                echo '&nbsp;<span>' . $approvals . '</font>';
            }
            echo '</form>';
        }
        echo '</div>'; // end of approval div
        echo '</div>';
        //-------------------------------------------------------------
        // End of approvals
        //-----------------------------------------------------------
        ?>

        <div class="content-space">
            <?php if (isset($ID)) { ?>
            <form method="post" action="" enctype="multipart/form-data"
                  onsubmit="showCommentUploading('comment<?php echo $postID?>', this);">

                <input type="text" class="form-control" name="postComment" id="postComment"
                       placeholder="Write a comment" title='' />

                <h6>Attach A Photo/Video To Your Comment</h6>
                <input type="file" name="flPostMedia" id="flPostMedia" class="flPostMedia"/>

                <br/>
                <div id="comment<?php echo $postID ?>" style="display:none;">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" >
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

            <br/>
<?php } ?>

            <?php
            $sql3 = "SELECT DISTINCT
                        PostComments.Comment As PostComment,
                        PostComments.ID As PostCommentID,
                        Members.ID As CommenterID,
                        Members.FirstName as FirstName,
                        Members.LastName As LastName,
                        Members.Username As Username,
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
                    $commenterUsername = $rows3['Username'];
                    $commenterProfileUrl = "/$commenterUsername";

                    echo '<div class="comment-row">';
                    echo '<div class="user-icon">
                    <a href='.$commenterProfileUrl.'>
                    <img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" class ="enlarge-onhover img-responsive border-1" />
                    <div class="user-name">' . $rows3['FirstName'] . ' ' . $rows3['LastName'] . '</div>
                     </a>
                    </div>
                    <div class="comment-content">' . nl2br($comment) . '</div>
                    </a>';
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
                        Members.Username As Username,
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
                    $commenterUsername = $rows4['Username'];
                    $commenterProfileUrl = "/$commenterUsername";

                    echo '<div class="user-icon">';
                    echo '<a href='.$commenterProfileUrl.'>';
                    echo '<img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" class ="enlarge-onhover img-responsive border-1" />
                    <div class="user-name">' . $rows4['FirstName'] .' '. $rows4['LastName'] .
                        '</div></div><div class="comment-content">' . nl2br($comment) .
                        '</div>
                    </a>';
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

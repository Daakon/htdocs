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
$rollCallResult = mysql_query($sqlRollCall) or die(logError(mysql_error(), $url, "Getting Connection Feed data"));
// if no results
if (mysql_num_rows($rollCallResult) == 0) {
    $greetText = "Make the first $genre connection in $searchCity ";
    if ($genre == "Individual") { $greetText = ""; }
    ?>
    <div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 roll-call"
         align="left">
         <?php echo $greetText ?>
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




<div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 col-sm-12 col-xs-12 roll-call">

    <?php
    $profileUrl = "/$username";
    ?>

    <div class="profileImageWrapper-Feed">
        <a href="<?php echo $profileUrl ?>">
            <img src="<?php echo $mediaPath. $profilePhoto ?>" class="profilePhoto-Feed enlarge-onhover " alt=""
                 title="<?php echo $name ?>" />
        </a>
    </div>

    <div class="profileNameWrapper-Feed">
        <a href="<?php echo $profileUrl ?>">
            <div class="profileName-Feed"><?php echo $name ?></div>
        </a>
    </div>


    <div class="post" <?php echo $postStyle ?> style="clear:both;">

        <?php
        // remove excessive white space
        $post = preg_replace('/\s+/', ' ', $post);
        // trim white space
        $post = trim($post);
        // remove excessive line breaks
        $post = cleanBrTags($post);

        // check check post length if it has a url in it
        if (strstr($post, "http://") || strstr($post, "https://")) {
            echo nl2br($post);

        }
        else if (strlen($post) > 700) {
            $post500 = substr($post, 0, strpos($post, ' ', 700)).'<br/>'; ?>

            <?php echo nl2br($post500) ?>
            <br/>
            <a onclick="saveScrollPositionOnLinkClick('/home')" style="display:block;" style="width:100%;" href="show_post?postID=<?php echo $postID ?>&email=0">
                <span style="color:black;font-weight: 800">Show More</span>
            </a>

            <?php
        }
        else {
            echo nl2br($post);
        }
        ?>

    </div>


    <hr class="hr-line" />

    <?php if (isset($ID)) { ?>
        <a href='/post-interest?interest=<?php echo urlencode($category) ?>' onclick="saveScrollPositionOnLinkClick('/home')" class='category'><span class="engageText">#<?php echo $category ?></span></a>



        <?php if ($ID != $memberID) {?>
            | <a href="/view_messages/<?php echo $username ?>"><span class="engageText"><img src = "/images/messages.png" height="20" width="20" /> Message </span> </a>
        <?php } ?>

    <?php } ?>

    <div >

        <br/>

        <?php
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if (strstr($url, "local")) {
        }
        elseif (strstr($url, "dev")) {
            $postPath = "http://dev.rapportbook.com/";
        }
        else {
            $postPath = "http://rapportbook.com/";
        }
        ?>

        <?php $shareLinkID = "shareLink$postID"; ?>
       <a href="javascript:showLink('<?php echo $shareLinkID ?>');">
           <img src="/images/share.gif" height="50px" width="50px" />
           <span style="color:black;font-weight:bold;">Share This Post</span>
       </a>

        <?php $shareLink = 'show_post?postID='.$postID; ?>
        <input id="<?php echo $shareLinkID ?>" style="display:none;" value ="<?php echo $postPath.$shareLink ?>" />

        <?php

        if (!isset($ID)) {
            $readonly = "readonly";
        }
        else {
            $readonly = "";
        }

        echo "<hr class='hr-line'/>";

        //check if member has approved this post
        //----------------------------------------------------------------
        //require 'getSessionType.php';
        $sql2 = "SELECT ID FROM PostApprovals WHERE Post_ID = '$postID' AND Member_ID = '$ID'";
        $result2 = mysql_query($sql2) or die(logError(mysql_error(), $url, "Getting member approval"));
        $rows2 = mysql_fetch_assoc($result2);
        // get approvals for each post
            $approvals = mysql_num_rows(mysql_query("SELECT * FROM PostApprovals WHERE Post_ID = $postID "));
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

                    <h6>Add Photos/Video</h6>
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

            $result3 = mysql_query($sql3) or die(logError(mysql_error(), $url, "Gettig first 3 Connection Feed comments"));
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
                    echo '<div class="profileImageWrapper-Feed">
                    <a href='.$commenterProfileUrl.'>
                    <img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" class ="enlarge-onhover img-responsive" />
                    </a>
                    </div>

                     <div class="commentNameWrapper-Feed">
                      <a href='.$commenterProfileUrl.'>
                        <div class="profileName-Feed"><?php echo $name ?> ' .
                        $rows3['FirstName'] . ' ' . $rows3['LastName'] .
                        '</div>
                     </a>
                     ' . nl2br($comment) . '
                    </div>

                    <div class="comment-content" style="clear:both"></div>';
                    echo '</div>';
                    if ($commentOwnerID == $ID || $memberID == $ID) {
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
                        AND Members.ID = Profile.Member_ID
                        And Members.ID = PostComments.Member_ID
                        And PostComments.IsDeleted = 0
                        Group By PostComments.ID
                        Order By PostComments.ID DESC LIMIT 3, 100 ";

            $result4 = mysql_query($sql4) or die(logError(mysql_error(), $url, "Gettig first 3 Connection Feed comments"));

            $moreComments = "moreComments$postID";

            if (mysql_num_rows($result4) > 0) {
                ?>

            <a href="javascript:showComments('<?php echo $moreComments ?>');">Show More</a>

            <div id="<?php echo $moreComments ?>" style="display:none;">

                <div class="comment-style">

                <?php
                while ($rows4 = mysql_fetch_assoc($result4)) {
                    $comment = $rows4['PostComment'];
                    $profilePhoto = $rows4['ProfilePhoto'];
                    $commentID = $rows4['PostCommentID'];
                    $commentOwnerID = $rows4['CommenterID'];
                    $commenterUsername = $rows4['Username'];
                    $commenterProfileUrl = "/$commenterUsername";
                ?>
            <div class="comment-row">
                <div class="profileImageWrapper-Feed">
                    <a href='<?php echo $commenterProfileUrl ?>'>
                        <img src = "<?php echo $mediaPath . $profilePhoto ?>" height = "50" width = "50" class ="enlarge-onhover img-responsive" />
                        </a>
                    </div>
                </div>

                    <div class="commentNameWrapper-Feed">
                        <a href='<?php echo $commenterProfileUrl ?>'>
                            <div class="profileName-Feed">
                                <?php echo $rows4['FirstName'] . ' ' . $rows4['LastName'] ?>
                                </div>
                        </a>
                        <?php echo nl2br($comment) ?>
                    </div>
                    <div class="comment-content" style="clear:both"></div>

                    <!--DELETE BUTTON ------------------>
                    <?php if ($commentOwnerID == $ID || $memberID == $ID) { ?>
                    <div class="comment-delete">
                        <form action="" method="post" onsubmit="return confirm(\'Do you really want to delete this comment?\')">
                            <input type="hidden" name="commentID" id="commentID" value="' .  $commentID . '" />
                            <input type ="submit" name="DeleteComment" id="DeleteComment" value="Delete" class="deleteButton" />
                            </form>
                        </div>
                        <?php } ?>
            <?php

                }
                ?>
                    </div>
            </div>
                <?php
            }
                ?>


</div>
            </div>
            </div>
            <!---------------------------------------------------
                              End of comments div
                              ----------------------------------------------------->


    <?php
    }
    }
?>


<!--Right Column -->

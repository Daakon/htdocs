<?php

// ad demographics
$age = getAge($ID);
$state =  getState($ID);
$interests = getInterests($ID);
$interests = strtolower($interests);
$gender = getGender($ID);
// pre-load Roll Call
// get genre selection
$genre = $_GET['genre'];
if (!empty($genre) && $genre != "Show-All") {
    $genreCondition = "And Posts.Category = '$genre' ";
}
else if($genre = "Show-All") {
    $genre = '';
    $genreCondition = "And Posts.Category > '' ";
}
else { $genreCondition = "And Posts.Category > '' "; }
// get member name
$queryName = $_GET['mn'];
if (!empty($queryName)) {
    $memberName = preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/', $queryName, -1, PREG_SPLIT_NO_EMPTY);
    $memberFirstName = $memberName[0];
    $memberLastName = $memberName[1];
    if (strlen($memberLastName) > 0) {
        $memberCondition = " And (Members.FirstName Like '%$memberFirstName%' And Members.LastName Like '%$memberLastName%') ";
    }
    else {
        $memberCondition = "And (Members.FirstName Like '%$memberFirstName%') ";
    }
}
else { $memberCondition = ""; }
$ads = getAds($genre, $age, $state, $interests, $gender);

$sqlRollCall = " SELECT DISTINCT
    Posts.Post As Post,
    Members.ID As MemberID,
    Members.FirstName As FirstName,
    Members.LastName As LastName,
    Members.Username As Username,
    Posts.ID As PostID,
    Posts.Category As Category,
    Profile.Poster As ProfilePhoto
    FROM Members,Posts,Profile
    WHERE
    Members.IsActive = 1
    And (Members.IsSuspended = 0)
    And (Members.ID = Posts.Member_ID)
    And (Members.ID = Profile.Member_ID)
    And (Posts.IsDeleted = 0)
    AND (Posts.Category <> 'Sponsored')
    $genreCondition
    $memberCondition
    UNION
    $ads
    Group By PostID
    Order By PostID DESC LIMIT $limit ";
$rollCallResult = mysql_query($sqlRollCall) or die(mysql_error());

// if no results
if (mysql_num_rows($rollCallResult) == 0) {
    ?>
    <div class=" col-lg-9 col-md-9 roll-call"
         style="background:white;border-radius:10px;margin-top:20px;border:2px solid black;" align="left">
        No Results
    </div>
<?php }
if (mysql_num_rows($rollCallResult) > 0) {
    while ($rows = mysql_fetch_assoc($rollCallResult)) {
        $memberID = $rows['MemberID'];
        $name = $rows['FirstName'] . ' ' . $rows['LastName'];
        $profilePhoto = $rows['ProfilePhoto'];
        $category = $rows['Category'];
        $post = $rows['Post'];
        $postID = $rows['PostID'];
        $postOwner = $memberID;
        $userName = $rows['Username'];
        ?>




<div class=" col-lg-9 col-md-9 roll-call "
     align="left">

    <?php if ($memberID == $ID) {
        $profilePath = "<a href='/profile.php/$userName'>";
    }
    else {
        $profilePath = "<a href='/profile_public.php/$userName'>";
    }
    ?>

    <?php echo $profilePath; ?>
    <img src="/poster/<?php echo $profilePhoto ?>" class="profilePhoto-Feed enlarge-onhover " alt=""
         title="<?php echo $name ?>" /> &nbsp <b><font size="4"><?php echo $name ?></font></b>
    </a>

    <div class="post">
        <?php
        if (strlen($post) > 700) {
            $post500 = substr($post, 0, 700); ?>

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

    <a href='/post-interest.php?interest=<?php echo urlencode($category) ?>' class='category'><h5><?php echo $category ." ". interestGlyphs($category) ?></h5></a>
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
    echo '</div>';
    //-------------------------------------------------------------
    // End of approvals
    //-----------------------------------------------------------
    ?>

    <!--Share Post to other social media -->
    <br/>
    <?php
    $showPostURL = "http://rapportbook.com/show_post.php?postID=$postID";
    $twitterLogo = "<img src='/images/twitter-logo-red.png' height='50px' width='50px' alt='Twitter'/>";
    $facebookLogo = "<img src='/images/facebook-logo-red.png' height='50px' width='50px' alt='Facebook'/>";
    $tumblrLogo = "<img src='/images/tumblr-logo-red.png' height='40px' width='40px' alt='Tumblr'/>";
    ?>
    Share This Post:
    <a href = "http://twitter.com/share?text=<?php echo strip_tags($post) ?>&url=<?php echo $showPostURL ?>&hashtags=Rapportbook" target="_blank"><?php echo $twitterLogo ?></a>
    <a href="http://www.facebook.com/sharer/sharer.php?t=<?php echo strip_tags($post) ?>&u=<?php echo $showPostURL ?>" target="_blank"><?php echo $facebookLogo ?></a>
    <a href="http://www.tumblr.com/share/link?url=<?php echo urlencode($showPostURL) ?>&amp;name=&amp;description=<?php echo strip_tags($post) ?>" target="_blank"><?php echo $tumblrLogo ?></a>

    <div style="padding-top:10px;padding-bottom:10px;margin-top:10px;">
        <form method="post" action="" enctype="multipart/form-data"
              onsubmit="showCommentUploading('comment<?php echo $postID?>', this);">

            <input type="text" class="form-control" name="postComment" id="postComment"
                   placeholder="Write a comment" title='' style="border:1px solid black"/>

            <strong style="color:red">Attach A Photo,Video or Music File To Your Comment</strong>
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
        if (mysql_num_rows($result3) > 0) {
            echo '<div class="comment-style">';
            while ($rows3 = mysql_fetch_assoc($result3)) {
                $comment = $rows3['PostComment'];
                $profilePhoto = $rows3['ProfilePhoto'];
                $commentID = $rows3['PostCommentID'];
                $commentOwner = $rows3['MemberID'];
                echo '<div class="comment-row">';
                echo '<div class="user-icon"><img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" style = "border:1px solid black" class ="enlarge-onhover img-responsive" /><div class="user-name">' . $rows3['FirstName'] . ' ' . $rows3['LastName'] . '</div></div><div class="comment-content">' . nl2br($comment) . '</div>';
                echo '</div>';
                if ($postOwner == $ID) {
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
                $commentOwner = $rows4['MemberID'];
                echo '<div class="user-icon">';
                echo '<img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" style = "border:1px solid black" class ="enlarge-onhover img-responsive" /><div class="user-name">' . $rows4['FirstName'] . $rows['LastName'] . '</div></div><div class="comment-content">' . nl2br($comment) . '</div>';
                echo '</td></tr>';
            }
            echo '</div>';
            if ($postOwner == $ID) {
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
    ?>
</div>
<!--Right Column -->
<div class="col-md-3 col-lg-3 col-md-offset-9 col-lg-offset-9 ad-desktop hidden-sm hidden-xs rightColumn" >
    <h3><a href="advertising.php">Advertise
            <img src="<?php echo $imagesPath ?>ad-pic.jpg" style="border-bottom:1px solid black;" />
        </a></h3>
    <?php
    $rightColumnAds = getRightColumnAds($genre, $age, $state, $interests);
    $rightColSql = $rightColumnAds;
    $rightColResult = mysql_query($rightColSql) or die(mysql_error());
    //$rows = mysql_fetch_assoc($result);
    if (mysql_num_rows($rightColResult) > 0) { ?>

        <div style="padding:10px;width:200px;">
            <?php
            while ($rightColRows = mysql_fetch_assoc($rightColResult)) {
                echo $rightColRows["Post"];
                ?>
                <hr class="ad-border" />
                <?php
            } ?>
        </div>
        <?php
    }
    ?>

<?php
require 'connect.php';
require 'html_functions.php';
require 'mediaPath.php';
require 'getSession_public.php';
get_head_files();
get_header()
?>

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


    <div class="col-xs-12 col-md-12 col-lg-12 roll-call" >
        <div align="left">
            <?php if (empty($_SESSION['ID']) || !isset($_SESSION['ID'])) { ?>
            <a href="/index.php" ><h4>Login or Sign Up</h4></a></h4>
            <?php } else { ?>
            <a href="/home.php">Back to Roll Call</a>
            <?php } ?>

            <span class="lead" style="color:red;font-weight:bold">Network By Interests With People Around You.</span>

                <img src="<?php echo $imagesPath ?>interests-lg.JPG" />

                <br/><br/>

                    <img src="/images/camera.png" height="50" width="50" />
                    &nbsp;&nbsp;<span class="lead">
                    Post Photos & Videos
                    </span>


                    <br/><br/>


                    <img src="/images/share-people.png" height="50" width="50" />
                    &nbsp;&nbsp;<span class="lead">
                    Share Information.
                    </span>

                    <br/><br/>

                    <img src="/images/local.png" height="50" width="50" />
                    &nbsp;&nbsp;<span class="lead">
                    Discover Your Local Area.
                    </span>





            <br/><br/>

</div>

    <h5 style="color:red;">Checkout what people are sharing</h5>

<?php


$genreCondition = "And Posts.Category > '' ";


if (!empty($searchState)) {
    $stateCondition = "AND (Profile.State = '$searchState' AND Profile.City = '$searchCity')";
}
else {
    $stateCondition = "";
}

$limit = "10";
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
         style="background:white;border-radius:10px;margin-top:20px;border:2px slategray;border-style:dashed" align="left">
        No Results
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
         style="background:#f5f8fa;border-radius:10px;margin-top:20px;border:2px solid slategray;"
         align="left">

        <?php
        $profileUrl = "";
        if ($memberID == $ID) {

            $profileUrl = "/profile.php/$username";
        }
        else {
            $profileUrl = "/profile_public.php/$username";
        }
        ?>

        <a href="<?php echo $profileUrl ?>">
            <img src="<?php echo $mediaPath. $profilePhoto ?>" class="profilePhoto-Feed enlarge-onhover " alt=""
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

        <h4 style="color:red;font-weight:bold"><?php echo $category ?></h4>

    <?php

    if (!isset($ID)) {
        $readonly = "readonly";
    }
    else {
        $readonly = "";
    }

    echo "<br/><br/>";

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
                        echo '<input type ="button" class = "btnApprove"'. $readonly .' />';
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

            // comments
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

    } ?>

        </div>



<?php
}
}
?>

<?php get_footer_files() ?>
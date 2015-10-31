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

<body style="background:white;">






        <div align="left" style="padding-left:10px;">
            <?php if (empty($_SESSION['ID']) || !isset($_SESSION['ID'])) { ?>
            <a href="../" >
                <button style="background:red;color:white;padding:10px;border: 2px solid black;border-radius: 10px;">
                    <span style="font-weight:bold;">Login or Sign Up</span>
                </button> <span style="font-weight:bold;font-size:26px;">Get Connected!</span>
            </a>
            <?php } else { ?>
            <a href="/home">Back</a>
            <?php } ?>

            <h4 class="lead" style="color:red;font-weight:bold">Connect With People By Interests In The Same Local Area.</h4>

                <img src="<?php echo $imagesPath ?>interests-lg.JPG" />

                <br/><br/>

                    <img src="/images/camera.png" height="30" width="30" />
                    <span class="lead">
                    Post Photos & Videos of Your Interests
                    </span>


                    <br/><br/>


                    <img src="/images/share-people.png" height="30" width="30" />
                    <span class="lead">
                    Share Information.
                    </span>

                    <br/><br/>

                    <img src="/images/local.png" height="30" width="30" />
                    <span class="lead">
                    Discover Things Around You.
                    </span>


            <br/><br/>


            Rapportbook Founder & CEO Chris Weathers speaks on why he became an entrepreneur.
            <br/>
            <iframe max-width="400" height="315" src="https://www.youtube.com/embed/qiFa1-Mtj8c" frameborder="0" allowfullscreen></iframe>

            <h2 style="font-family: cursive;color:green;text-decoration: underline;">Testimonials</h2>

<!--Testimonial 1 -->
            <div class="row">
                <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1" style="margin-right:-3%">
                    <img src="/images/brian-slawin.jpg" style="border:1px solid black;height:50px;width:50px;"/>
                </div>
                <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
                   <div style="font-style: italic">
                        "Rapportbook is all about local reach and I really like that." - <b>Brian Slawin</b>, Co-Founder, <a href="http://busyevent.com" target="_blank">Busy Event </a>
                        </div>
                </div>
            </div>
<!--End Testimonial 1 -->

            <br/>

            <!--Testimonial 2 -->
            <div class="row">
                <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1" style="margin-right:-3%">
                    <img src="/images/dr-mccarthy.jpg" style="border:1px solid black;height:50px;width:50px;"/>
                </div>
                <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
                    <div style="font-style: italic">
                        "Rapportbook is great for connecting communities." - <b>Dr. Lance McCarthy</b>, <a href="http://tedxgatewayarch.org/lance-mccarthy/" target="_blank">Economist, Investment Advisor, Urban Leader</a>
                    </div>
                </div>
            </div>
            <!--End Testimonial 1 -->

            <br/>

            <h4>Follow Us:</h4>
            <a href="http://facebook.com/rapportbook" target="_blank"><img src="/images/facebook-logo-red.png" height="50" width="50"></a>
            <a href="http://twitter.com/rapportbook" target="_blank"><img src="/images/twitter-logo-red.png" height="=50" width="50"></a>
            <a href="http://tumblr.com/blog/rapportbook" target="_blank"><img src="/images/tumblr-logo-red.png" height="40" width="40"/> </a>
            <a href="http://instagram.com/officialrapportbook" target="_blank"><img src="/images/instagram-logo-red.png" height="50" width="50"/></a> </a>
            <a href="http://pintrest.com/rapportbook" target="_blank"><img src="/images/pintrest-logo-red.png" height="50" width="50" /> </a>

            <h4 style="color:red;">Checkout what people are sharing</h4>
</div>


        <!--FEED STARTS HERE -->
        <div class="col-lg-12 col-md-12 col-xs-12" style="background-color: #888888;style=">
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
            $profileUrl = "/profile/$username";
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
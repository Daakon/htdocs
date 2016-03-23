<?php
require 'imports.php';

get_head_files();
get_header();
$_SESSION['ID'] = $ID;
if (!empty($_SESSION['ID'])) {
    $ID = $_SESSION['ID'];
}
?>



    <script>
        function checkSignup() {

            // check email
            var email = document.getElementById('email').value;
            var filter = /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i;
            if (!filter.test(email)) {
                alert('Please provide a valid email address');
                return false;
            }

            // check state
            var ddState = document.getElementById('State');
            var state = ddState.options[ddState.selectedIndex].value;

            if (state == '') {
                alert('State needed');
                return false;
            }

            return true;

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

    <style>
        video {
            width:70%;
            height: auto;
            object-fit: inherit;
        }
        iframe {
            width: 90%;
            max-width: 90%;
        }

        .input-lg {
            max-width: 95%;
        }
    </style>

    <body class="index">

    <div align="left" >

        <div class="row" style="margin-top:-20px;">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" >

                <div class="visible-xs" style="padding-left:10px;padding-top:10px;">
                    <a href="/login-mobile"><button class="btn btn-default">Login</button></a> &nbsp;&nbsp;
                    <a href="#signup"><button class="btn btn-default">Sign Up</button></a>
                </div>

                <div class="hidden-xs" style="padding-left:10px;padding-top:10px;">
                    <a href="../"><button class="btn btn-default">Login</button></a> &nbsp;&nbsp;
                    <a href="#signup"><button class="btn btn-default">Sign Up</button></a>
                </div>

                <!--Mobile -->
                <div class="visible-xs">

                    <table style="margin-left:10px;margin-top:20px;margin-bottom:10px;">
                        <tr>
                            <td>
                                <span style="font-size:20px;">Play Games & Shop on Us!</span>
                            </td>
                        </tr>
                        <tr>
                            <td><a href="/support">View Games & Rules</a></td>
                        </tr>
                    </table>
                </div>

                <!--Desktop -->
                <div class="hidden-xs">

                    <table style="margin-left:10px;margin-top:20px;">
                        <tr>
                            <td>
                                <span class="lead" style="font-weight:bold;">Play Games & Shop on Us!</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a style ="font-size:20px;" href="/support">View Games & Rules</a>
                            </td>
                        </tr>
                    </table>

                </div>


            </div>


            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="margin-top:0px;">
                <img style="float:left;" src="/images/trophy.jpg" width="50%" />
                <img style="float:right;" src="/images/giftcard.jpg" width="50%" />
            </div>

        </div>

        <div class="row" style="background:#e3e3e3;padding-left:10px;">
            <a id="signup"></a>
            <div style="padding-left:20px;">
                <h1 class="bold">Sign Up</h1>
                <span class="lead" style="font-weight: 500;">It's Free!</span>
                <br/><br/>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <!--                    <img src="--><?php //echo $imagesPath ?><!--NetworkGraphic.png" />-->


                <form method="post" action="signup.php" id="signup" onsubmit='return checkSignup();' >

                    <div class="form-group row" id="form-group-email">
                        <div class="col-md-6">
                            <label class="sr-only" for="email">Email Address</label>
                            <input class="form-control input-lg" type="email" name="email" id="email"
                                   placeholder="Email"/>
                        </div>
                        <div class="col-md-6">
                            <div class="error-text"></div>
                        </div>
                    </div>

                    <br/>

                    <small>By clicking sign up, you agree to our <a href="/terms">terms</a></small>
                    <br/><br/>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <input class="btn btn-default " type="submit" name="signup" id="signup"
                                   style="background:#E30022;color:white;"     value="Sign Up"/>

                        </div>
                    </div>

                </form>
                <hr/>
                <h4>
                    <div class="visible-xs">
                        <a href="/login-mobile"><div class="btn btn-default">Login</div></a> <a href="/support">Support</a>
                    </div>

                    <div class="hidden-xs">
                        <a href="../"><div class="btn btn-default">Login</div></a> &nbsp;&nbsp;<a href="/support">Support</a>
                    </div>


                </h4>

            </div>

            <div class="col-lg-5 col-lg-offset-1 col-md-5 col-md-offset-1 col-sm-12 col-xs-12">
                <h5>Follow us to stay up on new games and giveaways:</h5>
                <a href="http://facebook.com/rapportbook" target="_blank"><img src="/images/facebook-logo-red.png" height="50" width="50"></a>
                <a href="http://twitter.com/rapportbook" target="_blank"><img src="/images/twitter-logo-red.png" height="=50" width="50"></a>
                <a href="http://blog.rapportbook.com" target="_blank"><img src="/images/tumblr-logo-red.png" height="40" width="40"/> </a>
                <a href="http://instagram.com/officialrapportbook" target="_blank"><img src="/images/instagram-logo-red.png" height="50" width="50"/></a>
                <a href="http://pintrest.com/rapportbook" target="_blank"><img src="/images/pintrest-logo-red.png" height="50" width="50" /> </a>
                <a href="http://linkedin.com/company/rapportbook" target="_blank"><img src="/images/linkedin-logo-red.png" height="40" width="40" /></a>
                <a href="https://plus.google.com/+Rapportbook/" target="_blank"><img src="/images/google-youtube-logo.png" height="40" width="50" style="padding-left:8px;" /></a>
                <br/>

                <div style="margin-top:60px;">


                    <!--Content Goes Here -->

                </div>

            </div>
        </div>

        <br/>

        <div class="hidden-xs">
            <h3 align="center" style="margin-left:-100px;"><img src="/images/stars.png" height="50" width="150" style="padding-right:10px;" />Featured Member<img src="/images/stars.png" height="50" width="150" style="padding-left:10px;" /></h3>
        </div>

        <div class="visible-xs">
            <h4 align="center" style="margin-left:-10px;">
                <img src="/images/stars.png" height="50" width="50" style="padding-right:10px;" />Featured Member<img src="/images/stars.png" height="50" width="50" style="padding-left:10px;" />
            </h4>
        </div>
    </div>


    <!--FEED STARTS HERE -->
    <div class="col-lg-12 col-md-12 col-xs-12" style="background-color: #e3e3e3;" align="center">
        <?php


        $sqlRollCall = " SELECT DISTINCT
    Posts.Post As Post,
    Members.ID As MemberID,
    Members.FirstName As FirstName,
    Members.LastName As LastName,
    Members.Username As Username,
    Posts.ID As PostID,
    Posts.Category As Category,
    Posts.PostDate As PostDate,
    Posts.IsSponsored As IsSponsored,
    Profile.ProfilePhoto As ProfilePhoto,
    Profile.City As City,
    Profile.State As State
    FROM Members,Posts,Profile
    WHERE
    Members.IsActive = 1
    And (Members.IsSuspended = 0)
    And (Members.ID = Posts.Member_ID)
    And (Members.ID = Profile.Member_ID)
    And (Posts.IsDeleted = 0)
    And (Posts.ID = 314)
    AND (Posts.Category <> 'Sponsored') ";

        // last member of the month post = 259
        $rollCallResult = mysql_query($sqlRollCall) or die(logError(mysql_error(), $url, "Getting Learn more feed"));

        // if no results
        if (mysql_num_rows($rollCallResult) == 0) {
            ?>
            <div class="col-lg-offset-4 col-lg-8 col-md-offset-4 col-md-8 roll-call" >
                No Results
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
        $postDate = $rows['PostDate'];
        $city = $rows['City'];
        $state = $rows['State'];
        $isSponsored = $rows['IsSponsored'];
        $postOwner = $memberID;
        ?>

        <div class="col-lg-5 col-lg-offset-3 col-md-5 col-md-offset-3 col-sm-6 col-xs-12 roll-call" align="left"
             style="margin-bottom: 20px;margin-top:10px;">

            <?php
            $profileUrl = "#signup";
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
                <div class="date"><?php echo date('l F j, Y',strtotime($postDate)); ?>
                    <?php if ($isSponsored) { echo "<br/>Sponsored"; } ?>
                </div>
            </div>



            <div class="post" style="clear:both;">

                <?php
                // remove excessive white space inside anchor tags
                $post = preg_replace('~>\s+<~', '><', $post);
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

            <hr />

            <a href="#signup" >
                <span class='engageText'>#<?php echo $category ?></span>
                &nbsp;
                <button >
                    <span >Connect </span>
                </button>
            </a>

            <br/><br/>

            <?php echo $city.', '. $state ?>

            &nbsp;&nbsp;&nbsp;
            <?php
            $postPath = getPostPath();
            $shareLinkID = "shareLink$postID"; ?>
            <a href="javascript:showLink('<?php echo $shareLinkID ?>');">
                <img src="/images/share.gif" height="50px" width="50px" />
                <span style="color:black;font-weight:bold;">Share This Post</span>
            </a>

            <?php $shareLink = 'show_post?postID='.$postID.'&email=1';
            $shareLink = $postPath.$shareLink;
            $shortLink = shortenUrl($shareLink);
            ?>
            <input id="<?php echo $shareLinkID ?>" style="display:none;" value ="<?php echo $shortLink ?>" />



            <?php

            if (!isset($ID)) {
                $readonly = "readonly";
            }
            else {
                $readonly = "";
            }

            echo "<hr/>";

            //check if member has approved this post
            //----------------------------------------------------------------
            //require 'getSessionType.php';
            $sql2 = "SELECT ID FROM PostApprovals WHERE Post_ID = '$postID' AND Member_ID = '$ID'";
            $result2 = mysql_query($sql2) or die(logError(mysql_error(), $url, "Getting member approval"));
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
                echo '<input type ="button" class = "btnApprove"'. $readonly .' />';
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
            $result3 = mysql_query($sql3) or die(logError(mysql_error(), $url, "Getting post comments 0-3"));
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
                    <img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" class ="enlarge-onhover img-responsive" />
                    </a>
                    </div>

                     <div class="commentNameWrapper-Feed">
                      <a href='.$commenterProfileUrl.'>
                        <div class="profileName-Feed"><?php echo $name ?> ' .
                        $rows3['FirstName'] . ' ' . $rows3['LastName'] .
                        '</div>
                     </a><br/>
                     ' .nl2br($comment).'
                    </div>

                    <div class="comment-content" style="clear:both"></div>';
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
            $result4 = mysql_query($sql4) or die(logError(mysql_error(), $url, "Getting comments 3-100"));

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

                    echo '<div class="comment-row">';
                    echo '<a href='.$commenterProfileUrl.'>';
                    echo '<div class="profileImageWrapper-Feed">';
                    echo '<img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" class ="enlarge-onhover img-responsive" />
                    </a></div>

                    <div class="commentNameWrapper-Feed">
                    <a href='.$commenterProfileUrl.'>
                    <div class="profileName-Feed">' . $rows4['FirstName'] .' '. $rows4['LastName'] .
                        '</div>
                        </a><br/>' . nl2br($comment) .
                        '</div>
                        </div><div class="comment-content" style="clear:both">
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

                } ?>


            </div>


            <?php
            }
            }
            ?>

<?php get_footer_files() ?>
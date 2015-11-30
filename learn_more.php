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


            // check interest
            var ddInterest = document.getElementById('interest');
            var interest = ddInterest.options[ddInterest.selectedIndex].value;

            if (interest == '' || year.length == 0) {
                alert('Interest needed');
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
        width:90%;
        max-width: 90%;
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

<div class="row" style="margin-top:-20px;" >
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" >

            <h4 class="padding-left-10">
                    <span class="bold">
                        <img src="/images/bullhorn.png" height="50" width="30" />
                    </span> <span class="lead slogan" style="margin-left:-5px;">Promote</span> <span class="lead">Your Business.</span>
            </h4>

            <h4 class="padding-left-10">
                <img src="/images/location.png" height="40" width="25" />
                <span class="lead" style="margin-left:-3px;">Make Local or National</span> <span class="lead slogan">Connections</span>.
            </h4>

            <h4 class="padding-left-10">
                    <span class="bold">
                        <img src="/images/share-people.png" height="50" width="25" />
                    </span><span class="lead" style="margin-left:-3px;">Meet People Who Need Your </span> <span class="lead slogan">Product.</span>
            </h4>

<br/>
        </ul>


    </div>


    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="margin-top:-20px;">
        <img src="/images/business-networking.jpg" />
        </div>

</div>

    <div style="background:#E30022;color:white;padding-left:10px;padding-top:10px;">
<h4>It's as easy as...</h4>
    <b>1. Signing up</b> <i>(Only 2 Fields)</i>.
    <br/>
    <b>2. Post What Your Product Offers</b>.
    <br/>
    <b>3. Start Networking & Building Resources</b></h4>

    <br/>

    <h4>Watch the video to learn more.</h4>
    <video width="320" height="240" poster="/poster/how-it-works-poster.png" controls >
        <source src="/images/Rapportbook-How-It-Works-Lo-Res.mp4" />
        </video>
    <br/>
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

                        <select class="form-control input-lg" id="interest" name="interest">
                            <option value="">Select Your Business Type</option>
                            <?php echo category() ?>
                        </select>
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

                    <h4>
                        <div class="visible-xs">
                            <a href="/login-mobile.php">Login</a>
                        </div>

                        <div class="hidden-xs">
                            <a href="../">Login</a>
                        </div>
                    </h4>

                </div>

                <div class="col-lg-5 col-lg-offset-1 col-md-5 col-md-offset-1 col-sm-12 col-xs-12">
                    <h4 style="font-weight: bold">Follow Us:</h4>
                    <a href="http://facebook.com/rapportbook" target="_blank"><img src="/images/facebook-logo-red.png" height="50" width="50"></a>
                    <a href="http://twitter.com/rapportbook" target="_blank"><img src="/images/twitter-logo-red.png" height="=50" width="50"></a>
                    <a href="http://tumblr.com/blog/rapportbook" target="_blank"><img src="/images/tumblr-logo-red.png" height="40" width="40"/> </a>
                    <a href="http://instagram.com/officialrapportbook" target="_blank"><img src="/images/instagram-logo-red.png" height="50" width="50"/></a> </a>
                    <a href="http://pintrest.com/rapportbook" target="_blank"><img src="/images/pintrest-logo-red.png" height="50" width="50" /> </a>
                    <a href="http://linkedin.com/company/rapportbook" target="_blank"><img src="/images/linkedin-logo-red.png" height="40" width="40" /></a> </a>

                    <br/>

                    <div style="margin-top:60px;">


                    <!--Content Goes Here -->

                        </div>

                </div>
            </div>


            <div class="row" >
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" >

                    <h3 style="padding-left:10px;">Testimonials</h3>

                    <!--Testimonial 1 -->
                    <div class="row">
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2" >
                            <img src="/images/brian-slawin.jpg" class="testimony-img"/>
                        </div>
                        <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                            <div class="testimony-text" >
                                "Rapportbook is a great platform for promoting your product on a local or national scale."  - <b>Brian Slawin</b>, <a href="http://busyevent.com" target="_blank">Co-Founder, Busy Event </a>
                            </div>
                        </div>
                    </div>
                    <!--End Testimonial 1 -->

                    <hr/>

                    <!--Testimonial 2 -->
                    <div class="row">
                        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2" >
                            <img src="/images/angela-larocca.jpg" class="testimony-img" />
                        </div>
                        <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
                            <div class="testimony-text">
                                "Chris Weathers has a genuine passion for connecting people with similar interests, and Rapportbook  seamlessly allows you to interact with like-minded individuals." - <b>Angela LaRocca</b>, <a href="http://unidev.com" target="_blank">Brand Manager, Unidev</a>
                            </div>
                        </div>
                    </div>
                    <!--End Testimonial 2 -->
                    <hr/>

                    <br/>

                    <h5 class="padding-left-10">Rapportbook Communications Director Erin Thompson gives a talk about the company.</h5>
                    <a href="https://www.youtube.com/watch?v=jJMHsWxqF3M&t=27m45s" target="_blank" >
                        <h4 class="padding-left-10">
                            Watch Video
                        </h4>

                        <img src="/images/Erin-Thompson.jpg" width="90%" height="350" class="padding-left-10">
                    </a>

                    <hr/>
</div>


                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 padding-left-10 margin-top-10" >

                       <h5 class="padding-left-10">
                        Rapportbook Founder & CEO Chris Weathers speaks on why he became an entrepreneur.
                       </h5>
                        <iframe max-width="400" height="315" src="https://www.youtube.com/embed/qiFa1-Mtj8c" frameborder="0" allowfullscreen class="padding-left-10"></iframe>

                        <br/><br/>


                    </div>
                </div>

            <br/>


            <h5 class="padding-left-10">Checkout what people are sharing</h5>
</div>


        <!--FEED STARTS HERE -->
        <div class="col-lg-12 col-md-12 col-xs-12" style="background-color: #e3e3e3;">
<?php


$genreCondition = "And Posts.Category > '' ";


if (!empty($searchState)) {
    $stateCondition = "AND (Profile.State = '$searchState' AND Profile.City = '$searchCity')";
}
else {
    $stateCondition = "";
}

$limit = "100";
$sqlRollCall = " SELECT DISTINCT
    Posts.Post As Post,
    Members.ID As MemberID,
    Members.FirstName As FirstName,
    Members.LastName As LastName,
    Members.Username As Username,
    Posts.ID As PostID,
    Posts.Category As Category,
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
    AND (Posts.Category <> 'Sponsored')
    $genreCondition
    $stateCondition
    Group By PostID
    Order By PostID DESC LIMIT $limit ";
$rollCallResult = mysql_query($sqlRollCall) or die(mysql_error());

// if no results
if (mysql_num_rows($rollCallResult) == 0) {
    ?>
    <div class="col-lg-offset-2 col-lg-8 col-md-offset-2 col-md-8 roll-call about-roll-call" align="left">
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
        $city = $rows['City'];
        $state = $rows['State'];
        $postOwner = $memberID;
        ?>

        <div class="col-lg-5 col-lg-offset-1 col-md-6 col-sm-6 col-xs-12 about-roll-call" align="left">

        <?php
            $profileUrl = "#signup";
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
            if (strlen($post) > 700) {
                $post500 = substr($post, 0, 700); ?>

                <div id="short<?php echo $postID ?>">
                    <?php echo nl2br($post500) ?>...<a href="javascript:showPost('long<?php echo $postID ?>', 'short<?php echo $postID ?>');">Show More</a>
                </div>
                <?php
                echo "<div id='long$postID' style='display:none;'>";
                echo nl2br($post);

                ?>

                <br/><br/>

                <?php
                $messageLink;
                if (!empty($_SESSION['ID']) && $memberID != $ID) {
                    $messageLink = "#signup";
                }
                else {
                    $messageLink = '#signup';
                }
                ?>

                <a href="<?php $messageLink ?>">
                    <button>
                        <span>Connect With <?php echo $firstName ?></span>
                    </button>
                </a>

                <?php

                echo "</div>";
            }
            else {
                echo nl2br($post);
                ?>

                <br/><br/>

            <a href="#signup" >
                <button >
                    <span >Connect With <?php echo $firstName ?></span>
                </button>
            </a>

            <?php
            }
            ?>
        </div>

        <h4 class='category'>#<?php echo $category ?></h4>

            <br/>
            <?php echo $city.', '. $state ?>
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
            echo '<img src = "' . $mediaPath . $profilePhoto . '" height = "50" width = "50" class ="enlarge-onhover img-responsive border-1" /><div class="user-name">' . $rows4['FirstName'] .' '. $rows4['LastName'] . '</div></div><div class="comment-content">' . nl2br($comment) . '</div>';
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
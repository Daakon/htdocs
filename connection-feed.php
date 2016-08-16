<?php
$ID = $_SESSION['ID'];
$sql1 = "SELECT BlockedID, BlockerID FROM Blocks WHERE (BlockerID = $ID Or BlockedID = $ID)";
$result1 = mysql_query($sql1) or die(logError(mysql_error(), $url, ""));

$blockIDs = array();

//Get blocked IDs
while ($rows1 = mysql_fetch_assoc($result1)) {
    $blockedID = $rows['BlockedID'];
    $blockerID = $rows['BlockerID'];
    if ($blockedID != $ID) {
        array_push($blockIDs, $rows1['BlockedID']);
        if ($blockerID != $ID) {

            array_push($blockIDs, $rows1['BlockerID']);
        }
    }
}

//$ads = getAds($genre, $age, $state, $interests, $gender);
$sqlRollCall = " SELECT DISTINCT
Posts.Post As Post,
Posts.PostDate As PostDate,
Posts.Member_ID As MemberID,
Posts.ID As PostID,
Posts.Reposter_ID as ReposterID,
Posts.OrigPost_ID as OrigPostID,
Members.FirstName As FirstName,
Members.LastName As LastName,
Members.Username As Username,
Profile.ProfilePhoto As ProfilePhoto,
(Select count(PostID) From PostApprovals Where Post_ID = PostID) As Likes
FROM Members,Posts,Profile
WHERE
Members.IsActive = 1
And (Members.IsSuspended = 0)
And (Members.ID = Posts.Member_ID)
And (Members.ID = Profile.Member_ID)
And (Posts.IsDeleted = 0)
And ((Posts.Member_ID = $ID) Or (Posts.Reposter_ID = $ID) Or (Posts.Member_ID in (Select Followed_ID FROM Follows WHERE Follower_ID = $ID)))
And (Members.ID Not in ( '" . implode($blockIDs, "', '") . "' ))
$lastPostCondition
Group By PostID

Order By PostID DESC Limit $limit";
$rollCallResult = mysql_query($sqlRollCall) or die(logError(mysql_error(), $url, "Getting Connection Feed data"));



// if no results
if (mysql_num_rows($rollCallResult) == 0) {
    ?>
    <div class="col-lg-offset-3 col-lg-6 col-md-offset-3 col-md-6 roll-call-feed"
         align="left" style="padding-left:10px;">

        <h4>Suggested profiles to follow</h4>
        <?php

        $playdoeID = 22;
        $sqlPlaydoeFollow = "SELECT * FROM Follows WHERE Follower_ID = $ID And Followed_ID = $playdoeID ";
        $resultPlaydoeFollow = mysql_query($sqlPlaydoeFollow) or die (logError(mysql_error(), $url, "Getting Follows"));
        $playdoeName = get_users_name(22);
        $playdoeUsername = get_username(22);
        if (mysql_num_rows($resultPlaydoeFollow) == 0) {
            $followPlaydoePhoto = get_users_photo_by_id(22);
            echo "<a href='/$playdoeUsername'><img src='$followPlaydoePhoto' height='50' width='50' /> $playdoeName</a>";

        }

        echo "<hr class='hr-line'>";

        $newsID = 528;
        $sqlNewsFollow = "SELECT * FROM Follows WHERE Follower_ID = $ID And Followed_ID = $newsID ";
        $resultNewsFollow = mysql_query($sqlNewsFollow) or die (logError(mysql_error(), $url, "Getting Follows"));
        $newsName = get_users_name(528);
        $newsUsername = get_username(528);
        if (mysql_num_rows($resultNewsFollow) == 0) {
            $followNewsPhoto = get_users_photo_by_id(528);
            echo "<a href='/$newsUsername'><img src='$followNewsPhoto' height='50' width='50' /> $newsName</a>";

        }

        ?>

    </div>
<?php }
if (mysql_num_rows($rollCallResult) > 0) {
    $total = mysql_num_rows($rollCallResult);
    $counter = 0;
    while ($rows = mysql_fetch_assoc($rollCallResult)) {
        // determine the last Post ID and use that for JS to find
        // this lastPostID value will be passed to JS in home.php then to a query string
        // loadMoreConnections.php will take this value and pass it back to this file
        // so the query only queries rows less that the lowest one
        if (++$counter == $total) {
            $lastPostID = $rows['PostID'];
            echo "<input type='hidden' id='lastPostID' value='".$lastPostID."' />";
        }

        $memberID = $rows['MemberID'];
        $firstName = $rows['FirstName'];
        $lastName = $rows['LastName'];
        if (strlen($lastName) > 0) {
            $name = $rows['FirstName'] . ' ' . $rows['LastName'];
        } else {
            $name = $rows['FirstName'];
        }

        $username = $rows['Username'];
        $profilePhoto = $rows['ProfilePhoto'];
        $category = $rows['Category'];
        $post = $rows['Post'];
        $postID = $rows['PostID'];
        $postDate = $rows['PostDate'];
        $isSponsored = $rows['IsSponsored'];
        $postOwner = $memberID;
        $lastPostID = $rows['LastPostID'];

        $reposterID = $rows['ReposterID'];
        $origPostID = $rows['OrigPostID'];
        ?>


        <div class="col-lg-offset-3 col-lg-6 col-md-offset-3 col-md-6 col-sm-12 col-xs-12 roll-call-feed" >

            <?php
            $repostText = '';
            $img = '';
            $prestinePostID = $rows['PostID'];

            // check if post is a repost
            if (!empty($reposterID) && isset($reposterID) && $reposterID != 0) {

                $postID = $origPostID;

                if ($reposterID == $ID) {
                    $img = "<img src='/images/repost_icon.png' style='float:left;' height='20' width='20'/>";
                    $reposterName = get_users_name($ID);
                    $repostText = "$img You reposted <br/><br/>";
                    $reposterUsername = get_username($ID);
                    echo "<div style='margin-left:10px;color:#8899a6;float:left;'><a style='color:#8899a6' href='/$reposterUsername'>$repostText</a></div>";
                }
                else {
                    $img = "<img src='/images/repost_icon.png' style='float:left;' height='20' width='20'/>";
                    $reposterName = get_users_name($reposterID);
                    $reposterUsername = get_username($reposterID);
                    $repostText = $img . $reposterName ." reposted <br/><br/>";


                    echo "<div style='margin-left:10px;color:#8899a6;float:left;'><a style='color:#8899a6' href='/$reposterUsername'>$repostText</a></div>";
                }}

            $profileUrl = "/$username";
            ?>

            <div style="clear:both" class="profileImageWrapper-Feed">
                <a href="<?php echo $profileUrl ?>">
                    <img src="<?php echo $mediaPath. $profilePhoto ?>" class="profilePhoto-Feed" alt=""
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



            <div class="post" <?php echo $postStyle ?> style="clear:both;">
                <?php

                // remove excessive white space inside anchor tags
                $post = preg_replace('~>\s+<~', '><', $post);
                // trim white space
                $post = trim($post);
                // remove excessive line breaks
                $post = cleanBrTags($post);
                $post = preg_replace('/<input"[^>]+\>/i', "", $post);

                // check check post length if it has a url in it
                if (strstr($post, "http://") || strstr($post, "https://")) {
                    echo nl2br($post);

                }
                else if (strlen($post) > 700) {
                    $post500 = substr($post, 0, strpos($post, ' ', 700)).'<br/>'; ?>

                    <?php echo nl2br($post500) ?>
                    <br/>
                    <a href="#<?php echo $postID ?>"></a>
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

            <?php
            //check if member has approved this post
            //----------------------------------------------------------------
            //require 'getSessionType.php';
            $sql2 = "SELECT ID FROM PostApprovals WHERE Post_ID = '$postID' AND Member_ID = '$ID'";
            $result2 = mysql_query($sql2) or die(logError(mysql_error(), $url, "Getting member approval"));
            $rows2 = mysql_fetch_assoc($result2);
            // get approvals for each post
            $approvals = mysql_num_rows(mysql_query("SELECT * FROM PostApprovals WHERE Post_ID = $postID "));
            // show disapprove if members has approved the post
            echo '<div class="post-approvals postApprovalsAlign">';
            echo "<div id = 'approvals$postID'>";
            if (mysql_num_rows($result2) > 0) {
                echo '<form>';
                echo '<input type ="hidden" name="postID" class = "postID" id = "postID" value = "' . $postID . '" />';
                echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
                echo '<input type ="hidden" class = "memberID" id = "memberID" value = "' . $memberID . '" />';
                echo '<input type ="button" class = "btnDisapprove"'. $disabled.' />';
                if ($approvals > 0) {
                    echo '&nbsp;<span>' . $approvals . '</span>';
                }
                echo '</form>';
            } else {
                echo '<form>';
                echo '<input type ="hidden" name="postID" class = "postID" id = "postID" value = "' . $postID . '" />';
                echo '<input type ="hidden" class = "ID" id = "ID" value = "' . $ID . '" />';
                echo '<input type ="hidden" name="memberID" class = "memberID" id = "memberID" value = "' . $memberID . '" />';
                echo '<input type ="button" class = "btnApprove"'. $disabled.' />';
                if ($approvals > 0) {
                    echo '&nbsp;<span>' . $approvals . '</span>';
                }
                echo '</form>';
            }
            echo '</div>'; // end of approval div
            echo '</div>';
            //-------------------------------------------------------------
            // End of approvals
            //-----------------------------------------------------------


            if (isset($ID)) { ?>

                <?php if ($ID != $memberID) {?>
                    <a href="/view_messages/<?php echo $username ?>" class="messageEnvelope"><img src = "/images/messages.png" height="20" width="20" /></a>
                <?php } ?>

            <?php } ?>

            <div >

                <?php if ($ID != $memberID) { ?>

                    <?php if ($reposterID == $ID) { } else {
                        if (hasReposted($ID, $postID)) { $repostDisabled = "disabled"; } else { $repostDisabled = ''; }

                        ?>

                        <form class="repostAlign" action="" method="post" onsubmit="return confirm('Are you sure you want to repost this?') && saveScrollPositions(this)">
                            <input type="image" id="btnRepost" name="btnRepost" value="Repost" src="/images/repost_icon.png" style="margin-left:20px;margin-top:3px;" <?php echo $repostDisabled ?> />
                            <input type="hidden" id="memberID" name="memberID" value="<?php echo $memberID ?>" />
                            <input type="hidden" id="ownerID" name="ownerID" value="<?php echo $memberID ?>" />
                            <input type="hidden" id="postID" name="postID" value="<?php echo $postID ?>" />
                            <input type="hidden" id="postDate" name="postDate" value="<?php echo $postDate ?>" />
                            <input type="hidden" id="reposterID" name="reposterID" value="<?php echo $ID ?>" />
                            <input type="hidden" name="scrollx" id="scrollx" value="0"/>
                            <input type="hidden" name="scrolly" id="scrolly" value="0"/>
                        </form>

                    <?php } ?>


                    <?php $optionsID = "options$prestinePostID"; ?>

                    <a href="javascript:showOptions('<?php echo $optionsID ?>');" class="blockLink">...</a>


                    <?php
                    $postPath = getPostPath();
                    $shareLinkID = "shareLink$prestinePostID"; ?>
                    <a class="shareLink" href="javascript:showLink('<?php echo $shareLinkID ?>');">
                        <img style="margin-left:20px;" src="/images/share.png" height="25px" width="25px" />
                    </a>

                    <?php $shareLink = 'show_post?postID='.$postID.'&email=1';
                    $shareLink = $postPath.$shareLink;
                    $shortLink = shortenUrl($shareLink);
                    ?>


                <?php } ?>


                <?php


                if (isEmailValidated($ID) && hasOnePost($ID)) {
                    $disabled = '';
                } else {
                    $disabled = 'disabled';
                }


                //Detect device
                $iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
                $iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
                $iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
                $Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
                $webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");

                ?>

                <hr class="hr-line"/>
                <div class="content-space content-space-align">

                    <!--Show block button here show it displays clearly between engagement icons and comment box -->
                    <div style="display:none;" id="<?php echo $optionsID ?>">
                        <form action="" method="post" onsubmit="return confirm('Do you really want to block this member?') && saveScrollPositions(this) ">
                            <input type="hidden" id="blockedID" name="blockedID" class="blockedID" value="<?php echo $memberID ?>" />
                            <input type="hidden" id="ID" name="ID" class="ID" value="<?php echo $ID ?>" />
                            <input type="hidden" name="scrollx" id="scrollx" value="0"/>
                            <input type="hidden" name="scrolly" id="scrolly" value="0"/>
                            <input type="submit" id="block" name="block" class="btn btn-primary" style="margin-left:10px;background:red;" value="Block This User" />
                        </form>
                    </div>

                    <!--Show share link here show it displays clearly between engagement icons and comment box -->
                    <input id="<?php echo $shareLinkID ?>" style="display:none;margin-left:20px;" value ="<?php echo $shortLink ?>" />


                    <!--Comment box -->

                    <form style="width:100%;" class="commentBoxAlign" method="post" action="" enctype="multipart/form-data"
                          onsubmit="showCommentUploading('comment<?php echo $postID?>', this);">



                        <input type="file" style='float:left;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' name="flCommentMedia[]" id="flCommentMedia" multiple onchange='$("#upload-photo-info").html($(this).val());' />

                        <textarea style="margin-top:10px;float:left;border:none;font-size:17px;" name="postComment" id="postComment" onkeyup="this.style.height='24px'; this.style.height = this.scrollHeight + 12 + 'px';"
                                  placeholder="Write a comment" title='' ></textarea>
                        <br/><br/>

                        <label style="float:left;clear:both" for="flCommentMedia">
                            <img src="/images/camera.png" style="height:25px;width:25px;float:left;margin-right:10px;" />
                        </label>
                        <input type="submit" name="btnComment" id="btnComment" class="btn btn-primary" style="float:left;" Value="Comment"  />


                        <input type="hidden" name="postID" id="postID" class="postID" Value="<?php echo $postID ?>"/>
                        <input type="hidden" name="ID" id="ID" class="ID" value="<?php echo $ID ?>"/>
                        <input type="hidden" name="memberID" id="memberID" class="memberID" value="<?php echo $memberID ?>"/>
                        <input type="hidden" name="scrollx" id="scrollx" value="0"/>
                        <input type="hidden" name="scrolly" id="scrolly" value="0"/>

                        <br/><br/>

                        <span style="float:left;clear:both;margin-top:10px;" class='label label-info' id="upload-photo-info"></span>

                        <br/><br/>

                        <div id="comment<?php echo $postID ?>" style="display:none;float:left;clear:both;">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-danger active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" >
                                    <b>File uploading...please wait</b>
                                </div>
                            </div>
                        </div>

                    </form>




                    <?php
                    $sql3 = "SELECT DISTINCT
                        PostComments.Comment As PostComment,
                        PostComments.ID As PostCommentID,
                        PostComments.CommentDate As CommentDate,
                        Members.ID As CommentOwnerID,
                        Members.FirstName as FirstName,
                        Members.LastName As LastName,
                        Members.Username As Username,
                        Profile.ProfilePhoto As ProfilePhoto
                        FROM PostComments,Members, Profile
                        WHERE
                        PostComments.Post_ID = $postID
                        AND Members.ID = Profile.Member_ID
                        And Members.ID = PostComments.Member_ID
                        And (Members.ID Not in ( '" . implode($blockIDs, "', '") . "' ))
                        And PostComments.IsDeleted = 0
                        Group By PostComments.ID
                        Order By PostComments.ID DESC LIMIT 3 ";

                    $result3 = mysql_query($sql3) or die(logError(mysql_error(), $url, "Gettig first 3 Connection Feed comments"));
                    echo '<br/>';
                    if (mysql_num_rows($result3) > 0) {
                        echo '<div class="comment-style commentStyleAlign" >';
                        while ($rows3 = mysql_fetch_assoc($result3)) {
                            $comment = $rows3['PostComment'];
                            $profilePhoto = $rows3['ProfilePhoto'];
                            $commentID = $rows3['PostCommentID'];
                            $commentOwnerID = $rows3['CommentOwnerID'];
                            $commenterUsername = $rows3['Username'];
                            $commenterProfileUrl = "/$commenterUsername";
                            $commentDate = $rows3['CommentDate'];

                            //if (checkBlock($ID, $commentOwnerID)) { $display2 = "style= 'display:none;'"; } else { $display2 = "style='display:block;'"; }
                            echo '<div class="comment-row" >';
                            echo '<div class="profileImageWrapper-Feed">
                    <a href='.$commenterProfileUrl.'>
                    <img src = "' . $mediaPath . $profilePhoto . '" class="profilePhoto-Feed enlarge-onhover " />
                    </a>
                    </div>

                     <div class="commentNameWrapper-Feed">
                      <a href='.$commenterProfileUrl.'>
                        <div class="profileName-Feed"><?php echo $name ?> ' .
                                $rows3['FirstName'] . ' ' . $rows3['LastName'] .
                                '</div>
                     </a>
                     <div class="date">'. date('l F j, Y',strtotime($commentDate)) .'</div>

                     ' . nl2br($comment) . '

                    </div>

                    <div class="comment-content" style="clear:both"></div>';
                            echo '</div>';
                            if ($commentOwnerID == $ID || $memberID == $ID) {
                                //<!--DELETE BUTTON ------------------>
                                echo '<div class="comment-delete">';
                                echo '<form action="" method="post" onsubmit="return confirm(\'Do you really want to delete this comment?\') && saveScrollPositions(this) ">';
                                echo '<input type="hidden" name="commentID" id="commentID" value="' .  $commentID . '" />';
                                echo '<input type ="image" name="DeleteComment" id="DeleteComment" value="Delete" src="/images/delete.png" style="height:20px;width:20px" />';
                                echo '<input type="hidden" name="scrollx" id="scrollx" value="0"/>';
                                echo '<input type="hidden" name="scrolly" id="scrolly" value="0"/>';
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
                        PostComments.CommentDate As CommentDate,
                        Members.ID As CommentOwnerID,
                        Members.FirstName as FirstName,
                        Members.LastName As LastName,
                        Members.Username As Username,
                        Profile.ProfilePhoto As ProfilePhoto
                        FROM PostComments,Members, Profile
                        WHERE
                        PostComments.Post_ID = $postID
                        AND Members.ID = Profile.Member_ID
                        And Members.ID = PostComments.Member_ID
                        And (Members.ID Not in ( '" . implode($blockIDs, "', '") . "' ))
                        And PostComments.IsDeleted = 0
                        Group By PostComments.ID
                        Order By PostComments.ID DESC LIMIT 3, 100 ";

                    $result4 = mysql_query($sql4) or die(logError(mysql_error(), $url, "Gettig first 3 Connection Feed comments"));

                    $moreComments = "moreComments$postID";

                    if (mysql_num_rows($result4) > 0) {
                        ?>

                        <a href="javascript:showComments('<?php echo $moreComments ?>');">Show More</a>

                        <div id="<?php echo $moreComments ?>" style="display:none;">

                            <div class="comment-style commentStyleAlign">

                                <?php
                                while ($rows4 = mysql_fetch_assoc($result4)) {
                                    $comment = $rows4['PostComment'];
                                    $profilePhoto = $rows4['ProfilePhoto'];
                                    $commentID = $rows4['PostCommentID'];
                                    $commentOwnerID = $rows4['CommentOwnerID'];
                                    $commenterUsername = $rows4['Username'];
                                    $commenterProfileUrl = "/$commenterUsername";
                                    $commentDate = $rows4['CommentDate'];

                                    //if (checkBlock($ID, $commentOwnerID)) { $display3 = "style= 'display:none;'"; } else { $display3 = "style='display:block;'"; }
                                    ?>

                                    <div class="comment-row" >
                                        <div class="profileImageWrapper-Feed">
                                            <a href='<?php echo $commenterProfileUrl ?>'>
                                                <img src = "<?php echo $mediaPath . $profilePhoto ?>" class="profilePhoto-Feed" />
                                            </a>
                                        </div>
                                    </div>

                                    <div class="commentNameWrapper-Feed">
                                        <a href='<?php echo $commenterProfileUrl ?>'>
                                            <div class="profileName-Feed">
                                                <?php echo $rows4['FirstName'] . ' ' . $rows4['LastName'] ?>
                                            </div>
                                        </a>
                                        <div class="date"><?php echo date('l F j, Y',strtotime($commentDate)) ?></div>

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
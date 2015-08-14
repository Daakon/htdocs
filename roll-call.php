<?php
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

$getOnlyServiceSeekerPost = "";
if ($isServiceProvider == 0) {
    $getOnlyServiceSeekerPost = "AND (Members.ID = $ID)";
}

//$ads = getAds($genre, $age, $state, $interests, $gender);
$sqlRollCall = " SELECT DISTINCT
    Posts.Post As Post,
    Members.ID As MemberID,
    Members.FirstName As FirstName,
    Members.LastName As LastName,
    Members.IsServiceProvider As ServiceProvider,
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
    $memberCondition
    $getOnlyServiceSeekerPost
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
$isServiceProvider = $rows['IsServiceProvider'];
$name = $rows['FirstName'] . ' ' . $rows['LastName'];
$profilePhoto = $rows['ProfilePhoto'];
$category = $rows['Category'];
$post = $rows['Post'];
$postID = $rows['PostID'];
$postOwner = $memberID;
?>




<div class=" col-lg-9 col-md-9 roll-call "
     align="left">

    <img src="<?php echo $mediaPath. $profilePhoto ?>" class="profilePhoto-Feed enlarge-onhover " alt=""
         title="<?php echo $name ?>" /> &nbsp <b><font size="4"><?php echo $name ?></font></b>


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

    <h5 style="color:#60A3BD"><?php echo $category ?></h5>
    <br/><br/>
    <?php

    if ($isServiceProvider == 0) {
        echo "<h5>A service provider will contact you shortly</h5>";
        echo "<span style='color:red;'>Make sure you have provided your phone number in your profile to receive text
        messages when a service provider responds</span>";
    }

    //check if member has approved this post
    //----------------------------------------------------------------
    //require 'getSessionType.php';
    /*$sql2 = "SELECT ID FROM PostApprovals WHERE Post_ID = '$postID' AND Member_ID = '$ID'";
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
    echo '</div>';*/
    //-------------------------------------------------------------
    // End of approvals
    //-----------------------------------------------------------
    ?>


    <div style="padding-top:10px;padding-bottom:10px;margin-top:10px;">


        <br/>

        <?php
            $isServiceProvider = $_SESSION['IsServiceProvider'];
            if ($memberID != $ID && $isServiceProvider == 1) { ?>
            <a href="/view_messages.php?id=<?php echo $memberID ?>">Message <?php echo $rows['FirstName'] ?> </a>
        <?php } ?>
        <br/>



    </div>
    <?php
    }
    }
    ?>
</div>
<!--Right Column -->

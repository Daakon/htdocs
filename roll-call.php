<?php

// ad demographics
$age = getAge($ID);
$state =  getMemberState($ID);
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



if (empty($ageStart)) {
    $ageStart = 18;
}
if (empty($ageEnd)) {
    $ageEnd = 50;
}

if (!empty($searchState)) {
    $stateCondition = "AND (Profile.State = '$searchState')";
}
else {
    $stateCondition = "";
}
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
    AND (Members.Gender = '$getGender')
    AND TIMESTAMPDIFF(YEAR, Members.DOB, CURDATE()) >= $ageStart
    AND TIMESTAMPDIFF(YEAR, Members.DOB, CURDATE()) <= $ageEnd
    $stateCondition
    $genreCondition
    $memberCondition
    UNION
    SELECT DISTINCT
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
    Members.ID = $ID
    And (Posts.Member_ID = $ID)
    And (Profile.Member_ID = $ID)
    And (Posts.IsDeleted = 0)
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
        $name = $rows['FirstName'];
        $userName = $rows['Username'];
        $profilePhoto = $rows['ProfilePhoto'];
        $category = $rows['Category'];
        $post = $rows['Post'];
        $postID = $rows['PostID'];
        $postOwner = $memberID;
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

    <a href='/post-interest.php?interest=<?php echo urlencode($category) ?>&gender=<?php echo urlencode($getGender) ?>&ageStart=<?php echo urlencode($ageStart) ?>&ageEnd=<?php echo urlencode($ageEnd) ?>&state=<?php echo urlencode($state) ?>' class='category'><h5><?php echo $category ." ". interestGlyphs($category) ?></h5></a>
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

        <br/>

        <?php if ($memberID != $ID) { ?>
            <a href="/view_messages.php?id=<?php echo $memberID ?>">Direct Message <?php echo $rows['FirstName'] ?></a>
        <?php } ?>
        <br/>

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


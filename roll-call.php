<?php
// pre-load Roll Call
// get genre selection


if (!empty($genre) && $genre != "Show-All") {
    $genreCondition = "And Posts.Category = '$genre' ";
}
else if($genre = "Show-All") {
    $genre = '';
    $genreCondition = "And Posts.Category > '' ";
}
else { $genreCondition = "And Posts.Category = '$genre' "; }

if (!empty($searchState)) {
    $stateCondition = "AND (Profile.State = '$searchState')";
}
else {
    $stateCondition = "";
}

$getOnlyServiceSeekerPost = null;
if (get_is_service_provider($ID) == 0) {
    $getOnlyServiceSeekerPost = "AND (Members.ID = $ID)";
}

//$ads = getAds($genre, $age, $state, $interests, $gender);
$sqlRollCall = " SELECT DISTINCT
    Posts.Post As Post,
    Members.ID As MemberID,
    Members.FirstName As FirstName,
    Members.LastName As LastName,
    Members.IsServiceProvider As IsServiceProvider,
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
    $getOnlyServiceSeekerPost
    $stateCondition
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

    if (get_is_service_provider($ID) == 0) {
        echo "<h5>A service provider will contact you shortly</h5>";
    }

    if (get_is_service_provider($ID) == 1) {
        if (strlen(check_service_is_provided($ID)) == 0) {
            echo "<h5>To receive notifications when someone posts a request related to your service, you need to add your service in your profile</h5>";
        }
    }

    if (strlen(check_phone($ID)) == 0) {
        echo "<h5>You should consider adding a phone number to your profile for text notifications</h5>";
    }
    ?>


    <div style="padding-top:10px;padding-bottom:10px;margin-top:10px;">


        <br/>

        <?php
            if ($memberID != $ID && get_is_service_provider($ID) == 1) { ?>
            <a href="/view_messages.php?id=<?php echo $memberID ?>">Message <?php echo $rows['FirstName'] ?> </a>
        <?php } ?>
        <br/>


    </div>
</div>
    <?php
    }
    }
    ?>


<!--Right Column -->

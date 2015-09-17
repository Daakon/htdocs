<?php
/**
 * Created by PhpStorm.
 * User: chrismoney
 * Date: 9/17/15
 * Time: 12:32 PM
 */


$sql = " SELECT DISTINCT
    Reviews.Review As Review,
    Members.ID As MemberID,
    Members.FirstName As FirstName,
    Members.LastName As LastName,
    Members.IsServiceProvider As ServiceProvider,
    Reviews.ID As ReviewID,
    Profile.ProfilePhoto As ProfilePhoto
    FROM Members,Reviews,Profile
    WHERE
    Members.IsActive = 1
    And (Members.IsSuspended = 0)
    And (Members.ID = Reviews.Member_ID)
    And (Members.ID = Profile.Member_ID)
    AND (Reviews.Provider_ID = $providerID)
    And (Reviews.IsDeleted = 0)
    Group By ReviewID
    Order By ReviewID DESC LIMIT 1 ";

$result = mysql_query($sql) or die(mysql_error());
// if no results
if (mysql_num_rows($result) == 0) {
    ?>
    <div class=" col-lg-9 col-md-9 roll-call"
         style="background:white;border-radius:10px;margin-top:20px;border:2px solid black;" align="left">
        No Reviews
    </div>
<?php }
if (mysql_num_rows($result) > 0) {

while ($rows = mysql_fetch_assoc($result)) {
$memberID = $rows['MemberID'];
$name = $rows['FirstName'] . ' ' . $rows['LastName'];
$profilePhoto = $rows['ProfilePhoto'];
$review = $rows['Review'];
$reviewID = $rows['ReviewID'];
//$postOwner = $memberID;
?>



<div class=" col-lg-9 col-md-9 roll-call" align="left">

    <img src="<?php echo $mediaPath . $profilePhoto ?>" class="profilePhoto-Feed enlarge-onhover " alt=""
         title="<?php echo $name ?>"/> &nbsp <b><font size="4"><?php echo $name ?></font></b>


    <div class="post">
        <?php
        if (strlen($review) > 700) {
            $post500 = substr($review, 0, 700); ?>

            <div id="short<?php echo $reviewID ?>">
                <?php echo nl2br($post500) ?>...<a
                    href="javascript:showPost('long<?php echo $reviewID ?>', 'short<?php echo $reviewID ?>');">Show More</a>
            </div>
            <?php
            echo "<div id='long$reviewID' style='display:none;'>";
            echo nl2br($review);
            echo "</div>";
        } else {
            echo nl2br($review);
        }
        }
        }
        ?>

    </div>
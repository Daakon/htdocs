<?php
require 'imports.php';

get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];
?>


<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
preg_match("/[^\/]+$/",$url ,$match);
$username = $match[0];

$profileID = get_id_from_username($username);

?>


<div class="container">

    <div class="row row-padding" >

        <div class="col-lg-offset-3 col-lg-6 col-md-offset-3 col-md-6 col-sm-12 col-xs-12 roll-call-feed member-media" style="padding-left:10px;">
            <?php require 'profile_menu.php'; ?>

            <?php
            $sqlF = "SELECT COUNT( Follows.ID ) AS FollowerCount
FROM Follows, Members
WHERE Follows.Followed_ID = $ID
AND Follows.Followed_ID = Members.ID
AND Members.IsActive =1
AND Members.IsEmailValidated =1";
            $resultF = mysql_query($sqlF);
            $rowsF = mysql_fetch_assoc($resultF);
            $followedCount = $rowsF['FollowerCount'];
            ?>

            <h3><?php if ($username != get_username($ID)) {
                    echo trim(get_user_firstName($profileID)).'\'s Followers ('. $followedCount.')' ;
                }
                else {
                    echo "Your Followers ($followedCount)";
                }
                ?>
            </h3>


            - <a href="/member_following/<?php echo $username ?>">Following</a>
            <hr/>
            <?php
            $memberID = get_id_from_username($username);
            $sql = "SELECT * FROM Follows WHERE Follows.Followed_ID = $memberID Order By New Desc";
            $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting members who follow user"));

            while ($rows = mysql_fetch_assoc($result)) {

                $followerID = $rows['Follower_ID'];
                $new = $rows['New'];

                if (checkBlock($ID, $followerID) == false) {

                    $sql2 = "SELECT
                      Members.ID As MemberID,
                        Members.FirstName As FirstName,
                        Members.LastName As LastName,
                        Members.Username As Username,
                        Members.Interest As Interest,
                        Profile.ProfilePhoto As ProfilePhoto
                      FROM Members, Profile WHERE
                      Members.ID = $followerID
                      And Members.IsActive = 1
                      AND Profile.Member_ID = $followerID ";
                    $result2 = mysql_query($sql2) or die(logError(mysql_error(), $url, "Getting profile photos and names of members who follow user"));


                    while ($rows2 = mysql_fetch_assoc($result2)) {

                        $firstName = $rows2['FirstName'];
                        $lastName = $rows2['LastName'];
                        $name = $rows2['FirstName'] . ' ' . $rows2['LastName'];
                        $username = $rows2['Username'];
                        $profilePhoto = $rows2['ProfilePhoto'];
                        $interest = $rows2['Interest'];

                        $profileUrl = "/$username";
                        $memberID = get_id_from_username($username);


                        ?>

                        <div class='profileImageWrapper-Feed'>
                            <a href="<?php echo $profileUrl ?>">
                                <img src="<?php echo $mediaPath . $profilePhoto ?>" class="profilePhoto-Feed enlarge-onhover "
                                     alt=""
                                     title="<?php echo $name ?>"/>
                            </a>
                        </div>

                        <div class='profileNameWrapper-Feed'>
                            <a href="<?php echo $profileUrl ?>">
                                <div class="profileName-Feed">
                                    <?php echo $name ?>
                                    <?php
                                    if ($ID == $profileID && $new == 1) {
                                        echo "<span style='color:red;'>New</span>";
                                    }
                                    ?>
                                </div>
                            </a>

                        </div>


                        <hr class="hr-line" style="clear: both;"/>
                        <?php
                    }
                }
            }


            ?>

            <?php
            // update new follows notification
            if ($profileID == $ID) {
                $sql = "Update Follows Set New = 0 WHERE Followed_ID = $ID ";
                mysql_query($sql);
            }
            ?>

        </div>
    </div>
</div>
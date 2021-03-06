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


<div class="container containerFlush">

    <div class="row row-padding" >

        <div class="col-lg-offset-3 col-lg-6 col-md-offset-3 col-md-6 col-sm-12 col-xs-12 roll-call-feed member-media" style="padding-left:10px;">

            <?php require 'profile_menu.php'; ?>

            <?php
            $sqlF = "SELECT COUNT( Follows.ID ) AS FollowingCount
FROM Follows, Members
WHERE Follows.Follower_ID = $profileID
AND Follows.Followed_ID = Members.ID
AND Members.IsActive =1
AND Members.IsEmailValidated =1";
            $resultF = mysql_query($sqlF);
            $rowsF = mysql_fetch_assoc($resultF);
            $followingCount = $rowsF['FollowingCount'];
            ?>

            <h4><?php if ($username != get_username($ID)) {
                    echo 'Followed By '.trim(get_user_firstName($profileID)).' ('.$followingCount.')' ;
                }
                else {
                    echo "Followed by You ($followingCount)";
                }
                ?>
            </h4>



            - <a href="/member_follows/<?php echo $username ?>">Followers</a>
            <hr/>
            <?php
            $memberID = get_id_from_username($username);
            $sql = "SELECT * FROM Follows WHERE Follows.Follower_ID = $memberID Order By New Desc";
            $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting member follows"));

            while ($rows = mysql_fetch_assoc($result)) {

                $followedID = $rows['Followed_ID'];
                if (checkBlock($ID, $followedID) == false) {


                    $sql2 = "SELECT
                      Members.ID As MemberID,
                        Members.FirstName As FirstName,
                        Members.LastName As LastName,
                        Members.Username As Username,
                        Members.Interest As Interest,
                        Profile.ProfilePhoto As ProfilePhoto
                      FROM Members, Profile WHERE
                      Members.ID = $followedID
                      And Members.IsActive = 1
                      AND Profile.Member_ID = $followedID
                      Group By Interest ";
                    $result2 = mysql_query($sql2) or die(logError(mysql_error(), $url, "Getting profile photos and names of who user is following"));


                    while ($rows2 = mysql_fetch_assoc($result2)) {

                        $firstName = $rows2['FirstName'];
                        $lastName = $rows2['LastName'];
                        $name = $rows2['FirstName'] . ' ' . $rows2['LastName'];
                        $username = $rows2['Username'];
                        $profilePhoto = $rows2['ProfilePhoto'];
                        $followingCount = $rows2['FollowingCount'];

                        $profileUrl = "/$username";
                        ?>


                        <div class='profileImageWrapper-Feed'>
                            <a href="<?php echo $profileUrl ?>">
                                <img src="<?php echo $mediaPath . $profilePhoto ?>"
                                     class="profilePhoto-Feed enlarge-onhover " alt=""
                                     title="<?php echo $name ?>"/>
                            </a>
                        </div>

                        <div class='profileNameWrapper-Feed'>
                            <a href="<?php echo $profileUrl ?>">
                                <div class="profileName-Feed">
                                    <?php echo $name ?>
                                </div>
                            </a>

                        </div>


                        <hr class="hr-line" style="clear: both;"/>

                        <?php
                    }
                }
            }
            ?>

        </div>
    </div>
</div>
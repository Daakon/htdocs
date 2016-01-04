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

    <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call member-media" >
        <?php require 'profile_menu.php'; ?>

        <h3><?php if ($username != get_username($ID)) {
                echo trim(get_user_firstName($profileID)).'\'s Followers';
            }
            else {
               echo "Your Followers";
            }
            ?>
        </h3>
        - <a href="/member_following/<?php echo $username ?>">Following</a>
        <hr/>
        <?php
        $memberID = get_id_from_username($username);
        $sql = "SELECT * FROM Follows WHERE Follows.Followed_ID = $memberID";
        $result = mysql_query($sql);

        while ($rows = mysql_fetch_assoc($result)) {

            $followerID = $rows['Follower_ID'];

            $sql2 = "SELECT
                      Members.ID As MemberID,
                        Members.FirstName As FirstName,
                        Members.LastName As LastName,
                        Members.Username As Username,
                        Members.Interest As Interest,
                        Profile.ProfilePhoto As ProfilePhoto
                      FROM Members, Profile WHERE
                      Members.ID = $followerID
                      AND Profile.Member_ID = $followerID
                      Order By Interest ASC ";
            $result2 = mysql_query($sql2);


            while ($rows2 = mysql_fetch_assoc($result2)) {

                $firstName = $rows2['FirstName'];
                $lastName = $rows2['LastName'];
                $name = $rows2['FirstName'] . ' ' . $rows2['LastName'];
                $username = $rows2['Username'];
                $profilePhoto = $rows2['ProfilePhoto'];
                $interest = $rows2['Interest'];

                $profileUrl = "/$username";
?>

                <div class='profileImageWrapper-Feed'>
                    <a href="<?php echo $profileUrl ?>">
                        <img src="<?php echo $mediaPath. $profilePhoto ?>" class="profilePhoto-Feed enlarge-onhover " alt=""
                             title="<?php echo $name ?>" />
                    </a>
                </div>

                <div class='profileNameWrapper-Feed'>
                    <a href="<?php echo $profileUrl ?>">
                        <div class="profileName-Feed">
                            <?php echo $name ?>
                            <span style="font-style: italic;font-weight: normal">
                                    (<?php echo $interest ?>)
                                </span>
                        </div>
                    </a>

                </div>


                <hr class="hr-line" style="clear: both;"/>
        <?php
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

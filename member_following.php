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

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 ">
            <ul class="list-inline">

                <?php require 'profile_menu.php'; ?>
            </ul>
        </div>

        <?php if ($username == get_username($ID)) { ?>
            <style>
                .list-inline {
                    margin-top:-30px;
                }
            </style>
            <?php
        }
        else { ?>
            <style>
                .list-inline {
                    margin-top:-100px;
                    padding-top: 30px;
                }
            </style>

            <?php
        }
        ?>

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call member-media" >
            <h3><?php if ($username != get_username($ID)) {
                    echo 'Followed By '.trim(get_user_firstName($profileID));
                }
                else {
                    echo "Followed by You";
                }
                ?>
            </h3>
            - <a href="/member_follows/<?php echo $username ?>">Followers</a>
            <hr/>
            <?php
            $memberID = get_id_from_username($username);
            $sql = "SELECT * FROM Follows WHERE Follows.Follower_ID = $memberID";
            $result = mysql_query($sql);

            while ($rows = mysql_fetch_assoc($result)) {

                $followedID = $rows['Followed_ID'];

                $sql2 = "SELECT
                      Members.ID As MemberID,
                        Members.FirstName As FirstName,
                        Members.LastName As LastName,
                        Members.Username As Username,
                        Members.Interest As Interest,
                        Profile.ProfilePhoto As ProfilePhoto
                      FROM Members, Profile WHERE
                      Members.ID = $followedID
                      AND Profile.Member_ID = $followedID
                      Order By Interest ASC ";
                $result2 = mysql_query($sql2);

                        echo "<table><tr></tr>";

                while ($rows2 = mysql_fetch_assoc($result2)) {

                    $firstName = $rows2['FirstName'];
                    $lastName = $rows2['LastName'];
                    $name = $rows2['FirstName'] . ' ' . $rows2['LastName'];
                    $username = $rows2['Username'];
                    $profilePhoto = $rows2['ProfilePhoto'];
                    $interest = $rows2['Interest'];

                    $profileUrl = "/$username";
                    if (strlen($name) > 70) {
                        $name = checkNameLength($name,$firstName,$lastName);
                    }
                    ?>

                    <td>
                        <a href="<?php echo $profileUrl ?>">
                            <img src="<?php echo $mediaPath. $profilePhoto ?>" class="profilePhoto-Feed enlarge-onhover " alt=""
                             title="<?php echo $name ?>" /> &nbsp <span class="profileName-Feed"><?php echo $name ?></span>
                        </a>
                        <span style="font-style: italic">(<?php echo $interest ?>)</span>
                    </td>
                                <?php
                }
                echo "</tr></table>";
            }

            ?>

        </div>
    </div>
</div>

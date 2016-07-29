<?php
require 'imports.php';


get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];
?>

<?php

if (isset($_POST['redeem']) && $_POST['redeem'] == 'Redeem') {

    $memberID = $_POST['memberID'];
    $username = $_POST['username'];
    $referralID = $_POST['referralID'];

    $sql = "Update PostApprovals
    Join Members
    ON PostApprovals.Member_ID = Members.ID
    Set IsRedeemed = 1
    Where IsRedeemed = 0 And Owner_ID = $memberID ";

    mysql_query($sql);

    //update comment count
    $sql2 = "Update PostComments
    Join Members
    ON PostComments.Member_ID = Members.ID
    Set IsRedeemed = 1 Where Owner_ID = $memberID ";

    mysql_query($sql2);

    //Update Follows
    $sql3 = "UPDATE Follows SET IsRedeemed =1 WHERE Followed_ID = $memberID and IsRedeemed = 0 ";
    mysql_query($sql3);

    //Update Referrals
       $sql4 = "UPDATE Referrals
       JOIN Members
       ON Referrals.Signup_ID = Members.ID
       SET    Referrals.IsRedeemed = 1
       WHERE Referrals.Referral_ID = '$referralID' and Members.IsEmailValidated = 1 ";
       mysql_query($sql4);

    echo "<script>alert('Redemption process successful');location = '/backoffice?username=$username' </script>";
}
?>


<body>

<?php

?>

<div class="container" style = "margin-top:-50px">




    <div class="col-xs-12 col-md-8 col-lg-8 col-md-offset-2 roll-call">
        <?php require 'profile_menu.php'; ?>
        <h4>Back Office</h4>
        <?php
        $sql = "SELECT Count(*) As Total FROM Members ";
        $result = mysql_query($sql) or die(mysql_error());
        $rows = mysql_fetch_assoc($result);
        ?>
        <span style="color:#8899a6;font-weight:bold">Current Sign Ups</span> : <?php echo $rows['Total']; ?>

        <br/><br/>

        <?php
        $username = $_GET['username'];
        if (isset($username) && !empty($username)) {
            $memberID = get_id_from_username($username);
            $referralID = get_referralID($memberID);

            // get referral money
            $sql3 = "SELECT COUNT( Referrals.ID ) AS ReferralCount
                FROM Referrals
                JOIN Members ON Referrals.Signup_ID = Members.ID
                WHERE Referrals.Referral_ID =  '$referralID'
                AND Referrals.IsRedeemed =0
                AND Referrals.Signup_ID IN

                ( SELECT Posts.Member_ID
                FROM Posts
                WHERE Posts.IsDeleted =0
                GROUP BY Posts.Member_ID
                HAVING COUNT( Posts.Member_ID ) >=5
                ORDER BY Posts.ID DESC )

                AND Members.IsEmailValidated =1";

            $result3 = mysql_query($sql3) or die(mysql_error());
            $rows3 = mysql_fetch_assoc($result3);
            $referralCount = $rows3['ReferralCount'];
            $referralMoney = $referralCount * 3;

            echo "Referral Count: $referralCount <br/>";
            echo "Referral Money: ". money_format('$%i', $referralMoney);

            echo "<hr class='hr-line' />";

            // Likes
            $sql = "Select count(PostApprovals.ID) as LikeCount
            From PostApprovals, Members
            Where (PostApprovals.Owner_ID = $memberID) and (PostApprovals.IsRedeemed = 0)
            And (PostApprovals.Member_ID = Members.ID)
            And (PostApprovals.Member_ID != $memberID)
            And (Members.IsEmailValidated = 1) ";
            $result = mysql_query($sql) or die(mysql_error());
            $rows = mysql_fetch_assoc($result);
            $likeCount = $rows['LikeCount'];
            $likeMoney = $likeCount * 0.01;

            echo "Like Count: $likeCount <br/>";
            echo "Like Money: ". money_format('$%i', $likeMoney);

            echo "<hr class='hr-line' />";

            // Comments
            $sql2 = "Select Count(PostComments.ID) As CommentCount
            FROM PostComments, Members
            WHERE (PostComments.Owner_ID = $memberID And PostComments.IsRedeemed = 0)
            And (PostComments.Member_ID = Members.ID)
            And (PostComments.Member_ID != $memberID)
            And (Members.IsEmailValidated = 1) ";
            $result = mysql_query($sql) or die(mysql_error());
            $rows = mysql_fetch_assoc($result);
            $commentCount = $rows['CommentCount'];
            $commentMoney = $commentCount * 0.01;

            echo "Comment Count: $commentCount <br/>";
            echo "Comment Money: ". money_format('$%i', $commentMoney);

            echo "<hr class='hr-line' />";

            // Followers
            $sql1 = "SELECT COUNT(Follows.ID) AS FollowerCount
            FROM Follows, Members
            WHERE (Follows.Followed_ID = $memberID)
            AND (IsRedeemed =0)
            And (Follows.Follower_ID = Members.ID)
            And (Members.IsEmailValidated = 1) ";
            $result1 = mysql_query($sql1) or die(mysql_error());
            $rows1 = mysql_fetch_assoc($result1);
            $followerCount = $rows1['FollowerCount'];
            $followerMoney = $followerCount * 0.01;

            echo "Follower Count: $followerCount <br/>";
            echo "Follower Money: ". money_format('$%i', $followerMoney);

            echo "<hr class='hr-line' />";

            $addedMoney = $referralMoney + $likeMoney + $commentMoney + $followerMoney;
            $totalMoney =  money_format('$%i', $addedMoney);

            echo "Total Money: $totalMoney ";
            ?>

            <br/><br/>

            <form style="float:left" action="" method="post" onsubmit="return confirm('Are you sure you want to redeem this member')">
                <input type="hidden" name="memberID" id="memberID" value="<?php echo $memberID ?>" />
                <input type="hidden" name="username" id="username" value="<?php echo $username ?>" />
                <input type="hidden" name="referralID" id="referralID" value="<?php echo $referralID ?>" />
                <input type="submit" name="redeem" id="redeem" value="Redeem" class="btn btn-primary" />
            </form>

            <a style="float:left;padding-left:10px;" href="/view_messages/<?php echo $username ?>"><img src="/images/back.png" style="height: 20px;width:20px;" />
            </a>
        <?php

        }


        ?>

    </div>
</div>

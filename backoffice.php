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


    </div>
</div>


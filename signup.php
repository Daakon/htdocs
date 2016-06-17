<?php

require 'imports.php';

$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$email = $_POST['email'];
$referredBy = $_POST['referredBy'];
$emailSplit = explode("@", $email);
$username = $emailSplit[0];

if (check_referral_ID($referredBy) == false) {
        echo "<script>alert('Referral ID not found. Please check back with the person who referred you.'); location ='/learn_more'</script>";
        exit;
    }

// captilize first letter only
$fName = ucfirst(strtolower($username));
$lName = ' ';


// check if email exists
$sql = "SELECT * FROM Members WHERE Email = '$email'";
$result = mysql_query($sql) or die(logError(mysql_error(), $url, "Checking if email exists"));

if (mysql_num_rows($result) > 0) {

        echo '<script>alert("You already have an profile, please login");location = "index.php"</script>';
        exit;
}

$unique = 1;
a:
// check if username exists
$sql = "SELECT * FROM Members WHERE Username = '$username'";
$result = mysql_query($sql) or die(logError(mysql_error(), $url, "Checking if username exists"));

if (mysql_num_rows($result) > 0) {
    $username = $username . $unique;
    $unique++;
    goto a;
}



$sql = "INSERT INTO Members (FirstName, LastName, Email,    Username,   ReferralID,    Password,        SignupDate,   IsSuspended, EmailActive, LastLogin     )
Values 			            ('$fName', '$lName', '$email', '$username', '$username', '".md5($pass)."',  CURRENT_DATE(),   0,           1,       CURRENT_DATE() )";
$result = mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting new member account"));

$ID = mysql_insert_id();

$sql = "SELECT * FROM Members WHERE ID = $ID ";
$result = mysql_query($sql) or die(logError(mysql_error(), $url, "Get profile from brand new member ID"));
$rows = mysql_fetch_assoc($result);

/*Set two session variable which will be needed throughout the program */
session_start();
$_SESSION['ID'] = $rows['ID'];
setcookie("ID", $rows['ID'], time() + (10 * 365 * 24 * 60 * 60)); // set cookie for 10 years

//sign up date

$date = date('Y-m-d H:i:s');
$sql = "UPDATE Members SET SignupDate = '$date' WHERE ID = '$ID' ";
$result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting sign up date"));

// insert default profile pic into profile table
$sql = "INSERT INTO Profile (Member_ID,  Poster,               ProfileVideo ) Values
                            ($ID,       'default_photo.png', 'default_video.png' )    ";
$result = mysql_query($sql) or die(logError(mysql_error(), $url, "Inserting default photo"));


// insert Referral ID if exists
if (strlen($referredBy) > 0) {

        $sql = "INSERT INTO Referrals (Signup_ID, Referral_ID, ReferralDate) Values ($ID, '$referredBy', NOW()) ";
        $result = mysql_query($sql) or die(mysql_error());
    }



// Send out sign up email
$toId = $rows['ID'];
build_and_send_email(0,$ID, 3, null,$pass);

$firstName = get_user_firstName($ID);
// DM new member with instructions
$message = "<p>Hey $firstName, You can now make money from your social media efforts.
        <br/>
        1. Get paid every time someone likes your post.
        <br/>
        2. Get paid every time someone comments on your post.
        <br/>
        3. Get paid every time you get a new follower.
        <br/>
        4. Get paid every time you refer your friends to join Playdoe and your referral makes at least 5 unique posts.
        <br/>
        You have a referral ID assigned to you. Go to your profile and you will see it. Give that to your friends so you can get get paid for referring them. You have also earned $1 for signing yourself up.
        <br/>
        Last but not least, if you are really popular, get 20 referrals and earn $20.
        <br/>
        You will see a running total of your money on your home screen.
        <br/>
        You must reach a $10 threshold before redeeming you money. At which point you would direct message the <a href='/view_messages/redeem'>Playdoe Redemption Team</a>, who handles all member redemptions.
         <br/>
        If you do not have an account, you can elect to receive a gift card for the value of your cash balance. Please request the gift card of your preference at the time of your redemption request. If an electronic gift card is available we will provide it. Some restrictions apply depending on the particular company, such as Walmart only offers gift cards in increments of $5, so your cash balance will be rounded up or down to the nearest dollar.
        <br/>
        You cannot partially redeem your balance, our system is currently all of nothing.
        <br/>
        So as you can see, there are many ways to make money from your social media efforts here on Playdoe.
        <br/>
        Make sure you keep this message and reply anytime you have a question about how things work.
        <br/>
        You can do one better and follow us so you have easy access to our profile and you can see all of the posts we make about how to do certain things and how features work.</p>";

$message = mysql_escape_string($message);
$subject = "Welcome to Playdoe";
$receiverID = $ID;
$rInitialMessage = 1;
$rFirstMessage = 1;
$supportID = 448;
// create thread for receiver
$sql = "INSERT INTO Messages  (ThreadOwner_ID, Sender_ID,   Receiver_ID,  Subject,    Message,         InitialMessage,      New,        FirstMessage,       MessageDate   ) VALUES
                              ($receiverID,    $supportID,  $receiverID, '$subject', '$message',     '$rInitialMessage',    '1',        $rFirstMessage,     CURRENT_TIMESTAMP ) ";
mysql_query($sql) or die(mysql_error());
?>

<!--track sign ups through Google-->
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt=""
             src="//www.googleadservices.com/pagead/conversion/951358222/?label=jDs3CNyP4GAQjqbSxQM&guid=ON&script=0"/>
    </div>
</noscript>

    <?php
        echo '<script>alert("Your profile was successfully set up");location = "home.php"</script>';

    ?>

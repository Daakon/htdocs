<?php

require 'connect.php';
require 'model_functions.php';
require 'email.php';



$fName = $_POST['firstName'];
$lName = $_POST['lastName'];
$email = $_POST['email'];
$gender = $_POST['gender'];
$month = $_POST['ddMonth'];
$day = $_POST['ddDay'];
$year = $_POST['ddYear'];
$birthday = $_POST['birthday'];
$city = $_POST['ddCity'];
$state = $_POST['ddState'];
$zip = $_POST['zip'];
$username = $_POST['username'];
$pass = $_POST['password'];
$goal = $_POST['ddGoal'];
$phone = $_POST['phone'];
$interest = $_POST['interest'];
$fb_token = $_POST['fb_token'];
$fb_id = $_POST['fb_id'];


if ($city == '') {
    echo "<script>alert('You did not provide a city'); location='/index.php'</script>";
    exit;
}

if($gender=='') $gender = (($_POST['gender']=='Male')?1:2);

if ($year != '') {
    $dob = $year . '-' . $month . '-' . $day;
}
else {
    $dob = $birthday;
}

// check if email exists
$sql = "SELECT * FROM Members WHERE Email = '$email'";
$result = mysql_query($sql) or die(mysql_error());

if (mysql_num_rows($result) > 0) {

        echo '<script>alert("You already have an profile, please login");location = "index.php"</script>';
        exit;
}

// check if username exists
$sql = "SELECT * FROM Members WHERE Username = '$username'";
$result = mysql_query($sql) or die(mysql_error());

if (mysql_num_rows($result) > 0) {

    echo '<script>alert("That username is not available");location = "index.php"</script>';
    exit;
}


$sql = "INSERT INTO Members (FirstName, LastName, Email,    Gender,    DOB,    Username,      Password,         Interest,     SignupDate,   IsSuspended, EmailActive, LastLogin,      fb_token,   fb_id)
Values 			            ('$fName', '$lName', '$email', '$gender', '$dob', '$username',  '".md5($pass)."',  '$interest',     CURRENT_DATE(),   0,           1,        CURRENT_DATE(),'$fb_token','$fb_id')";
$result = mysql_query($sql) or die(mysql_error());

$ID = mysql_insert_id();

$sql = "SELECT * FROM Members WHERE ID = $ID ";
$result = mysql_query($sql) or die(mysql_error());
$rows = mysql_fetch_assoc($result);

/*Set two session variable which will be needed throughout the program */
session_start();
$_SESSION['ID'] = $rows['ID'];
setcookie("ID", $rows['ID'], time() + (10 * 365 * 24 * 60 * 60)); // set cookie for 10 years

//sign up date

$date = date('Y-m-d H:i:s');
$sql = "UPDATE Members SET SignupDate = '$date' WHERE ID = '$ID' ";
$result = mysql_query($sql) or die(mysql_error());

// insert default profile pic into profile table
$sql = "INSERT INTO Profile (Member_ID, Poster,               ProfileVideo,        State,    City,  Zip,    Phone) Values
                            ('$ID',     'default_photo.png', 'default_video.png', '$state',   '$city', '$zip', '$phone')    ";
$result = mysql_query($sql) or die(mysql_error());


// Send out sign up email
$toId = $rows['ID'];
build_and_send_email(0,$ID, 3, null);
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

<?php

require 'connect.php';
require 'model_functions.php';
require 'email.php';



$fName = $_POST['firstName'];
$lName = $_POST['lastName'];
$email = $_POST['email'];
$gender = $_POST['ddGender'];
$month = $_POST['ddMonth'];
$day = $_POST['ddDay'];
$year = $_POST['ddYear'];
$username = $_POST['username'];
$pass = $_POST['password'];
$fb_token = $_POST['fb_token'];
$fb_id = $_POST['fb_id'];

if($gender=='') $gender = (($_POST['gender']=='male')?1:2);

$dob = $year . '-' . $month . '-' . $day;


$sql = "SELECT * FROM Members WHERE Email = '$email'";
$result = mysql_query($sql) or die(mysql_error());

if (mysql_num_rows($result) > 0) {

    if($fb_id!='') {

        $rows = mysql_fetch_assoc($result);
        session_start();

        //die(print_r($rows));

        $_SESSION['ID'] = $rows['ID'];

        setcookie("ID", $rows['ID'], time() + (10 * 365 * 24 * 60 * 60)); // set cookie for 10 years

        //update last login
        $id = $rows['ID'];
        $date = date('Y-m-d H:i:s');
        $sql2 = "UPDATE Members SET LastLogin = '$date' WHERE ID = '$id' ";
        mysql_query($sql2) or die(mysql_error());

        exit;

    }
    else{
        echo '<script>alert("You already have an profile, please login");location = "index.php"</script>';
        exit;
    }
}

// if facebook is used for a sign up then we will use fb_id for a username to keep things unique
if (strlen($username) == 0 || $username == '') {
    $username = $fb_id;
}

$sql = "INSERT INTO Members (FirstName, LastName, Email,    Gender,    DOB,    Username,      Password,     SignupDate,   IsSuspended, EmailActive, LastLogin,      fb_token,   fb_id)
    Values 			('$fName', '$lName', '$email', '$gender', '$dob', '$username', '".md5($pass)."', CURRENT_DATE(), 0,           1,        CURRENT_DATE(),'$fb_token','$fb_id')";
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
$sql = "INSERT INTO Profile (Member_ID, ProfilePhoto,     ProfileVideo,      HomeState, HomeCity, CurrentCity, CurrentState, Interests, Books, Movies, Food, Dislikes, Plan) Values
                                ('$ID', 'default_photo.png', 'default_video.png', '', '', '', '', '', '', '', '', '', '')    ";
$result = mysql_query($sql) or die(mysql_error());


// Send out sign up email
$toId = $rows['ID'];
build_and_send_email(1,$ID, 3, null);

echo '<script>alert("Your profile was successfully set up");location = "home.php"</script>';

?>

<?php

require 'connect.php';
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

$dob = $year . '-' . $month . '-' . $day;

$sql = "SELECT Email FROM Members WHERE Email = '$email'";

$result = mysql_query($sql) or die(mysql_error());
$rows = mysql_fetch_assoc($result);

if (count($rows) > 0) {
    echo '<script>alert("You already have an profile, please login");location = "index.php"</script>';
    exit();
} else {
    $sql = "INSERT INTO Members (FirstName,    LastName,   Email,   Gender,   DOB,   Username,   Password, SignupDate)
    Values                        ('$fName',  '$lName','   $email','$gender','$dob','$username', '$pass',  CURRENT_DATE())";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);

    $sql = "SELECT * FROM Members WHERE Email = '$email'";
    $result = mysql_query($sql) or die(mysql_error());
    $rows = mysql_fetch_assoc($result);

    /*Set two session variable which will be needed throughout the program */
    session_start();
    $_SESSION['ID'] = $rows['ID'];
    setcookie("ID", $rows['ID'], time() + (10 * 365 * 24 * 60 * 60)); // set cookie for 10 years

    //sign up date
    $id = $rows['ID'];
    $date = date('Y-m-d H:i:s');
    $sql2 = "UPDATE Members SET SignupDate = '$date' WHERE ID = '$id' ";
    $result = mysql_fetch_assoc($sql2) or die(mysql_error());



    // insert default profile pic
    $sql = "INSERT INTO Media (Member_ID, MediaName,          MediaType, MediaDate,     IsProfilePhoto) Values
                              ('$id',     'default_photo.png',  'png',   CURRENT_DATE (),     1)    ";
    $result = mysql_query($sql) or die(mysql_error());

    // Send out sign up email
    /*$removeLink = "If you did not sign up for this profile, contact us at <a href = 'mailto:info@businessconnect.co'>info@businessconnect.co</a> ";
    $fName = $rows['FirstName'];
    $pass = $rows['Password'];
    $toEmail = $rows['Email'];
    $fromEmail = '<noreply@rapportbook.com>';
    $failMessage = '<script>alert("Your sign up email could not be sent. Please contact support at info@businessconnect.co ");</script>'; // no return link
    $photo = "http://rapportbook.com/media/rapportbook.png";
    $subject = 'Rapportbook Sign Up';
    
    
    $toId = $rows['ID'];
    build_and_send_email();*/

    echo '<script>alert("Your profile was successfully set up");location = "home.php"</script>';
}
?>

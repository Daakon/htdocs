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
    $ID = $rows['ID'];
    $date = date('Y-m-d H:i:s');
    $sql2 = "UPDATE Members SET SignupDate = '$date' WHERE ID = '$id' ";
    $result = mysql_fetch_assoc($sql2) or die(mysql_error());



    // insert default profile pic into profile table
    $sql = "INSERT INTO Profile (Member_ID) Values
                                ('$ID')    ";
    $result = mysql_query($sql) or die(mysql_error());


    // Send out sign up email
    $toId = $rows['ID'];
    build_and_send_email(1,$toId, 3, '');

    echo '<script>alert("Your profile was successfully set up");location = "home.php"</script>';
}
?>

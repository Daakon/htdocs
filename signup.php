<?php

require 'imports.php';

$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$email = $_POST['email'];
$emailSplit = explode("@", $email);
$username = $emailSplit[0];


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



$sql = "INSERT INTO Members (FirstName, LastName, Email,    Username,      Password,        SignupDate,   IsSuspended, EmailActive, LastLogin     )
Values 			            ('$fName', '$lName', '$email', '$username',  '".md5($pass)."',  CURRENT_DATE(),   0,           1,       CURRENT_DATE() )";
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


// Send out sign up email
$toId = $rows['ID'];
build_and_send_email(0,$ID, 3, null,$pass);
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

<?php

require 'connect.php';
require 'model_functions.php';
require 'email.php';

$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$email = $_POST['email'];

$emailSplit = explode("@", $email);
$username = $emailSplit[0];

// captilize first letter only
$fName = ucfirst(strtolower($username));
$lName = ' ';

//$gender = $_POST['gender'];
//$month = $_POST['ddMonth'];
//$day = $_POST['ddDay'];
//$year = $_POST['ddYear'];
//$birthday = $_POST['birthday'];

$ip = $_SERVER['REMOTE_ADDR'];
$key = 'dc5ff2626e3bfffd325504af3e81c54d26e1c6c0bf5312c2ce5ef30043d314f6';
$apiUrl = "http://api.ipinfodb.com/v3/ip-city/?key=$key&ip=$ip&format=json";

$d = file_get_contents($apiUrl);
$data = json_decode($d , true);

/*
 * JSON Returned
{
"statusCode" : "OK",
"statusMessage" : "",
"ipAddress" : "74.125.45.100",
"countryCode" : "US",
"countryName" : "UNITED STATES",
"regionName" : "CALIFORNIA",
"cityName" : "MOUNTAIN VIEW",
"zipCode" : "94043",
"latitude" : "37.3956",
"longitude" : "-122.076",
"timeZone" : "-08:00"
}
*/

if(strlen($data['countryCode'])) {
    $info = array(
        'ip' => $data['ipAddress'],
        'country_code' => $data['countryCode'],
        'country_name' => $data['countryName'],
        'region_name' => $data['regionName'],
        'city' => $data['cityName'],
        'zip_code' => $data['zipCode'],
        'latitude' => $data['latitude'],
        'longitude' => $data['longitude'],
        'time_zone' => $data['timeZone'],
    );
}

if (strstr($url, "local")) {
    $city = 'Saint Louis';
    $state = 'MO';
    $zip = '63101';
}
else {
    $city = $info['city'];
    $state = $info['region_name'];
    $zip = $info['zip_code'];

}

//$zip = $_POST['zip'];
$username = $username;
$pass = 'password10';
//$phone = $_POST['phone'];
$interest = $_POST['interest'];


if ($city == '') {
    echo "<script>alert('You did not provide a city'); location='../'</script>";
    exit;
}

//if($gender=='') $gender = (($_POST['gender']=='Male')?1:2);

/*if ($year != '') {
    $dob = $year . '-' . $month . '-' . $day;
}
else {
    $dob = $birthday;
}*/


// check if email exists
$sql = "SELECT * FROM Members WHERE Email = '$email'";
$result = mysql_query($sql) or die(mysql_error());

if (mysql_num_rows($result) > 0) {

        echo '<script>alert("You already have an profile, please login");location = "index.php"</script>';
        exit;
}

$unique = 1;
a:
// check if username exists
$sql = "SELECT * FROM Members WHERE Username = '$username'";
$result = mysql_query($sql) or die(mysql_error());

if (mysql_num_rows($result) > 0) {
    $username = $username . $unique;
    $unique++;
    goto a;
}


// check if we have this city
$sql = "SELECT City FROM City WHERE City = '$city'";
$result = mysql_query($sql) or die(mysql_error());
if (mysql_num_rows($result) == 0) {
    // get state key
    $sql2 = "SELECT ID FROM State WHERE State = '$state'";
    $result2 = mysql_query($sql2) or die(mysql_error());
    $stateRow = mysql_fetch_assoc($result2);
    $stateID = $stateRow['ID'];

    // insert new city with state key
    $sql3 = "INSERT INTO City (State_ID, City) Values ($stateID, '$city')";
    mysql_query($sql3) or die(mysql_error());
}


$sql = "INSERT INTO Members (FirstName, LastName, Email,    Username,      Password,         Interest,     SignupDate,   IsSuspended, EmailActive, LastLogin     )
Values 			            ('$fName', '$lName', '$email', '$username',  '".md5($pass)."',  '$interest',   CURRENT_DATE(),   0,           1,       CURRENT_DATE() )";
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
$sql = "INSERT INTO Profile (Member_ID, Poster,               ProfileVideo,        State,    City,      Zip,    Phone) Values
                            ('$ID',     'default_photo.png', 'default_video.png', '$state',   '$city', '$zip', '$phone')    ";
$result = mysql_query($sql) or die(mysql_error());


// dynamic auto post
$dynamicText = dynamicPost($interest);

// insert default post
$post = "Hey!, my name is $fName and my interest is $interest. Comment on my post or direct message if you want to connect.";
$post = mysql_real_escape_string($post);
$sql = "INSERT INTO Posts (Post,    Category,  Member_ID,   PostDate) Values
                          ('$post', '$interest', '$ID',       CURDATE())";
mysql_query($sql) or die(mysql_error());


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

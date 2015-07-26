<?php

$sql = "SELECT Username,DOB, Email FROM Members WHERE ID = $ID";
$result = mysql_query($sql) or die(mysql_error());
$rows = mysql_fetch_assoc($result);

$dob = $rows['DOB'];
$email = $rows['Email'];
$username = $rows['Username'];

$date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';

if (!preg_match($date_regex, $dob)) {
    echo "<script>alert('Please provide your date of birth so others can find you');location='/profile.php/$username'</script>";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Please provide an email for your profile');location='/profile.php/$username</script>";
}
?>
<?php

require 'connect.php';

$email = $_POST['login_email'];
$pass = $_POST['login_password'];


$sql = "SELECT * FROM Members WHERE Email = '$email' And Password = '".md5($pass)."' ";

$result = mysql_query($sql) or die(logError(mysql_error(), $url, "Check existing member email"));


if (mysql_num_rows($result) > 0) {
    $rows = mysql_fetch_assoc($result);

    if ($rows['IsSuspended'] == 1) {
        echo "<script>alert('Your account has been suspended. Contact support for more information.');location = '/index.php'</script>";
        exit;
    }

    if ($rows[''])

    session_start();

    $_SESSION['ID'] = $rows['ID'];

    setcookie("ID", $rows['ID'], time() + (10 * 365 * 24 * 60 * 60)); // set cookie for 10 years

    //update last login
    $id = $rows['ID'];
    $date = date('Y-m-d H:i:s');
    $sql2 = "UPDATE Members SET LastLogin = '$date' WHERE ID = '$id' ";
    mysql_query($sql2) or die(logError(mysql_error(), $url, "Updating last login"));

    if ($rows['IsActive'] == 0) {
        $sql = "UPDATE Members SET IsActive = 1 WHERE ID = $id";
        mysql_query($sql) or die(mysql_error());
        echo "<script>alert('You are now reactivating your account.'); location='/home.php'</script>";
    }

    header('location:home');

} else {
    echo '<script>alert("Your email or password was incorrect");location = "../"</script>';
}
?>
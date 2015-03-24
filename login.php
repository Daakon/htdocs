<?php

require 'connect.php';

$email = $_POST['login_email'];
$pass = $_POST['login_password'];

$sql = "SELECT * FROM Members WHERE Email = '$email' And Password = '$pass'";
$result = mysql_query($sql) or die(mysql_error());


if (mysql_num_rows($result) > 0) {
    $rows = mysql_fetch_assoc($result);
    session_start();

    $_SESSION['ID'] = $rows['ID'];

    setcookie("ID", $rows['ID'], time() + (10 * 365 * 24 * 60 * 60)); // set cookie for 10 years

    //update last login
    $id = $rows['ID'];
    $date = date('Y-m-d H:i:s');
    $sql2 = "UPDATE Members SET LastLogin = '$date' WHERE ID = '$id' ";
    mysql_query($sql2) or die(mysql_error());

    header('location:home.php');

} else {
    echo '<script>alert("Your email or password was incorrect");location = "index.php"</script>';
}
?>
<?php
require 'connect.php';
$generic = $_POST['generic'];
$pass = $_POST['login_password'];

$sql = "SELECT * FROM Members WHERE Email = '$generic' Or username='$generic' And password = '$pass'";
$result = $conn->prepare($sql) or die(mysql_error());
$result->execute();
$rows = $result->fetchAll();
if (count($rows) == 0) {
    echo '<script>alert("Your email or password was incorrect");location = "index.php"</script>';
}
else {
    session_start();
    
    $_SESSION['ID'] = $rows['ID'];

    setcookie("ID", $rows['ID'], time() + (10 * 365 * 24 * 60 * 60)); // set cookie for 10 years
    
     //update last login
        $id = $rows['ID'];
        $date = date('Y-m-d H:i:s');
        $sql2 = "UPDATE Members SET LastLogin = '$date' WHERE ID = '$id' ";
        $conn->prepare($sql2) or die(mysql_error());
        $conn->execute();
    header('location:home.php');
}
?>
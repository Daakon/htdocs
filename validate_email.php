<?php
require 'imports.php';

$ID = $_GET['x'];

$sql = "Update Members Set IsEmailValidated = 1 ";
mysql_query($sql);

echo "<script>alert('Your email was validated'); location = '/home' </script>";
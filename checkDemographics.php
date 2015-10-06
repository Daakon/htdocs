<?php

$sql = "SELECT About FROM Profile WHERE Member_ID = $ID";
$result = mysql_query($sql) or die(mysql_error());
$rows = mysql_fetch_assoc($result);

$about = $rows['About'];

?>
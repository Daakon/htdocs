<?php
require 'connect.php';
//require 'getSession.php';
$ID = $_SESSION['ID'];

$sql = "SELECT * FROM Messages WHERE (ThreadOwner_ID = '$ID') And (New = 1)";
$result = mysql_query($sql) or die(mysql_error());
if (mysql_num_rows($result) > 0 && isset($_SESSION['ID'])) {
echo '<span class="notification">'.mysql_num_rows($result).'</span></a>';
}

?>

<!--<script>
    setInterval(function()
    {
        $('#newMessageCount').load('getNewMessageCount.php');}, 1000);
</script>-->
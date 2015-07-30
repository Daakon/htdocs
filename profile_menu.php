<?php if ($_SESSION['ID'] == $ID) {

$ageStart = $_GET['ageStart'];
$ageEnd = $_GET['ageEnd'];
$gender = $_GET['gender'];
$state = $_GET['state'];
?>

<ul class="list-inline demoText profileMenu">
    <li><a href="/home.php?ageStart=<?php echo $ageStart ?>&ageEnd=<?php echo $ageEnd ?>&gender=<?php echo $gender ?>&state=<?php echo $state ?>">Roll Call</a></li>
    <li><a href="/profile.php/<?php echo get_username($ID) ?>">Profile</a></li>
    <li><a href="/member_media.php">Video Book</a></li>
    <li><a href="/messages.php">Messaging <?php require 'getNewMessageCount.php' ?></a></li>
    <li><a href="/manage_post.php">Manage Posts</a></li>

    <?php
    }
    else {
        require 'profile_menu.php';
    }
    ?>

    <br/><br/>

    <?php
$sql = "SELECT Admin FROM Members WHERE ID = $ID ";
$result = mysql_query($sql) or die(mysql_error());
$rows = mysql_fetch_assoc($result);
    if ($rows['Admin'] == 1) { ?>
        <li><a href="/marketing_manager.php">Marketing Manager</a></li>
    <?php }
?>

</ul>
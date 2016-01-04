<?php


$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// variables that get sent in post must have identical names every where they exist
$state = $_POST['state'];
$page = $_POST['page'];

$sql = "SELECT ID FROM State WHERE State = '$state'";
$result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting State ID"));
$rows = mysql_fetch_assoc($result);
$stateID = $rows['ID'];

$sql2 = "SELECT City FROM City WHERE State_ID = $stateID Order By City ASC ";
$result2 = mysql_query($sql2) or die(mysql_error("You must select a city"));

if (mysql_num_rows($result2) > 0) {
 if ($page =='home') {?>
<select name="ddCity" id="ddCity" onchange="updateFeed();">
    <?php } else { ?>
    <div class="form-group row" id="form-group-city">
                    <div class="col-md-6">
                        <label class="sr-only" for="city">City</label>
                        <select class='form-control input-lg' name="ddCity" id="ddCity">
                        <?php } ?>
                            <option value="">City</option>

    <?php

while ($rows2 = mysql_fetch_assoc($result2)) {
$city = $rows2['City'];
?>
<option value = "<?php echo $city ?>"><?php echo $city ?></option>
<?php } } ?>

                    </div>
    </div>
<div class="col-md-6">
    <div class="error-text"></div>
</div>
</select>


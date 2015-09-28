<?php

require 'connect.php';


// variables that get sent in post must have identical names every where they exist
$state = $_POST['state'];

$sql = "SELECT ID FROM State WHERE State = '$state'";
$result = mysql_query($sql) or die(mysql_error());
$rows = mysql_fetch_assoc($result);
$stateID = $rows['ID'];

$sql2 = "SELECT City FROM City WHERE State_ID = $stateID Order By City ASC ";
$result2 = mysql_query($sql2) or die(mysql_error());

if (mysql_num_rows($result2) > 0) { ?>

    <div class="form-group row" id="form-group-email">
                    <div class="col-md-6">
                        <label class="sr-only" for="city">City</label>

                        <select class='form-control input-lg' name="ddCity" id="ddCity">

                            <option value="state">City</option>

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


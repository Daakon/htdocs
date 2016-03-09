<?php
//block member
require 'imports.php';

// variables that get sent in post must have identical names every where they exist

?>

<form>
    <input type="hidden" id="blockedID" name="blockedID" class="blockedID" value="<?php echo $memberID ?>" />
    <input type="hidden" id="ID" name="ID" class="ID" value="<?php echo $ID ?>" />
    <input type="button" class="btnUnblock" value="Unblock This User" />
</form>

?>


<script>
    function storeRecipients(id,name,firstName) {
        // append ids to receiverID array
        $('#messageForm').append('<input type="hidden" id="receiverID" name="receiverID[]" value="'+id+'" />');
        // preview names for user in UI
        $('#previewNames').append('<span id="'+id+'" style="padding-right:10px;">'+name+'<button style="margin-left:5px;" id="'+id+'" onclick="removeRecipient(this.id)">X</span>');
        // get first name for group name
        $("#messageForm input[name=groupName]").val($("#messageForm input[name=groupName]").val() + ' ' + firstName);
    }

    function removeRecipient(id) {
        $("span[id="+id+"]").remove();
        $("input[value="+id+"]").remove();
    }
</script>

<table id="tblGroupChat" style="margin-bottom:10px;">
<?php

require 'imports.php';

if($_POST)
{
    $q=$_POST['search'];
    $sql =mysql_query("select ID,FirstName,LastName,Email from Members where FirstName like '%$q%' or LastName like '%$q%' order by id LIMIT 5");
    while($row=mysql_fetch_array($sql))
    {
        $name=$row['FirstName'] . ' '.$row['LastName'];
        $firstName = $row['FirstName'];
        $receiverID = $row['ID'];
        $email=$row['Email'];
        ?>

            <br/>
            <tr>
                <td id="tbName" style="border-top:1px solid #e3e3e3;border-bottom:1px solid #e3e3e3;padding-top:5px;padding-bottom:5px;">
            <img src="<?php echo get_users_photo_by_id($receiverID) ?>" style="width:50px; height:50px; float:left; margin-right:6px;display:inline-table" /><?php echo $name; ?>&nbsp;
                <br/>
                <button id="<?php echo $receiverID ?>" name="<?php echo $name ?>" class="<?php echo $firstName ?>" value="<?php echo $name ?>" onclick="storeRecipients(this.id, this.name, this.className);">Add</button>
                </td></tr>


        <?php
    }
}
?>

</table>
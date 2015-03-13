<?php
require 'connect.php';
function getState() {
$sql = "SELECT * FROM State Order By State ASC";
                $result = mysql_query($sql) or die(mysql_error());
                ?>

                <?php
                while ($rows = mysql_fetch_assoc($result)) {
                    $state = $rows['State'];
                    ?>       
                        <option value = "<?php echo $state ?>"><?php echo $state ?></option>
                        <?php } ?>
                    </select>
<?php } 


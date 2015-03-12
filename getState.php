<?php
require 'connect.php';

function getState() {
$sql = "SELECT * FROM State Order By State ASC";
                $result = mysql_query($sql) or die(mysql_error());
                ?>
                <select name = "ddState" id = "ddState" >
                        <option value = ''>State</option>
                <?php
                while ($rows = mysql_fetch_assoc($result)) {
                    $state = $rows['State'];
                    ?>       
                        <option value = "<?php echo $city ?>"><?php echo $State ?></option>
                        <?php } ?>
                    </select>
<?php } 


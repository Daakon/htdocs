<?php

function getState() {
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$sql = "SELECT DISTINCT * FROM State Order By State ASC";
                $result = mysql_query($sql) or die(logError(mysql_error(), $url, "Getting State List"));
                ?>

                <?php
                while ($rows = mysql_fetch_assoc($result)) {
                    $state = $rows['State'];
                    ?>       
                        <option value = "<?php echo $state ?>"><?php echo $state ?></option>
                        <?php } ?>
                    </select>
<?php } 


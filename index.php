<?php
require 'html_functions.php';
require 'calendar.php';
get_head_files();
?>

<body>

<header class="navbar navbar-default navbar-static-top header">
            <img src="images/Rapportbook-Logo-White-Text-Large.png" alt="Rapportbook" height="90px;" width="200px;" style="margin-top:-30px;" />
    <span class="pull-right">
        <input type ="email" name="email" id="email" placeholder="User Name or Email" style="color:black;" />
        <input type="password" name="password" id="password" placeholder="Password" style="color:black;" />
        <input type="submit" name="login" id="login" value="Login" class="login-button" />

        <a href="forgot-passoword.php" style="color:white;font-size:12px;padding-left:100px;">Forgot Your Password</a>
    </span>
   </header>

<div class="container-fluid" style="padding-bottom:10px;">


    <div class="row">
        <div class = "col-sm-4 hidden-xs " >
           <img src="images/college-kids-texting.jpg" height="500px" width="400px" alt="" style="margin-right:70px;border:3px solid black;" />
        </div>

        <div class="col-sm-8 col-xs-12 form ">

            <!--Login div -->
            <div id="login">
                <h2>Sign Up</h2>

                <h4>Click here to see how it works</h4>

            <form>

                    <input class="input-style" type="text" name="firstName" id="firstName" placeholder="First Name" />
                    <br/>

                    <input class="input-style" type="text" name="lastName" id="lastName" placeholder="Last Name" />
                    <br/>

                    <input class="input-style" type="email" name="email" id="email" placeholder="Email" />
                    <br/>

                    <select class = 'input-style' name = "ddGender" id = "ddGender">
                        <option value = "">Gender</option>
                        <option value = "Male">Male</option>
                        <option value = "Female">Female</option>
                    </select>
                    <br/>

                <h4>Birthday</h4>

                <select class="bday-style" id = "ddMonth" name = "ddMonth">
                    <option value = "month">Month</option>

                    <?php echo calendarMonths() ?>
                </select>


                    <select class="bday-style" id = "ddDay" name = "ddDay">
                        <option value = "day">Day</option>

                        <?php echo calendarDays() ?>
                    </select>


                    <select class="bday-style" name = "ddYear" id = "ddYear">
                        <option value = "year">Year</option>

                        <?php echo calendarYears() ?>
                    </select>

                <br/>

                    <input class="input-style" type="password" name="password" id="password" placeholder="Password" />
                    <br/><br/>

                    <input class="btn signup-button" type="submit" name="login" id="login" value="Login" />
            </form>
        </div><!--end of login div -->

            </div>
    </div>

</div>

</body>
</html>



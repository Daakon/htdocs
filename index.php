<?php
require 'connect.php';

require 'html_functions.php';
require 'calendar.php';
require 'getState.php';
require 'category.php';
get_head_files();
?>
<?php get_login_header() ?>



<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>

<script>
    // redirect first time visitors directly to learn more page
    function redirect(){
        var thecookie = readCookie('doRedirect');
        if(!thecookie){
            window.location = '/learn_more.php';
        }}

    function createCookie(name,value,days)
    {
        if (days){
            var date = new Date();date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
        }
        else
            var expires = "";document.cookie = name+"="+value+expires+"; path=/";}

    function readCookie(name){
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++){var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }
    window.onload = function(){
        redirect();
        createCookie('doRedirect','true','999');
    }
</script>


<script>
    function capFname() {
        var fName = document.getElementById('firstName').value;
        document.getElementById('firstName').value = fName.substring(0,1).toUpperCase() + fName.substring(1, fName.length);
    }
</script>

<script>
    function capLname() {
        var lName = document.getElementById('lastName').value;
        document.getElementById('lastName').value = lName.substring(0,1).toUpperCase() + lName.substring(1, lName.length);
    }
</script>

<script type = "text/javascript">
    function checkGoal() {
        var selection = document.getElementById('ddGoal');
        var goal = selection.options[selection.selectedIndex].value;

        if (goal == 1) {
            var service = document.getElementById('service');
            if (service.style.display == 'none') {
                service.style.display = 'block';
            }
            else {
                service.style.display = 'none';
            }
        }
    }
</script>

<script>
        function getCity(sel) {
            var state = sel.options[sel.selectedIndex].value;

                $.ajax({
                    type: "POST",
                    url: "/getCity.php",
                    data: "state="+state,
                    cache: false,
                    beforeSend: function () {

                    },
                    success: function(html) {
                        $("#divCity").html(html);
                    }
                });

        }
</script>

<script>
    function checkSignup() {

        // check email
        var email = document.getElementById('email').value;
        var filter = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;
        if (!filter.test(email)) {
            alert('Please provide a valid email address');
            return false;
        }


        // check interest
        var ddInterest = document.getElementById('interest');
        var interest = ddInterest.options[ddInterest.selectedIndex].value;

        if (interest == '' || year.length == 0) {
            alert('Interest needed');
            return false;
        }
        return true;

    }
</script>

<?php require 'checkLogin.php'; ?>

    <body style="background: white;">

<div class="container-fluid" >

    <div class="row" >


        <div class="col-lg-6 col-md-5 hidden-sm hidden-xs">
            <image src="/images/connections.jpg"  class="center-block" ><br>
            </image>
        </div>

        <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12" >


            <div class="modal fade" id="request_message">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Error</h4>
                        </div>
                        <div class="modal-body">
                            <p>There was a problem submitting your request.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            <div class="visible-xs">
                <div class="bold" >
                    Share Your Interests & Make Connections!
                </div>
            </div>

            <div class="hidden-xs">
                <div class="lead slogan" >
                    Share Your Interests & Make Connections!
                </div>
            </div>


            <a href="/learn_more" class="visible-md visible-lg">
                <h4>About</h4>
            </a>



            <div class="visible-xs visible-sm" style="font-weight: bold;font-size:20px;margin-top:1%;">

                    <ul class="list-inline">
                        <li>
                            <a href="/learn_more">
                                About
                            </a>
                        </li>
                        <li>
                            <a href="/login-mobile">
                                Login
                            </a>
                        </li>

                        <li>
                            <a href="/support">
                                Support
                            </a>
                        </li>
                    </ul>


            </div>




            <form method="post" action="signup.php" id="signup" onsubmit='return checkSignup();' >

                <div class="visible-lg visible-md visible-sm visible-xs">
                    <h2 style="color:red;">Sign Up</h2>
                </div>



                <!--<div class="form-group row" id="form-group-firstName">
                    <div class="col-xs-12 col-md-12 col-md-6 col-lg-6 ">
                        <label class="sr-only" for="firstName">First Name</label>
                        <input class=" form-control input-lg" type="text" name="firstName" id="firstName"
                               placeholder="First Name" onblur="capFname()" />
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>-->

                <!--<div class="form-group row" id="form-group-lastName">
                    <div class="col-md-6">
                        <label class="sr-only" for="lastName">Last Name</label>
                        <input class="form-control input-lg" type="text" name="lastName" id="lastName"
                               placeholder="Last Name" onblur="capLname()"/>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>-->

                <div class="form-group row" id="form-group-email">
                    <div class="col-md-6">
                        <label class="sr-only" for="email">Email Address</label>
                        <input class="form-control input-lg" type="email" name="email" id="email"
                               placeholder="Email"/>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>

               <!-- <div class="form-group row" id="form-group-ddGender">
                    <div class="col-md-6">
                        <label class="sr-only" for="gender">Gender</label>
                        <select class='form-control input-lg' name="ddGender" id="ddGender">
                            <option value="">Gender</option>
                            <option value="1">Male</option>
                            <option value="2">Female</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>-->

                <!--<div class="form-group row" id="form-group-ddState" >
                    <div class="col-md-6">
                        <label class="sr-only" for="ddState">State</label>
                        <select class='form-control input-lg' name="ddState" id="ddState" onchange="getCity(this);">
                            <option value="">State</option>
                            <?php /*echo getState() */?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>-->

                <!--<div id="divCity">

                </div>-->


               <!-- <div class="form-group row" id="form-group-zip">
                    <div class="col-md-6">
                        <label class="sr-only" for="username">Zip</label>
                        <input class="form-control input-lg" type="text" name="zip" id="zip"
                               placeholder="Zip Code"/>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>-->

                <!-- <label>Birthday</label>

                <div class="form-group form-inline row" id="form-group-birthday">
                    <div class="col-md-6">
                        <label class="sr-only" for="ddMonth">Birthday Month</label>
                        <select class="form-control input-lg" id="ddMonth" name="ddMonth">
                            <option value="">Month</option>
                            <?php /*echo calendarMonths() */?>
                        </select>
                        <label class="sr-only" for="ddDay">Birthday Day</label>
                        <select class="form-control input-lg" id="ddDay" name="ddDay">
                            <option value="">Day</option>
                            <?php /*echo calendarDays() */?>
                        </select>
                        <label class="sr-only" for="ddYear">Birthday Year</label>
                        <select class="form-control input-lg" name="ddYear" id="ddYear">
                            <option value="">Year</option>
                            <?php /*echo calendarYears() */?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>-->

                <!--<div class="form-group row" id="form-group-username">
                    <div class="col-md-6">
                        <label class="sr-only" for="username">Username</label>
                        <input class="form-control input-lg" type="text" name="username" id="username"
                               placeholder="Username"/>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>-->

                <!--<div class="form-group row" id="form-group-password">
                    <div class="col-md-6">
                        <label class="sr-only" for="password">Password</label>
                        <input class="form-control input-lg" type="password" name="password" id="password"
                               placeholder="Password"/>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>-->


                <select class="form-control input-lg" id="interest" name="interest">
                    <option value="">Select Your Favorite Interest</option>
                    <?php echo category() ?>
                </select>
                    <br/>


               <!-- <div class="form-group row" id="form-group-phone">
                    <div class="col-md-6">
                        <label class="sr-only" for="phone">Phone</label>
                        <input class="form-control input-lg" type="text" name="phone" id="phone"
                               placeholder="2125551212 (Mobile)"/>
                        <small>Your mobile phone number will only be used for text notifications when we receive interest matches.</small>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>-->

                <small>By clicking sign up, you agree to our <a href="/terms">terms</a></small>
                <br/><br/>

                <div class="form-group row">
                    <div class="col-md-6">
                        <input class="btn btn-default " type="submit" name="signup" id="signup"
                               value="Sign Up"/>

                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">



                    </div>


                </div>
        </div>
        </form>


    </div>
</div>



<?php get_footer_files(); ?>
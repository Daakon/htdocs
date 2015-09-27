<?php
require 'connect.php';

require 'html_functions.php';
require 'calendar.php';
require 'getState.php';
require 'category.php';
get_head_files();
?>
<?php get_login_header() ?>

    <body>
<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script>
    // This is called with the results from from FB.getLoginStatus().
    function statusChangeCallback(response) {
        console.log('statusChangeCallback');
        console.log(response);
        // The response object is returned with a status field that lets the
        // app know the current login status of the person.
        // Full docs on the response object can be found in the documentation
        // for FB.getLoginStatus().
        if (response.status === 'connected') {
            // Logged into your app and Facebook.
            testAPI();
        } else if (response.status === 'not_authorized') {
            // The person is logged into Facebook, but not your app.
            document.getElementById('status').innerHTML = 'Please log ' +
            'into this app.';
        } else {
            // The person is not logged into Facebook, so we're not sure if
            // they are logged into this app or not.
            document.getElementById('status').innerHTML = 'Please log ' +
            'into Facebook.';
        }
    }

    // This function is called when someone finishes with the Login
    // Button.  See the onlogin handler attached to it in the sample
    // code below.
    function checkLoginState() {
        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });
    }

    window.fbAsyncInit = function() {
        FB.init({
            appId      : '1537351149864603',
            cookie     : true,  // enable cookies to allow the server to access
                                // the session
            xfbml      : true,  // parse social plugins on this page
            version    : 'v2.2' // use version 2.2
        });

        // Now that we've initialized the JavaScript SDK, we call
        // FB.getLoginStatus().  This function gets the state of the
        // person visiting this page and can return one of three states to
        // the callback you provide.  They can be:
        //
        // 1. Logged into your app ('connected')
        // 2. Logged into Facebook, but not your app ('not_authorized')
        // 3. Not logged into Facebook and can't tell if they are logged into
        //    your app or not.
        //
        // These three cases are handled in the callback function.

        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });

    };

    // Load the SDK asynchronously
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    // Here we run a very simple test of the Graph API after login is
    // successful.  See statusChangeCallback() for when this call is made.
    function testAPI() {
        console.log('Welcome!  Fetching your information.... ');
        FB.api('/me', function(response) {
            console.log(response);
            console.log('Successful login for: ' + response.name);
            document.getElementById('status').innerHTML =
                'Thanks for logging in, ' + response.name + '!';

            // parse birthday sent from FB to match MySQL Format
            var date = new Date(response.birthday);
            var d = date.getDate();
            var m = date.getMonth() + 1;
            var y = date.getFullYear();
            var birthday = '' + y + '-' + (m<=9 ? '0' + m : m) + '-' + (d <= 9 ? '0' + d : d);

            var data ={
                firstName:response.first_name,lastName:response.last_name,email:response.email,gender:response.gender,birthday:birthday,fb_id:response.id
            };

            $.post( 'signup.php', data)
                .done(function( data ) {
                    //alert("Your profile was successfully set up");
                    window.location = "/home.php";
                })
                .fail(function() {
                    alert( "error on create account" );

                })
            ;

        });



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

<?php require 'checkLogin.php'; ?>

<div class="container-fluid">


    <div class="row">
        <div class="col-lg-6 col-md-5 hidden-sm hidden-xs">
            <image src="/images/interests-lg.JPG" style="border:2px solid black" class="center-block" ><br>
            </image>
            <h3 align="center"><span style="font-style: italic;color:red;font-weight: bold">Network And Promote Your Interests</span></h3>
        </div>

        <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12 ">

            <fb:login-button data-max-rows="1" data-size="xlarge" data-show-faces="true" data-auto-logout-link="true" scope="public_profile,email" onlogin="checkLoginState();" style="margin-top:10px;">
            </fb:login-button>
            <div id="status">
            </div>


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

            <div class="visible-xs" style="font-weight: bold;font-size:20px">
                <br/>
                <a href="login-mobile.php" style="color:red;">Login With Rapportbook</a>

            </div>


                <a href="/learn_more.php">
                    <br/>
                    <h3>Learn More</h3>
                </a>


            <form method="post" action="signup.php" id="rb_signup_form" >

                <div class="visible-lg visible-md visible-sm visible-xs">
                    <h2 style="color:red;">Sign Up</h2>
                </div>



                <div class="form-group row" id="form-group-firstName">
                    <div class="col-xs-12 col-md-12 col-md-6 col-lg-6 ">
                        <label class="sr-only" for="firstName">First Name</label>
                        <input class=" form-control input-lg" type="text" name="firstName" id="firstName"
                               placeholder="First Name" onblur="capFname()" />
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>
                <div class="form-group row" id="form-group-lastName">
                    <div class="col-md-6">
                        <label class="sr-only" for="lastName">Last Name</label>
                        <input class="form-control input-lg" type="text" name="lastName" id="lastName"
                               placeholder="Last Name" onblur="capLname()"/>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>

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

                <div class="form-group row" id="form-group-ddGender">
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
                </div>

                <div class="form-group row" id="form-group-email">
                    <div class="col-md-6">
                        <label class="sr-only" for="email">City</label>
                        <input class="form-control input-lg" type="text" name="city" id="city"
                               placeholder="City"/>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>

                <div class="form-group row" id="form-group-ddGender">
                    <div class="col-md-6">
                        <label class="sr-only" for="ddState">State</label>
                        <select class='form-control input-lg' name="ddState" id="ddState">
                            <option value="state">State</option>
                            <?php echo getState() ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>

                <div class="form-group row" id="form-group-zip">
                    <div class="col-md-6">
                        <label class="sr-only" for="username">Zip</label>
                        <input class="form-control input-lg" type="text" name="zip" id="zip"
                               placeholder="Zip Code"/>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>

                <label>Birthday</label>

                <div class="form-group form-inline row" id="form-group-birthday">
                    <div class="col-md-6">
                        <label class="sr-only" for="ddMonth">Birthday Month</label>
                        <select class="form-control input-lg" id="ddMonth" name="ddMonth">
                            <option value="month">Month</option>
                            <?php echo calendarMonths() ?>
                        </select>
                        <label class="sr-only" for="ddDay">Birthday Day</label>
                        <select class="form-control input-lg" id="ddDay" name="ddDay">
                            <option value="day">Day</option>
                            <?php echo calendarDays() ?>
                        </select>
                        <label class="sr-only" for="ddYear">Birthday Year</label>
                        <select class="form-control input-lg" name="ddYear" id="ddYear">
                            <option value="year">Year</option>
                            <?php echo calendarYears() ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>

                <div class="form-group row" id="form-group-username">
                    <div class="col-md-6">
                        <label class="sr-only" for="username">Username</label>
                        <input class="form-control input-lg" type="text" name="username" id="username"
                               placeholder="Username"/>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>

                <div class="form-group row" id="form-group-password">
                    <div class="col-md-6">
                        <label class="sr-only" for="password">Password</label>
                        <input class="form-control input-lg" type="password" name="password" id="password"
                               placeholder="Password"/>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>


                <select class="form-control input-lg" id="interest" name="interest">
                    <option value="">Select Your Main Interest</option>
                    <?php echo category() ?>
                </select>
                    <br/>


                <div class="form-group row" id="form-group-phone">
                    <div class="col-md-6">
                        <label class="sr-only" for="phone">Phone</label>
                        <input class="form-control input-lg" type="text" name="phone" id="phone"
                               placeholder="2125551212 (Mobile)"/>
                        <small>Your mobile phone number will only be used for text notifications when we receive interest matches.</small>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>

                <small>By clicking sign up, you agree to our <a href="/terms.php">terms</a></small>
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
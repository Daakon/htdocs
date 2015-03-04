<?php
require 'connect.php';
require 'html_functions.php';
require 'calendar.php';
get_head_files();
?>

<body>

<?php get_login_header() ?>

<div class="container-fluid container-fluid-custom">


    <div class="row">
        <div class = "col-sm-4 hidden-xs" >
           <img src="images/college-kids-texting.jpg" height="350" width="523" alt="" class="index-image"/>
        </div>

        <div class="col-sm-8 col-xs-12 form ">

            <!--Login div -->
            <div id="login">
                <h2>Sign Up</h2>

                <h4>Click here to see how it works</h4>

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
                appId      : 1537351149864603,
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
                console.log('Successful login for: ' + response.name);
                document.getElementById('status').innerHTML =
                'Thanks for logging in, ' + response.name + '!';
                });
                }
                </script>

                <!--
                Below we include the Login Button social plugin. This button uses
                the JavaScript SDK to present a graphical Login button that triggers
                the FB.login() function when clicked.
-->

                <fb:login-button scope="public_profile,email" onlogin="checkLoginState();">
                </fb:login-button>

                <div id="status">
                </div>

                <div class="modal fade" id="request_message">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Error</h4>
                      </div>
                      <div class="modal-body">
                        <p>There was a problem submitting your request.</p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
                      </div>
                    </div><!-- /.modal-content -->
                  </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->

                <br/><br/>
            <form method ="post" action="signup.php" id="rb_signup_form">
                    <div class="form-group row" id="form-group-firstName">
                        <div class="col-md-6">
                            <label class="sr-only" for="firstName">First Name</label>
                            <input class="input-style form-control" type="text" name="firstName" id="firstName" placeholder="First Name" />
                        </div>
                        <div class="col-md-6">
                            <div class="error-text"></div>
                        </div>
                    </div>
                    <div class="form-group row" id="form-group-lastName">
                        <div class="col-md-6">
                            <label class="sr-only" for="lastName">Last Name</label>
                            <input class="input-style form-control" type="text" name="lastName" id="lastName" placeholder="Last Name" />
                        </div>
                        <div class="col-md-6">
                            <div class="error-text"></div>
                        </div>
                    </div>

                    <div class="form-group row" id="form-group-email">
                        <div class="col-md-6">
                            <label class="sr-only" for="email">Email Address</label>
                            <input class="input-style form-control" type="email" name="email" id="email" placeholder="Email" />
                        </div>
                        <div class="col-md-6">
                            <div class="error-text"></div>
                        </div>
                    </div>

                    <div class="form-group row" id="form-group-ddGender">
                        <div class="col-md-6">
                            <label class="sr-only" for="ddGender">Gender</label>
                            <select class = 'input-style form-control' name = "ddGender" id = "ddGender">
                                <option value = "">Gender</option>
                                <option value = "1">Male</option>
                                <option value = "2">Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="error-text"></div>
                        </div>
                    </div>

                    <label>Birthday</label>
                    <div class="form-group form-inline row" id="form-group-birthday">
                        <div class="col-md-6">
                            <label class="sr-only" for="ddMonth">Birthday Month</label>
                            <select class="bday-style form-control" id = "ddMonth" name = "ddMonth">
                                <option value = "month">Month</option>
                                <?php echo calendarMonths() ?>
                            </select>
                            <label class="sr-only" for="ddDay">Birthday Day</label>
                            <select class="bday-style form-control" id = "ddDay" name = "ddDay">
                                <option value = "day">Day</option>
                                <?php echo calendarDays() ?>
                            </select>
                            <label class="sr-only" for="ddYear">Birthday Year</label>
                            <select class="bday-style form-control" name = "ddYear" id = "ddYear">
                                <option value = "year">Year</option>
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
                            <input class="input-style form-control" type="text" name="username" id="username" placeholder="Username" />
                        </div>
                        <div class="col-md-6">
                            <div class="error-text"></div>
                        </div>
                    </div>
                    
                    <div class="form-group row" id="form-group-password">
                        <div class="col-md-6">
                            <label class="sr-only" for="password">Password</label>
                            <input class="input-style form-control" type="password" name="password" id="password" placeholder="Password" />
                        </div>
                        <div class="col-md-6">
                            <div class="error-text"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <input class="btn btn-default signup-button" type="submit" name="signup" id="signup" value="Sign Up" />
                        </div>
                    </div>
            </form>
        </div><!--end of login div -->

            </div>
    </div>

</div>


<?php get_footer_files();?>



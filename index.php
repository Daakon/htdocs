<?php
require 'connect.php';
require 'checkLogin.php';
require 'html_functions.php';
require 'calendar.php';
get_head_files();
?>

<body>

<?php get_login_header() ?>

<div class="container-fluid">


    <div class="row">
        <div class="col-lg-6 col-md-5 hidden-sm hidden-xs">
            <img src="/images/dance.jpg" height="350" width="100%" alt="" class="index-image"/>
            <h2>Share Your Talent</h2>
        </div>

        <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12 ">




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
                    <a href="login-mobile.php">Login Here</a>
                </div>


                <form method="post" action="signup.php" id="rb_signup_form" >
                    <h2>Sign Up</h2>
                    <h4><a href="how_it_works.php">Click Here To See How It Works</a></h4>

                    <div class="form-group row" id="form-group-firstName">
                        <div class="col-xs-12 col-md-12 col-md-6 col-lg-6 ">
                            <label class="sr-only" for="firstName">First Name</label>
                            <input class=" form-control input-lg" type="text" name="firstName" id="firstName"
                                   placeholder="First Name"/>
                        </div>
                        <div class="col-md-6">
                            <div class="error-text"></div>
                        </div>
                    </div>
                    <div class="form-group row" id="form-group-lastName">
                        <div class="col-md-6">
                            <label class="sr-only" for="lastName">Last Name</label>
                            <input class="form-control input-lg" type="text" name="lastName" id="lastName"
                                   placeholder="Last Name"/>
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
                            <label class="sr-only" for="ddGender">Gender</label>
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
                    <div class="form-group row">
                        <div class="col-md-6">
                            <input class="btn btn-default " type="submit" name="signup" id="signup"
                                   value="Sign Up"/>
                        </div>
                    </div>
                </form>


        </div>
    </div>

</div>


<?php get_footer_files(); ?>



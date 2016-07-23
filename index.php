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

    <style>
        a {
            color:black;
        }
    </style>

<?php require 'checkLogin.php'; ?>

    <body class="index" style="background: url(images/girls-selfie.jpeg) no-repeat center center fixed;
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;">

    <div class="container-fluid" >

        <div class="row" >


            <div class="col-lg-12 col-md-12 ">


                <div align="center" class="index-menu" >

                    <ul class="list-inline" style="margin-top:-40px;">

                        <a href="/learn_more" style="color:#E30022;">
                            It Pays To Be Social: Learn How &nbsp;&nbsp;&nbsp;
                        </a>



                        <a class="hidden-xs" href="/learn_more#signup" style="color:white;">
                            Sign Up &nbsp;&nbsp;&nbsp;
                        </a>



                        <a class="visible-xs" href="/login-mobile" style="color:white;">
                            Sign Up or Login &nbsp;&nbsp;&nbsp;
                        </a>



                        <a href="/support" style="color:white;">
                            Support &nbsp;&nbsp;&nbsp;
                        </a>





                    </ul>

                </div>
            </div>
        </div>
    </div>




<?php get_footer_files(); ?>
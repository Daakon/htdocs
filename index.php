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

<body>

<div class="container-fluid" >

    <div class="row" >


        <div class="col-lg-12 col-md-12 " >

<!--Desktop view -->
                <div class="hidden-xs" style="margin-left:20%">
                    <a href="/learn_more" style="display:inline-block;font-weight: bold;padding-left:10px;padding-right:10px;">
                        <img src="/images/about.png" height="80" width="80" />
                    </a>


                    <a class="hidden-xs" href="/learn_more#signup" style="display:inline-block;padding-right:10px;">
                        <img src="/images/signup.png" height="80" width="80" />
                    </a>


                    <a href="http://twitter.com/officialplaydoe" style="display:inline-block;">
                        <img src="/images/support.png" height="90" width="90" />
                    </a>

                <div style="clear:both;display:inline-block;">
                    <img src="/images/phone-gift.png" style="width: 500px" />
                </div>
            </div>

            <!--Mobile View -->
            <div class="visible-xs" style="margin-left:15%;">

                <a href="/learn_more" style="float:left;margin-right:4%">
                    <img src="/images/about.png" height="70" width="70" />
                </a>

                <a class="visible-xs" href="/login-mobile" style="float:left;margin-right:2%">
                    <img src="/images/signup.png" height="70" width="70" />
                </a>

                <a href="http://twitter.com/officialplaydoe" style="float:left;margin-right:2%">
                    <img src="/images/support.png" height="75" width="75" />
                </a>

                <div style="margin-left:-10%">
                    <img src="/images/phone-gift.png" style="width: 500px" />
                </div>


            </div>



        </div>

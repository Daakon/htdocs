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

    <body class="index">

<div class="container-fluid" >

    <div class="row" >


        <div class="col-lg-12 col-md-12 ">


            <div align="center" class="index-menu">

                    <ul class="list-inline">
                        <li>
                            <a href="/learn_more">
                               About
                            </a>
                        </li>
                        <li>
                            <a class="visible-xs" href="/login-mobile">
                                Login
                            </a>
                        </li>

                        <li>
                            <a href="/support">
                                Support
                            </a>
                        </li>
                    </ul>

                <img src="/images/Rapportbook-Logo.png" height="200" width="200" />

            </div>
                </div>
        </div>
    </div>




<?php get_footer_files(); ?>
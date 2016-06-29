<?php
require 'imports.php';

get_head_files();
get_header();
$_SESSION['ID'] = $ID;
if (!empty($_SESSION['ID'])) {
    $ID = $_SESSION['ID'];
}
?>



    <script>
        function checkSignup() {

            // check email
            var email = document.getElementById('email').value;
            var filter = /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i;
            if (!filter.test(email)) {
                alert('Please provide a valid email address');
                return false;
            }

            // check state
            var ddState = document.getElementById('State');
            var state = ddState.options[ddState.selectedIndex].value;

            if (state == '') {
                alert('State needed');
                return false;
            }

            return true;

        }
    </script>

    <script type="text/javascript">
        function showComments(id) {
            var e = document.getElementById(id);
            if (e.style.display == 'none') {
                e.style.display = 'block';
            }
            else
                e.style.display = 'none';
        }
    </script>

    <style>
        video {
            width:70%;
            height: auto;
            object-fit: inherit;
        }
        iframe {
            width: 90%;
            max-width: 90%;
        }

        .input-lg {
            max-width: 95%;
        }
    </style>

    <body class="index">

    <div align="left" >


        <!-- Modal -->
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Referral ID</h4>
                    </div>
                    <div class="modal-body">
                        <p>
                            A referral ID is not required to sign up but if someone did refer you, please provide their Referral ID.
                            This is one of the ways members earn points to redeem for cash and gift cards. Make sure you provide the
                            correct Referral ID and double check it before you enter it as it cannot be undone once you sign up.
                            Once you sign up, you will be assigned a Referral ID which you can give to people to use as well.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <!-- End Modal -->

    <div class="row" style="margin-top:-20px;">
        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12" >

            <div class="visible-xs" style="padding-left:10px;padding-top:10px;">
                <a href="/login-mobile"><button class="btn btn-default">Login</button></a> &nbsp;&nbsp;
                <a href="#signup"><button class="btn btn-default">Sign Up</button></a>
            </div>

            <div class="hidden-xs" style="padding-left:10px;padding-top:10px;">
                <a href="../"><button class="btn btn-default">Login</button></a> &nbsp;&nbsp;
                <a href="#signup"><button class="btn btn-default">Sign Up</button></a>
            </div>

            <!--Mobile -->
            <div class="visible-xs">

                <table style="margin-left:10px;margin-top:20px;margin-bottom:10px;margin-right:10px;">

                    <tr>
                        <td style="padding-left:0px;">
                            <h4>
                                Playdoe is a social application that offers fun ways to make money.
                            </h4>
                            <lead>
                                Checkout our <a href="http://officialplaydoe.tumblr.com/" target="_blank">blog</a> to learn more.
                            </lead>
                        </td>
                    </tr>

                </table>
            </div>

            <!--Desktop -->
            <div class="hidden-xs">

                <table style="margin-left:10px;margin-top:20px;">
                    <tr>
                        <td>
                            <img src="/images/Like-Redeem-Large.png" height="100px" width="100px" style="margin-bottom:10px;" />
                        </td>
                        <td style="padding-left:10px;">
                            <h4>
                                Playdoe is a social application that offers fun ways to make money.
                            </h4>
                            <lead>
                            Checkout our <a href="http://officialplaydoe.tumblr.com/" target="_blank">blog</a> to learn more.
                            </lead>
                        </td>
                    </tr>

                </table>

            </div>


        </div>



    </div>

    <div class="row" style="background:#e3e3e3;padding-left:10px;">
        <a id="signup"></a>
        <div style="padding-left:20px;">
            <h1 class="bold">Sign Up</h1>
            <span class="lead" style="font-weight: 500;">It's Free!</span>
            <br/><br/>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <!--                    <img src="--><?php //echo $imagesPath ?><!--NetworkGraphic.png" />-->


            <form method="post" action="signup.php" id="signup" onsubmit='return checkSignup();' >

                <div class="form-group row" id="form-group-email">
                    <div class="col-md-6">
                        <label class="sr-only" for="email">Email Address</label>
                        <input class="form-control input-lg" type="email" name="email" id="email"
                               placeholder="Email"/>

                        <label class="sr-only" for="referredBy">Referred By</label>
                        <input class="form-control input-lg" type="text" name="referredBy" id="referredBy"
                               placeholder="Referral ID" /> <button type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal">What's Referral ID ?</button>
                    </div>
                    <div class="col-md-6">
                        <div class="error-text"></div>
                    </div>
                </div>


                <br/>

                <small>By clicking sign up, you agree to our <a href="/terms">terms</a></small>
                <br/><br/>

                <div class="form-group row">
                    <div class="col-md-6">
                        <input class="btn btn-default " type="submit" name="signup" id="signup"
                               style="background:#E30022;color:white;"     value="Sign Up"/>

                    </div>
                </div>

            </form>
            <hr/>
            <h4>
                <div class="visible-xs">
                    <a href="/login-mobile"><div class="btn btn-default">Login</div></a>
                </div>

                <div class="hidden-xs">
                    <a href="../"><div class="btn btn-default">Login</div></a>
                </div>


            </h4>

        </div>

        <div class="col-lg-5 col-lg-offset-1 col-md-5 col-md-offset-1 col-sm-12 col-xs-12">


            <br/>

            <div style="margin-top:60px;">


                <!--Content Goes Here -->

            </div>

        </div>
    </div>

<?php get_footer_files() ?>
<?php
require 'connect.php';
require 'getSession.php';
require 'html_functions.php';
require 'mediaPath.php';
require 'findURL.php';
require 'model_functions.php';
require 'getState.php';

get_head_files();
get_header();
require 'memory_settings.php';
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$ID = $_SESSION['ID'];

?>

<style>

    iframe {
        max-width: 100%;
        max-height: 500px;
    }

    img {
        max-width: 100%;
        max-height:500px;
    }

    video {
        max-width: 100%;
        max-height: 500px;
    }

    embed {
        max-width: 100%;
        max-height: 500px;
    }

    script {
        max-width: 100%;
        max-height: 500px;
    }

    .btnApprove {
        background: url("/images/gray_check.png") no-repeat;
        width: 30px;
        height: 30px;
        border: none;
    }

    .btnDisapprove {
        background: url("/images/red_check.png") no-repeat;
        width: 30px;
        height: 30px;
        border: none;
    }
</style>



<body>

<div class="container" >
    <div class="row row-padding">

        <a href ="/home.php" class="black-link"><h4>Roll Call</h4></a>
        <br/><br/>

        <div class="col-md-offset-2 col-md-8 col-lg-offset-2 col-lg-8 roll-call ">

            <?php
            $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            preg_match("/[^\/]+$/",$url ,$match);
            $username = $match[0];

                $sql = "SELECT
                        Members.FirstName As FirstName,
                        Members.LastName As LastName,
                        Members.Email As Email,
                        Members.Password As Password,
                        Members.DOB As DOB,
                        Profile.ProfilePhoto As ProfilePhoto,
                        Profile.ProfileVideo As ProfileVideo,
                        Profile.HomeCity As HomeCity,
                        Profile.HomeState As HomeState,
                        Profile.CurrentCity As CurrentCity,
                        Profile.CurrentState As CurrentState,
                        Profile.Interests As Interests,
                        Profile.Books As Books,
                        Profile.Movies As Movies,
                        Profile.Food As Food,
                        Profile.Dislikes As Dislikes,
                        Profile.Plan As Plan
                        FROM Members, Profile
                        WHERE Members.ID = $ID
                        AND Profile.Member_ID = $ID ";

            $result = mysql_query($sql) or die(mysql_error());

            if (mysql_num_rows($result) == 0) {
                echo "<script>alert('Profile not found'); location = 'home.php'</script>";
            }

            $rows = mysql_fetch_assoc($result);

            $profilePhoto = $rows['ProfilePhoto'];
            $profileVideo = $rows['ProfileVideo'];
            $firstName = $rows['FirstName'];
            $lastName = $rows['LastName'];
            $homeCity = $rows["HomeCity"];
            $homeState = $rows['HomeState'];
            $currentCity = $rows['CurrentCity'];
            $currentState = $rows['CurrentState'];
            $interests = $rows['Interests'];
            $books = $rows['Books'];
            $movies = $rows['Movies'];
            $food = $rows['Food'];
            $dislikes = $rows["Dislikes"];
            $plan = $rows['Plan'];
            $email = $rows['Email'];
            $password = $rows['Password'];
            $dob = $rows['DOB'];
            $username = $rows['Username'];


            ?>

            <div align ="center">
            <img src = "<?php echo $mediaPath.$profilePhoto ?>" class="profilePhoto" alt="Profile Photo" />
            </div>


            <form method="post" enctype="multipart/form-data" action="">
                <img src="/images/image-icon.png" height="30px" width="30px" alt="Photos/Video"/>
                <strong>Upload A Profile Photo</strong>
                <input type="file" width="10px;" name="flPostMedia" id="flPostMedia"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="500000000"
                <br/>
                <br/>
                <input type="submit" class="post-button" name="photo" id="photo" value="Upload Photo"/>
            </form>

            <br/>
            <hr/>
            <br/>

            <!--Profile video --------------------------------------------------------------------------------->

            <div align ="center">
                <img src = "<?php echo $mediaPath.$profileVideo ?>" class="profileVideo" alt="Profile Photo" />
            </div>

            <form method="post" enctype="multipart/form-data" action="">
                <img src="/images/image-icon.png" height="30px" width="30px" alt="Photos/Video"/>
                <strong>Upload A Profile Video</strong>
                <input type="file" width="10px;" name="flPostMedia2" id="flPostMedia2"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="500000000"
                <br/>
                <br/>
                <input type="submit" class="post-button" name="video" id="video" value="Upload Profile Video"/>
            </form>

            <!--Profile ---------------------------------------------------------------------------------------->

            <br/><br/>

            <form method="post" action = "">

                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type ="text" class="form-control" id="firstName" name="firstName" value="<?php echo $firstName ?>" />
                </div>

                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $lastName ?>" />
                 </div>

                <div class="form-group">
                    <label for="homeCity">Hone City</label>
                    <input type="text" class="form-control" id="homeCity" name="homeCity" value="<?php echo $homeCity ?>" />
                </div>

                <div class="form-group">
                <label for="homeState">State</label>
                <select class="form-control">
                    <option value="<?php echo $state ?>"><?php echo $homeState ?></option>
                    <?php getState() ?>
                </select>
                </div>

                <div class="form-group">
                <label for="currentCity">Current City</label>
                <input type="text" class="form-control" id="currentCity" name="currentCity" value="<?php $currentCity ?>" />
                </div>

                <div class="form-group">
                    <label for="currentState">Current State</label>
                    <select class="form-control">
                        <option value="<?php echo $state ?>"><?php echo $currentState ?></option>
                        <?php getState() ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="interests">Interests</label>
                    <textarea class="form-control" id="interests" name="interests"><?php echo $interests ?> </textarea>
                </div>

                <div class="form-group">
                    <label for="books">Favorite Books</label>
                    <textarea class="form-control" id="books" name="books" ><?php echo $books ?></textarea>
                </div>

                <div class="form-group">
                    <label for="movies">Favorite Movies</label>
                    <textarea class="form-control" id="movies" name="movies"><?php echo $movies ?></textarea>
                </div>

                <div class="form-group">
                    <label for="food">Favorite Food</label>
                    <textarea class="form-control" id="food" name="food"><?php echo $food ?></textarea>
                </div>

                <div class="form-group">
                    <label for="dislikes">Dislikes</label>
                    <input type="text" class="form-control" id="dislikes" name="dislikes" value="<?php echo $dislikes ?>" />
                </div>

                <div class="form-group">
                    <label for="plan">5 Year Plan</label>
                    <textarea class="form-control" id="plan" name="plan"><?php echo $plan ?></textarea>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="lastName" name="lastName" value="<?php echo $email ?>" />
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo $username ?>" readonly="readonly" />
                </div>


            </form>

          <!------------->
            </div>
        </div>
    </div>


<?php
require_once('db/dbinit.php');
require_once(PUBLIC_PATH . '/classes/users.php');

$user = new User($con);

// if user is already logged in, go to "My Texts" section
if ($user->isLoggedIn()) {
    header('Location:/texts.php');
}
?>

<?php require_once 'simpleheader.php'; ?>

<!-- *****************************************************************************************************************
HEADERWRAP
***************************************************************************************************************** -->
    <div id="headerwrap" class="headerwrap nice-wallpaper-1">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <h1>Learn a language by reading
                        <br>
                        <u>interesting</u> texts.</h1>
                    <h5>Want to know more about
                        <a href="">active-reading</a>?</h5>
                    <br/>
                    <button type="button" class="btn btn-lg btn-success" onclick="window.location.href='chooselanguage.php'">Start learning</button>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                </div>
                <div class="col-xs-12 flag-col">
                    <div class="row">
                        <div class="col-xs-4 col-md-2">
                            <a href="register.php?tolang=english">
                                <span class="flag-icon">
                                    <img src="/images/flags/english.svg" alt="English" class="flag-icon">
                                    <p>English</p>
                                </span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-md-2">
                            <a href="register.php?tolang=spanish">
                                <span class="flag-icon">
                                    <img src="/images/flags/spanish.svg" alt="Spanish" class="flag-icon">
                                    <p>Spanish</p>
                                </span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-md-2">
                            <a href="register.php?tolang=portuguese">
                                <span class="flag-icon">
                                    <img src="/images/flags/portuguese.svg" alt="Portuguese" class="flag-icon">
                                    <p>Portuguese</p>
                                </span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-md-2">
                            <a href="register.php?tolang=french">
                                <span class="flag-icon">
                                    <img src="/images/flags/french.svg" alt="French" class="flag-icon">
                                    <p>French</p>
                                </span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-md-2">
                            <a href="register.php?tolang=italian">
                                <span class="flag-icon">
                                    <img src="/images/flags/italian.svg" alt="Italian" class="flag-icon">
                                    <p>Italian</p>
                                </span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-md-2">
                            <a href="register.php?tolang=german">
                                <span class="flag-icon">
                                    <img src="/images/flags/german.svg" alt="German" class="flag-icon">
                                    <p>German</p>
                                </span>
                            </a>
                        </div>
                    </div>
                    <!--/flag-row -->
                </div>
                <!--/col -->
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /headerwrap -->



    <!-- *****************************************************************************************************************
SERVICE LOGOS
***************************************************************************************************************** -->
    <div id="service" class="service">
        <div class="container">
            <div class="row centered">
                <div class="col-md-4">
                    <i class="fa fa-heart"></i>
                    <h4>Handsomely Crafted</h4>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's
                        standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled
                        it to make a type specimen book.</p>
                    <p>
                        <br/>
                        <a href="#" class="btn btn-theme">More Info</a>
                    </p>
                </div>
                <div class="col-md-4">
                    <i class="fa fa-book-open"></i>
                    <h4>Active Reading</h4>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's
                        standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled
                        it to make a type specimen book.</p>
                    <p>
                        <br/>
                        <a href="#" class="btn btn-theme">More Info</a>
                    </p>
                </div>
                <div class="col-md-4">
                    <i class="fa fa-trophy"></i>
                    <h4>Quality Theme</h4>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's
                        standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled
                        it to make a type specimen book.</p>
                    <p>
                        <br/>
                        <a href="#" class="btn btn-theme">More Info</a>
                    </p>
                </div>
            </div>
            <!--/row -->
        </div>
        <!--/container -->
    </div>
    <!--/service -->




    <?php require_once 'footer.php'?>

    <script type="text/javascript" src="js/register.js"></script>
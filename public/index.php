<?php
require_once('db/dbinit.php');
require_once(PUBLIC_PATH . '/classes/users.php');

$user = new User($con);

// if user is already logged in, go to "My Texts" section
if ($user->isLoggedIn()) {
    header('Location:texts.php');
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
                    <h1>Learn a language
                        <br>
                        by reading
                        <br>
                        your <u>favorite</u> texts.</h1>
                    <h5>Want to know more about
                        <a href=""><u>active-reading</u></a>?</h5>
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
                                    <img src="images/flags/english.svg" alt="English" class="flag-icon">
                                    <p>English</p>
                                </span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-md-2">
                            <a href="register.php?tolang=spanish">
                                <span class="flag-icon">
                                    <img src="images/flags/spanish.svg" alt="Spanish" class="flag-icon">
                                    <p>Spanish</p>
                                </span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-md-2">
                            <a href="register.php?tolang=portuguese">
                                <span class="flag-icon">
                                    <img src="images/flags/portuguese.svg" alt="Portuguese" class="flag-icon">
                                    <p>Portuguese</p>
                                </span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-md-2">
                            <a href="register.php?tolang=french">
                                <span class="flag-icon">
                                    <img src="images/flags/french.svg" alt="French" class="flag-icon">
                                    <p>French</p>
                                </span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-md-2">
                            <a href="register.php?tolang=italian">
                                <span class="flag-icon">
                                    <img src="images/flags/italian.svg" alt="Italian" class="flag-icon">
                                    <p>Italian</p>
                                </span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-md-2">
                            <a href="register.php?tolang=german">
                                <span class="flag-icon">
                                    <img src="images/flags/german.svg" alt="German" class="flag-icon">
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
                    <i class="fab fa-osi"></i>
                    <h4>Open source</h4>
                    <p>LangX is open source software, meaning you can download and fiddle with its source code. This may not mean anything
                        to you, but is critically important. It allows us to be super transparent and build a community of users and developers 
                        around LangX. Moreover, with some technical knowledge and persistance you can build your own LangX environment. 
                        This can be particularly useful for schools and other educational organizations.</p>
                    <p>
                        <br/>
                        <a href="#" class="btn btn-theme">More Info</a>
                    </p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-book-open"></i>
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
                    <i class="fas fa-mobile-alt"></i>
                    <h4>Use it on any device</h4>
                    <p>You can use LangX on mobile and desktop devices as long as they have an Internet connection. We offer addons for Firefox 
                        and Chrome to add texts to your library with the click of a mouse. Bookmarklets are also available for those who prefer
                        a browser agnostic solution.</p>
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
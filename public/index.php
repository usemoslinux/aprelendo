<?php
require_once 'db/dbinit.php';
require_once PUBLIC_PATH . '/classes/users.php';

$user = new User($con);

// if user is already logged in, go to "My Texts" section
if ($user->isLoggedIn()) {
    header('Location:texts.php');
    exit;
}
?>

    <?php require_once 'simpleheader.php';?>

<!-- HEADERWRAP -->
    <div id="headerwrap" class="headerwrap pattern-wallpaper">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <img class="img-responsive" src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/be/Southern_plains_grey_langur_%28Semnopithecus_dussumieri%29_female_head.jpg/1200px-Southern_plains_grey_langur_%28Semnopithecus_dussumieri%29_female_head.jpg" alt="How it works">
                </div>
                <div class="col-lg-6">
                    <h1>Learn languages by reading your <u>favorite</u> texts.</h1>
                    <h5>Want to know more about
                        <a href="activereading.php">
                            <u>active-reading</u>
                        </a>?</h5>
                    <br/>
                    <button type="button" class="btn btn-lg btn-success" onclick="window.location.href='chooselanguage.php'">Start learning</button>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                </div>
                </div></div>
                <div class="container-fluid">
                    <div class="row flag-col">
                        <div class="col-xs-4 col-md-2">
                            <a href="register.php?tolang=english">
                                <span class="flag-icon">
                                    <img src="images/flags/en.svg" alt="English" class="flag-icon">
                                    <p>English</p>
                                </span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-md-2">
                            <a href="register.php?tolang=spanish">
                                <span class="flag-icon">
                                    <img src="images/flags/es.svg" alt="Spanish" class="flag-icon">
                                    <p>Spanish</p>
                                </span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-md-2">
                            <a href="register.php?tolang=portuguese">
                                <span class="flag-icon">
                                    <img src="images/flags/pt.svg" alt="Portuguese" class="flag-icon">
                                    <p>Portuguese</p>
                                </span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-md-2">
                            <a href="register.php?tolang=french">
                                <span class="flag-icon">
                                    <img src="images/flags/fr.svg" alt="French" class="flag-icon">
                                    <p>French</p>
                                </span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-md-2">
                            <a href="register.php?tolang=italian">
                                <span class="flag-icon">
                                    <img src="images/flags/it.svg" alt="Italian" class="flag-icon">
                                    <p>Italian</p>
                                </span>
                            </a>
                        </div>
                        <div class="col-xs-4 col-md-2">
                            <a href="register.php?tolang=german">
                                <span class="flag-icon">
                                    <img src="images/flags/de.svg" alt="German" class="flag-icon">
                                    <p>German</p>
                                </span>
                            </a>
                        </div>
                <!--/col -->
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /headerwrap -->

<!-- SERVICE -->
    <div id="service" class="service">
        <div class="container">
            <div class="row centered">
                <div class="col-md-4">
                    <i class="fab fa-osi"></i>
                    <h4>Open source</h4>
                    <p>Aprelendo is open source software, meaning you can download and fiddle with its source code. This allows
                        us to be super transparent and build a community of users and developers around Aprelendo. With some
                        technical knowledge you can even build your own Aprelendo environment. This can be particularly useful
                        for schools and other educational organizations.</p>
                    <p>
                        <br/>
                        <a href="https://github.com/usemoslinux/aprelendo" target="_blank" class="btn btn-theme">More Info</a>
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
                        <a href="activereading.php" class="btn btn-theme">More Info</a>
                    </p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-mobile-alt"></i>
                    <h4>Use it on any device</h4>
                    <p>You can use Aprelendo on mobile and desktop devices as long as they have an Internet connection. We offer
                        addons for Firefox and Chrome to add texts to your library with the click of a mouse. Bookmarklets
                        are also available for those who prefer a browser agnostic solution.</p>
                    <p>
                        <br/>
                        <a href="extensions.php" class="btn btn-theme">More Info</a>
                    </p>
                </div>
            </div>
            <!--/row -->
        </div>
        <!--/container -->
    </div>
    <!--/service -->

    <!-- HOW IT WORKS -->
    <div class="hiw">
        <div class="container">
            <div class="row">
                <h1><a id="hiw">How it works</a></h1>
                <div class="col-lg-6">
                    <img class="img-responsive" src="images/backgrounds/01-pattern-wallpaper.png" alt="How it works">
                </div>
                <div class="col-lg-6">
                    <ol>
                        <li>Add texts and videos to your Aprelendo personal library using our craft-made extensions. You can also upload youtube videos and ebooks.</li>
                        <li>Read your texts (or transcripts) and look up words you don't know.</li>
                        <li>Next time, they will appear underlined, indicating you are learning these words. After seeing them a
                            couple times without looking for their meaning, the underlining will disappear.</li>
                    </ol>

                </div>
            </div>
        </div>
    </div>

    <!-- TESTIMONIALS -->
    <div class="testimonials">
        <div id="testimonials" class="container">
            <div class="row">
                <h1><a id="testimonials">What are people saying?</a></h1>
                <div class="left col-lg-6">
                    <blockquote class="quote">
                        <p>
                            Aprelendo is probably my top resource now for learning new languages.
                        </p>
                        <br>
                        <footer>Alex Rawlings - Language Teacher</footer>
                    </blockquote>
                </div>
                <div class="right col-lg-6">
                    <blockquote class="quote">
                        <p>
                            Aprelendo is one of the best language learning tools I've ever used. It is especially good for helping you get past the beginner
                            levels of a language and into the intermediate and advanced stages.
                        </p>
                        <br>
                        <footer>Ron Gullekson - Language Surfer</footer>
                    </blockquote>
                </div>
                <div class="left col-lg-6">
                    <blockquote class="quote">
                        <p>
                            It is difficult to give something that is so young a five star rating, but this is already one of the best sites (along with
                            stuff like Anki, Memrise, "X"Pod, Duolingo, LingQ, etc.) around for this and it is only getting
                            better.
                        </p>
                        <footer>Joseph Heavner - Quora </footer>
                    </blockquote>
                </div>
                <div class="right col-lg-6">
                    <blockquote class="quote">
                        <p>
                            This is one of the most fantastic language learning tools I have ever encountered.
                        </p>
                        <footer>Foreigner on German Soil </footer>
                    </blockquote>
                </div>
            </div>
            <!-- row -->
        </div>
        <!-- container -->
    </div>
    <!-- testimonials -->

    <!-- PRICING -->
    <div class="pricing">
        <h1><a id="pricing">Pricing</a></h1>
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-md-offset-2 col-sm-6">
                    <div class="pricingTable">
                        <span class="icon">
                            <i class="fa fa-globe"></i>
                        </span>
                        <div class="pricingTable-header">
                            <h3 class="title">Free</h3>
                            <span class="price-value">Free</span>
                        </div>
                        <ul class="pricing-content">
                            <li>Unlimited texts</li>
                            <li>Unlimited videos</li>
                            <li>Unlimited words</li>
                            <li>Web browser Extensions</li>
                            <li>Audio attachments (&lt;2 MB)</li>
                            <li><del>RSS support</del></li>
                            <li><del>Ebook support</del></li>
                            <li><del>Export words</del></li>
                        </ul>
                        <a href="#" class="pricingTable-signup">Sign Up</a>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="pricingTable">
                        <span class="icon">
                            <i class="fa fa-briefcase"></i>
                        </span>
                        <div class="pricingTable-header">
                            <h3 class="title">Premium</h3>
                            <span class="price-value">$5/month</span>
                        </div>
                        <ul class="pricing-content">
                            <li>Unlimited texts</li>
                            <li>Unlimited videos</li>
                            <li>Unlimited words</li>
                            <li>Web browser Extensions</li>
                            <li>Audio attachments (&lt;10 MB)</li>
                            <li>Up to 3 RSS feeds</li>
                            <li>Ebook support (&lt;2 MB)</li>
                            <li>Export words (CSV)</li>
                        </ul>
                        <a href="#" class="pricingTable-signup">Sign Up</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="js/register.js"></script>
    <?php require_once 'footer.php'?>
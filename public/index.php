<?php
require_once '../includes/dbinit.php';

use Aprelendo\Includes\Classes\User;

$user = new User($con);

// if user is already logged in, go to "My Texts" section
if ($user->isLoggedIn()) {
    header('Location:texts.php');
    exit;
}
?>

<?php require_once 'simpleheader.php';?>

<!-- HEADERWRAP -->
<div id="headerwrap" class="headerwrap landing-page-background">
    <div class="blurry-background">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h1>Learn foreign languages<br />by reading<br /> your favorite texts</h1>
                    <h5>Want to know more about our method? We call it
                        <a href="totalreading.php">
                            <u>total reading.</u>
                        </a></h5>
                    <br />
                    <h4>Select the language you want to learn to create an account</h4>
                    <br />
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-4 col-lg-2">
                    <a href="register.php?tolang=english">
                        <span class="flag-icon">
                            <img src="img/flags/en.svg" alt="English" class="flag-icon">
                            <p>English</p>
                        </span>
                    </a>
                </div>
                <div class="col-sm-4 col-lg-2">
                    <a href="register.php?tolang=spanish">
                        <span class="flag-icon">
                            <img src="img/flags/es.svg" alt="Spanish" class="flag-icon">
                            <p>Spanish</p>
                        </span>
                    </a>
                </div>
                <div class="col-sm-4 col-lg-2">
                    <a href="register.php?tolang=portuguese">
                        <span class="flag-icon">
                            <img src="img/flags/pt.svg" alt="Portuguese" class="flag-icon">
                            <p>Portuguese</p>
                        </span>
                    </a>
                </div>
                <div class="col-sm-4 col-lg-2">
                    <a href="register.php?tolang=french">
                        <span class="flag-icon">
                            <img src="img/flags/fr.svg" alt="French" class="flag-icon">
                            <p>French</p>
                        </span>
                    </a>
                </div>
                <div class="col-sm-4 col-lg-2">
                    <a href="register.php?tolang=italian">
                        <span class="flag-icon">
                            <img src="img/flags/it.svg" alt="Italian" class="flag-icon">
                            <p>Italian</p>
                        </span>
                    </a>
                </div>
                <div class="col-sm-4 col-lg-2">
                    <a href="register.php?tolang=german">
                        <span class="flag-icon">
                            <img src="img/flags/de.svg" alt="German" class="flag-icon">
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

</div>
<!-- /headerwrap -->

<!-- SERVICE -->
<div id="service" class="service">
    <div class="container">
        <div class="row centered">
            <div class="col-lg-4">
                <i class="fab fa-osi"></i>
                <h4>Open source</h4>
                <p>Aprelendo is open source software, meaning you can download and fiddle with its source code. This
                    allows us to be super transparent and build a community of users and developers. With some
                    technical knowledge, you can even build your own Aprelendo environment.</p>
                <p>
                    <br />
                    <a href="https://github.com/usemoslinux/aprelendo" target="_blank" rel="noopener noreferrer" class="btn btn-theme">More
                        Info</a>
                </p>
            </div>
            <div class="col-lg-4">
                <i class="fas fa-book-open"></i>
                <h4>Total Reading</h4>
                <p>Immersion is the best way to learn a language, but few -if any- online tools allow you to create an
                    immersive environement. Total reading is a system developed to achieve this goal, from the comfort
                    of your home and with enough flexibility to adapt to your schedules and needs.</p>
                <p>
                    <br />
                    <a href="totalreading.php" class="btn btn-theme">More Info</a>
                </p>
            </div>
            <div class="col-lg-4">
                <i class="fas fa-mobile-alt"></i>
                <h4>Use it on any device</h4>
                <p>You can use Aprelendo on mobile and desktop devices as long as they have an Internet connection. We
                    offer
                    addons for Firefox and Chrome to add texts to your library with the click of a mouse. Bookmarklets
                    are also available for those who prefer a browser agnostic solution.</p>
                <p>
                    <br />
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
            <h1 id="hiw" class="col-12">How it works</h1>
            <div class="col-xl-6">
                <img class="img-fluid" src="img/backgrounds/pattern-wallpaper.png" alt="How it works">
            </div>
            <div class="col-xl-6">
                <ol>
                    <li>Add texts to your Aprelendo personal library using our extensions. Premium users can also
                        upload Youtube videos and ebooks.</li>
                    <li>Read your texts (or transcripts) and look up words you don't know.</li>
                    <li>Whenever you encounter these words in other texts they will appear underlined, indicating you
                        are still learning them. After a couple times without looking for their meaning, the
                        underlining will disappear.</li>
                </ol>

            </div>
        </div>
    </div>
</div>

<!-- TESTIMONIALS -->
<div class="testimonials">
    <div id="testimonials" class="container">
        <div class="row">
            <h1 id="testimonials" class="col-12">What are people saying?</h1>
            <div class="left col-xl-6">
                <blockquote class="quote">
                    <p>
                        Aprelendo is probably my top resource now for learning new languages.
                    </p>
                    <br />
                    <footer>Alex Rawlings - Language Teacher</footer>
                </blockquote>
            </div>
            <div class="right col-xl-6">
                <blockquote class="quote">
                    <p>
                        Aprelendo is one of the best language learning tools I've ever used. It is especially good for
                        helping you get past the beginner
                        levels of a language and into the intermediate and advanced stages.
                    </p>
                    <br />
                    <footer>Ron Gullekson - Language Surfer</footer>
                </blockquote>
            </div>
            <div class="left col-xl-6">
                <blockquote class="quote">
                    <p>
                        It is difficult to give something that is so young a five star rating, but this is already one
                        of the best sites (along with
                        stuff like Anki, Memrise, "X"Pod, Duolingo, LingQ, etc.) around for this and it is only getting
                        better.
                    </p>
                    <footer>Joseph Heavner - Quora </footer>
                </blockquote>
            </div>
            <div class="right col-xl-6">
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
<section class="pricing py-5">
    <div class="container">
        <div class="row">
            <!-- Free Tier -->
            <div class="col-lg-4">
                <div class="card mb-5 mb-lg-0">
                    <div class="card-body">
                        <h5 class="card-title text-muted text-uppercase text-center">Free</h5>
                        <h6 class="card-price text-center">$0<span class="period">/month</span></h6>
                        <hr>
                        <ul class="fa-ul">
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited texts</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited videos</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited words</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Web browser extensions</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>TTS support
                                (1 text p/day)</li>
                            <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Up to 3 RSS
                                feeds</li>
                            <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Ebook support
                                (&lt;2 MB)</li>
                            <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Export words
                                (CSV)</li>
                        </ul>
                        <a href="#" class="btn btn-block btn-primary text-uppercase">Sign up</a>
                    </div>
                </div>
            </div>
            <!-- Plus Tier -->
            <div class="col-lg-4">
                <div class="card mb-5 mb-lg-0">
                    <div class="card-body">
                        <h5 class="card-title text-muted text-uppercase text-center">Premium</h5>
                        <h6 class="card-price text-center">$10<span class="period">/month</span></h6>
                        <hr>
                        <ul class="fa-ul">
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited texts</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited videos</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited words</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Web browser extensions</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>TTS support (3 texts p/day)</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Up to 3 RSS feeds</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Ebook support (&lt;2 MB)</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Export words (CSV)</li>
                        </ul>
                        <a href="#" class="btn btn-block btn-primary text-uppercase">Sign up</a>
                    </div>
                </div>
            </div>
            <!-- Pro Tier -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-muted text-uppercase text-center">Premium</h5>
                        <h6 class="card-price text-center">$90<span class="period">/year</span></h6>
                        <hr>
                        <ul class="fa-ul">
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited texts</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited videos</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Unlimited words</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Web browser extensions</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>TTS support (3 texts p/day)</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Up to 3 RSS feeds</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Ebook support (&lt;2 MB)</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Export words (CSV)</li>
                        </ul>
                        <a href="#" class="btn btn-block btn-primary text-uppercase">Sign up</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script src="js/register.js"></script>
<?php require_once 'footer.php'?>
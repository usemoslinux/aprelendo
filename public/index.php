<?php
/**
 * Copyright (C) 2019 Pablo Castagnino
 *
 * This file is part of aprelendo.
 *
 * aprelendo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * aprelendo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with aprelendo.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once '../Includes/dbinit.php';

use Aprelendo\User;
use Aprelendo\UserAuth;

$user = new User($pdo);
$user_auth = new UserAuth($user);

// if user is already logged in, go to "My Texts" section
if ($user_auth->isLoggedIn()) {
    header('Location:texts');
    exit;
}

require_once PUBLIC_PATH . 'head.php';
?>

<main>
    <!-- HEADERWRAP -->
    <section>
        <div id="headerwrap" class="headerwrap">
            <div class="blurry-background">
                <?php require_once PUBLIC_PATH . 'simpleheader.php'; ?>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12">
                            <h1>Learn a new language<br>one story at a time</h1>
                            <h5>Want to know more about our method?<br>
                            It&#39;s called
                                <a href="/totalreading">
                                    <u>total reading</u>
                                </a></h5>
                            <br>
                            
                        </div>
                    </div>
                </div>
                <div class="container-fluid mb-5">
                    <div class="row">
                        <div class="col">
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    I want to learn
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                    <a class="dropdown-item" href="/register?tolang=arabic" type="button">
                                        <img src="img/flags/ar.svg" alt="Arabic" class="flag-icon">
                                        &nbsp;Arabic
                                    </a>
                                    <a class="dropdown-item" href="/register?tolang=chinese" type="button">
                                        <img src="img/flags/zh.svg" alt="Chinese" class="flag-icon">
                                        &nbsp;Chinese
                                    </a>
                                    <a class="dropdown-item" href="/register?tolang=dutch" type="button">
                                        <img src="img/flags/nl.svg" alt="Dutch" class="flag-icon">
                                        &nbsp;Dutch
                                    </a>
                                    <a class="dropdown-item" href="/register?tolang=english" type="button">
                                        <img src="img/flags/en.svg" alt="English" class="flag-icon">
                                        &nbsp;English
                                    </a>
                                    <a class="dropdown-item" href="/register?tolang=french" type="button">
                                        <img src="img/flags/fr.svg" alt="French" class="flag-icon">
                                        &nbsp;French
                                    </a>
                                    <a class="dropdown-item" href="/register?tolang=german" type="button">
                                        <img src="img/flags/de.svg" alt="German" class="flag-icon">
                                        &nbsp;German
                                    </a>
                                    <a class="dropdown-item" href="/register?tolang=greek" type="button">
                                        <img src="img/flags/el.svg" alt="Greek" class="flag-icon">
                                        &nbsp;Greek
                                    </a>
                                    <a class="dropdown-item" href="/register?tolang=hebrew" type="button">
                                        <img src="img/flags/he.svg" alt="Hebrew" class="flag-icon">
                                        &nbsp;Hebrew
                                    </a>
                                    <a class="dropdown-item" href="/register?tolang=hindi" type="button">
                                        <img src="img/flags/hi.svg" alt="Hindi" class="flag-icon">
                                        &nbsp;Hindi
                                    </a>
                                    <a class="dropdown-item" href="/register?tolang=italian" type="button">
                                        <img src="img/flags/it.svg" alt="Italian" class="flag-icon">
                                        &nbsp;Italian
                                    </a>
                                    <a class="dropdown-item" href="/register?tolang=japanese" type="button">
                                        <img src="img/flags/ja.svg" alt="Japanese" class="flag-icon">
                                        &nbsp;Japanese
                                    </a>
                                    <a class="dropdown-item" href="/register?tolang=korean" type="button">
                                        <img src="img/flags/ko.svg" alt="Korean" class="flag-icon">
                                        &nbsp;Korean
                                    </a>
                                    <a class="dropdown-item" href="/register?tolang=portuguese" type="button">
                                        <img src="img/flags/pt.svg" alt="Portuguese" class="flag-icon">
                                        &nbsp;Portuguese
                                    </a>
                                    <a class="dropdown-item" href="/register?tolang=russian" type="button">
                                        <img src="img/flags/ru.svg" alt="Russian" class="flag-icon">
                                        &nbsp;Russian
                                    </a>
                                    <a class="dropdown-item" href="/register?tolang=spanish" type="button">
                                        <img src="img/flags/es.svg" alt="Spanish" class="flag-icon">
                                        &nbsp;Spanish
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section>
        <div class="hiw pt-5">
            <div class="container">
                <div class="row text-center">
                    <h2 id="hiw" class="w-100 mb-5">How it works</h2>
                    <div class="col-lg-4">
                        <h4>1. Add</h4>
                        <p>Add texts & YouTube videos to your Aprelendo library using our <a href="/extensions"
                                    target="_blank" rel="noopener noreferrer">extensions</a>. You can can also upload
                                    ebooks and texts from RSS feeds.</p>
                    </div>
                    <div class="col-lg-4">
                        <h4>2. Read</h4>
                        <p>Read your texts and video transcripts in our clutter-free reader. Look up words
                            you don't know in your favorite dictionaries and get cues of the learning status
                            of each word.</p>
                    </div>
                    <div class="col-lg-4">
                        <h4>3. Learn</h4>
                        <p>Our assisted learning method will help you to improve not only your reading comprehension
                                but also your listening, speaking and writing skills. Check
                                more on this on our <a href="/totalreading" target="_blank"
                                rel="noopener noreferrer">total reading</a> section.</p>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-12">
                        <div class="video-container">
                            <iframe src="https://www.youtube.com/embed/qimkPHrLkS4" allowfullscreen
                                title="How it works video" class="video"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN-FEATURES -->
    <section>
        <div id="main-features" class="main-features pb-5">
            <div class="container">
                <div class="row text-center">
                    <div class="col-lg-4 pt-5">
                        <span class="bi bi-code-slash display-1 pb-3"></span>
                        <h4>Open source</h4>
                        <p>Aprelendo is open source software, meaning you can download and fiddle
                            with its source code. This allows us to be super transparent and build
                            a community of users and developers. With some technical knowledge, you
                            can even build your own Aprelendo environment.</p>
                        <p>
                            <a href="https://github.com/usemoslinux/aprelendo" target="_blank" rel="noopener noreferrer"
                                class="btn btn-theme">More
                                Info</a>
                        </p>
                    </div>
                    <div class="col-lg-4 pt-5">
                        <span class="bi bi-book display-1 pb-3"></span>
                        <h4>Total Reading</h4>
                        <p>Our language learning system is aimed especially (though not exclusively)
                            at users with a beginner-intermediate level upwards. Complete beginners
                            may encounter difficulties in this system, but it is very beneficial
                            for those who are on a learning "plateau" or anyone seeking to improve
                            their language skills.</p>
                        <p>
                            <a href="/totalreading" class="btn btn-theme">More Info</a>
                        </p>
                    </div>
                    <div class="col-lg-4 pt-5">
                        <span class="bi bi-phone display-1 pb-3"></span>
                        <h4>Use it on any device</h4>
                        <p>You can use Aprelendo on mobile and desktop devices as long as they have
                            an Internet connection. We offer addons for Firefox, Chrome and Edge to easily
                            add texts to your library, as well as bookmarklets for those who prefer
                            a browser agnostic solution.</p>
                        <p>
                            <a href="/extensions" class="btn btn-theme">More Info</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'footer.php'?>

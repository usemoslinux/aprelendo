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
 * along with aprelendo.  If not, see <https://www.gnu.org/licenses/>.
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

<main class="cursive-titles">
    <!-- HEADERWRAP -->
    <section>
        <div id="headerwrap" class="headerwrap">
            <div class="blurry-background">
                <?php require_once PUBLIC_PATH . 'simpleheader.php'; ?>
                <div class="container mb-auto">
                    <div class="row">
                        <div class="col-sm-12">
                            <h1 class="display-4 py-5" style="line-height: 2em;">
                                Learn a new language<br>one story at a time
                            </h1>
                            <h4 class="py-md-5">Want to know more about our method?</h4><br>
                            <h4 class="py-md-5">It's called
                                <a href="/totalreading" class="text-warning">
                                    <u>total reading</u>
                                </a>
                            </h4>
                            <br>
                        </div>
                    </div>
                </div>
                <div class="container mb-5">
                    <div class="row justify-content-center">
                        <div class="col col-md-8">
                            <div id="languages-card" class="card faded-background">
                                <div class="card-body">
                                    <h4 class="card-title">I want to learn</h4>
                                    <div class="d-flex flex-wrap justify-content-center">
                                        <a href="/register?tolang=arabic" class="btn btn-secondary m-1 language-pill">
                                            <img src="img/flags/ar.svg" alt="Arabic" class="flag-icon">
                                            &nbsp;Arabic
                                        </a>
                                        <a href="/register?tolang=chinese" class="btn btn-secondary m-1 language-pill">
                                            <img src="img/flags/zh.svg" alt="Chinese" class="flag-icon">
                                            &nbsp;Chinese
                                        </a>
                                        <a href="/register?tolang=english" class="btn btn-secondary m-1 language-pill">
                                            <img src="img/flags/en.svg" alt="English" class="flag-icon">
                                            &nbsp;English
                                        </a>
                                        <a href="/register?tolang=french" class="btn btn-secondary m-1 language-pill">
                                            <img src="img/flags/fr.svg" alt="French" class="flag-icon">
                                            &nbsp;French
                                        </a>
                                        <a href="/register?tolang=german" class="btn btn-secondary m-1 language-pill">
                                            <img src="img/flags/de.svg" alt="German" class="flag-icon">
                                            &nbsp;German
                                        </a>
                                        <a href="/register?tolang=italian" class="btn btn-secondary m-1 language-pill">
                                            <img src="img/flags/it.svg" alt="Italian" class="flag-icon">
                                            &nbsp;Italian
                                        </a>
                                        <a href="/register?tolang=japanese" class="btn btn-secondary m-1 language-pill">
                                            <img src="img/flags/ja.svg" alt="Japanese" class="flag-icon">
                                            &nbsp;Japanese
                                        </a>
                                        <a href="/register?tolang=korean" class="btn btn-secondary m-1 language-pill">
                                            <img src="img/flags/ko.svg" alt="Korean" class="flag-icon">
                                            &nbsp;Korean
                                        </a>
                                        <a href="/register?tolang=portuguese" class="btn btn-secondary m-1 language-pill">
                                            <img src="img/flags/pt.svg" alt="Portuguese" class="flag-icon">
                                            &nbsp;Portuguese
                                        </a>
                                        <a href="/register?tolang=russian" class="btn btn-secondary m-1 language-pill">
                                            <img src="img/flags/ru.svg" alt="Russian" class="flag-icon">
                                            &nbsp;Russian
                                        </a>
                                        <a href="/register?tolang=spanish" class="btn btn-secondary m-1 language-pill">
                                            <img src="img/flags/es.svg" alt="Spanish" class="flag-icon">
                                            &nbsp;Spanish
                                        </a>
                                        <a href="/register" class="btn btn-secondary m-1 language-pill">
                                            <img src="img/flags/un.svg" alt="Spanish" class="flag-icon">
                                            &nbsp;+19 more
                                        </a>
                                    </div>
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
                        <p>Add texts & YouTube videos to your Aprelendo library using our <a href="/extensions" target="_blank" rel="noopener noreferrer">extensions</a>. You can can also upload
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
                            more on this on our <a href="/totalreading" target="_blank" rel="noopener noreferrer">total reading</a> section.</p>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-12">
                        <div class="video-container">
                            <iframe src="https://www.youtube-nocookie.com/embed/AmRq3tNFu9I" allowfullscreen title="How it works video" class="video"></iframe>
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
                        <a href="https://github.com/usemoslinux/aprelendo" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-dark">
                            <div class="card h-100">
                                <div class="card-body">
                                    <span class="bi bi-code-slash display-1"></span>
                                    <h4 class="card-title mt-3 mb-5">Open Source</h4>
                                    <p class="card-text">Aprelendo is free & open source software, meaning you can
                                        download and fiddle with its source code. This allows us to be super
                                        transparent and build a community of users and developers. With some technical
                                        knowledge, you can even build your own Aprelendo environment.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 pt-5">
                        <a href="/totalreading" class="text-decoration-none text-dark">
                            <div class="card h-100">
                                <div class="card-body">
                                    <span class="bi bi-book display-1"></span>
                                    <h4 class="card-title mt-3 mb-5">Total Reading</h4>
                                    <p class="card-text">Our language learning system is aimed especially (though not exclusively)
                                        at users with a beginner-intermediate level upwards. Complete beginners
                                        may encounter difficulties in this system, but it is very beneficial
                                        for those who are on a learning "plateau" or anyone seeking to improve
                                        their language skills.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 pt-5">
                        <a href="/extensions" class="text-decoration-none text-dark">
                            <div class="card h-100">
                                <div class="card-body">
                                    <span class="bi bi-phone display-1"></span>
                                    <h4 class="card-title mt-3 mb-5">Use it on any device</h4>
                                    <p class="card-text">You can use Aprelendo on mobile and desktop devices as long as they have
                                        an Internet connection. We offer addons for Firefox, Chrome and Edge to easily
                                        add texts to your library, as well as bookmarklets for those who prefer
                                        a browser agnostic solution.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'footer.php' ?>

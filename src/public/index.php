<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../Includes/dbinit.php';

use Aprelendo\User;
use Aprelendo\UserAuth;

$user = new User($pdo);
$user_auth = new UserAuth($user);

// if user is already logged in, go to "My Texts" section
if ($user_auth->isLoggedIn()) {
    header('Location:/texts');
    exit;
}

require_once PUBLIC_PATH . 'head.php';
?>

<main class="landing-page">
    <!-- HERO -->
    <section class="landing-hero">
        <div id="headerwrap" class="headerwrap">
            <div class="blurry-background">
                <?php require_once PUBLIC_PATH . 'simpleheader.php'; ?>

                <div class="container landing-hero-content d-flex justify-content-center py-4 py-lg-5">
                    <div class="row justify-content-center w-100">
                        <div class="col-12 col-xl-9">
                            <div class="landing-hero-copy text-center">
                                <p class="landing-eyebrow mb-3">100% free and open source</p>
                                <h1 class="landing-title display-4 mb-4">
                                    Learn languages through ebooks, videos and real content
                                </h1>
                                <p class="landing-lead mb-4">
                                    Aprelendo helps you turn what you read and watch into vocabulary you can
                                    understand, review, and actually use.
                                </p>
                                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center mb-4">
                                    <a href="/register" class="btn btn-light btn-lg landing-primary-cta">
                                        Start now
                                    </a>
                                    <a href="#hiw" class="btn btn-outline-light btn-lg">
                                        See how it works
                                    </a>
                                </div>
                                <p class="landing-support mb-0">
                                    Especially useful for intermediate and advanced learners who feel stuck on a
                                    plateau.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container landing-language-section pb-5">
                    <div class="row justify-content-center">
                        <div class="col-12 col-xl-10">
                            <div id="languages-card" class="card faded-background landing-language-card">
                                <div class="card-body">
                                    <div class="row align-items-center g-4">
                                        <div class="col-lg-4 text-center text-lg-start">
                                            <h2 class="h4 card-title mb-2">Choose your target language</h2>
                                            <p class="mb-0">
                                                Start with one of our most popular languages or browse the full list
                                                during signup.
                                            </p>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="d-flex flex-wrap justify-content-center justify-content-lg-end">
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
                                                    <img src="img/flags/un.svg" alt="All languages" class="flag-icon">
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
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section id="hiw" class="landing-section hiw" aria-labelledby="landing-how-it-works-title">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="text-center mb-5">
                        <p class="landing-section-label mb-2">How it works</p>
                        <h2 id="landing-how-it-works-title" class="landing-section-title">
                            From ebooks and videos to active vocabulary
                        </h2>
                        <p class="landing-section-intro">
                            Aprelendo works best when one piece of content becomes several kinds of practice. Start
                            with an ebook, a YouTube video, or a web text, then keep revisiting the same vocabulary in
                            ways that fit your time and level.
                        </p>
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100 landing-step-card border-0">
                                <div class="card-body p-4">
                                    <div class="landing-step-icon">
                                        <span class="bi bi-book-half" aria-hidden="true"></span>
                                    </div>
                                    <p class="landing-step-number mb-2">Step 1</p>
                                    <h3 class="h5 mb-3">Start with real material</h3>
                                    <p class="mb-0">
                                        Import ebooks, YouTube videos, and web texts into your library, including quick
                                        capture from the web with our extensions. Ebooks and videos are often the
                                        easiest way to build a steady habit.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100 landing-step-card border-0">
                                <div class="card-body p-4">
                                    <div class="landing-step-icon">
                                        <span class="bi bi-book" aria-hidden="true"></span>
                                    </div>
                                    <p class="landing-step-number mb-2">Step 2</p>
                                    <h3 class="h5 mb-3">Understand words in context</h3>
                                    <p class="mb-0">
                                        Read or watch in a focused reader, look up unknown words, and keep them tied to
                                        the sentence where you found them. You can also ask AI for extra help when
                                        needed.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100 landing-step-card border-0">
                                <div class="card-body p-4">
                                    <div class="landing-step-icon">
                                        <span class="bi bi-card-text" aria-hidden="true"></span>
                                    </div>
                                    <p class="landing-step-number mb-2">Step 3</p>
                                    <h3 class="h5 mb-3">Keep going with cards</h3>
                                    <p class="mb-0">
                                        On busy days, study saved vocabulary through classic flashcards or cloze cards
                                        instead of doing a full reading session.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100 landing-step-card border-0">
                                <div class="card-body p-4">
                                    <div class="landing-step-icon">
                                        <span class="bi bi-stars" aria-hidden="true"></span>
                                    </div>
                                    <p class="landing-step-number mb-2">Step 4</p>
                                    <h3 class="h5 mb-3">Push words into active use</h3>
                                    <p class="mb-0">
                                        Use listening, dictation, speaking, review, and AI writing feedback to move
                                        vocabulary from recognition to production.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card landing-video-card border-0">
                        <div class="card-body p-0">
                            <div class="row g-0 align-items-center">
                                <div class="col-lg-5 p-4 p-lg-5 text-center text-lg-start">
                                    <p class="landing-card-label mb-2">Method walkthrough</p>
                                    <h3 class="h4 mb-3">See the full workflow in action</h3>
                                    <p class="mb-4">
                                        Watch how one ebook chapter or video transcript can become reading, listening,
                                        speaking, dictation, cards, and review. If you want the deeper explanation,
                                        our method page covers the full total reading approach.
                                    </p>
                                    <a href="/totalreading" class="btn btn-outline-primary">
                                        Read the full method
                                    </a>
                                </div>
                                <div class="col-lg-7 p-4 p-lg-5">
                                    <div class="ratio ratio-16x9 landing-video-frame">
                                        <iframe src="https://www.youtube-nocookie.com/embed/AmRq3tNFu9I"
                                            allowfullscreen
                                            title="Aprelendo total reading walkthrough"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN FEATURES -->
    <section class="landing-section landing-value-section" aria-labelledby="landing-why-title">
        <div id="main-features" class="main-features">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-xl-10">
                        <div class="text-center mb-5">
                            <p class="landing-section-label mb-2">Why Aprelendo</p>
                            <h2 id="landing-why-title" class="landing-section-title">
                                Built for learners who want to keep progressing
                            </h2>
                            <p class="landing-section-intro">
                                Many language tools are made for absolute beginners. Aprelendo is especially helpful
                                once you already know the basics and need richer practice to break through a plateau.
                            </p>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6 col-xl-3">
                                <div class="card h-100 landing-feature-card border-0">
                                    <div class="card-body p-4">
                                        <div class="landing-feature-icon">
                                            <span class="bi bi-graph-up-arrow" aria-hidden="true"></span>
                                        </div>
                                        <h3 class="h5 card-title mb-3">Best after the basics</h3>
                                        <p class="card-text mb-0">
                                            Aprelendo shines when you are no longer a complete beginner and need better
                                            input, more context, and more active practice to keep improving.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="card h-100 landing-feature-card border-0">
                                    <div class="card-body p-4">
                                        <div class="landing-feature-icon">
                                            <span class="bi bi-card-checklist" aria-hidden="true"></span>
                                        </div>
                                        <h3 class="h5 card-title mb-3">Study even on busy days</h3>
                                        <p class="card-text mb-0">
                                            When you do not have time for a full ebook or video session, you can still
                                            move forward with classic flashcards or cloze review.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="card h-100 landing-feature-card border-0">
                                    <div class="card-body p-4">
                                        <div class="landing-feature-icon">
                                            <span class="bi bi-stars" aria-hidden="true"></span>
                                        </div>
                                        <h3 class="h5 card-title mb-3">AI when you want it</h3>
                                        <p class="card-text mb-0">
                                            Ask AI about what you are reading and use Lingobot writing feedback when
                                            you want more demanding practice with fuller, more natural sentences.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="card h-100 landing-feature-card border-0">
                                    <div class="card-body p-4">
                                        <div class="landing-feature-icon">
                                            <span class="bi bi-code-slash" aria-hidden="true"></span>
                                        </div>
                                        <h3 class="h5 card-title mb-3">100% free and open source</h3>
                                        <p class="card-text mb-0">
                                            There is no paid tier. Aprelendo is completely free to use, open source,
                                            and available on desktop and mobile.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card landing-cta-panel border-0 mt-5">
                            <div class="card-body p-4 p-lg-5">
                                <div class="row align-items-center g-4">
                                    <div class="col-lg-7 text-center text-lg-start">
                                        <p class="landing-card-label mb-2">Ready to begin?</p>
                                        <h3 class="h4 mb-3">Choose a language and start building active vocabulary</h3>
                                        <p class="mb-0">
                                            Aprelendo is 100% free. Start with one ebook, one video, or one text and
                                            build from there.
                                        </p>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-end">
                                            <a href="/register" class="btn btn-primary btn-lg">
                                                Start now
                                            </a>
                                            <a href="#languages-card" class="btn btn-outline-primary btn-lg">
                                                Choose a language
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'footer.php' ?>

<?php
// SPDX-License-Identifier: GPL-3.0-or-later

require_once '../Includes/dbinit.php'; // connect to database
require_once PUBLIC_PATH . 'head.php';

use Aprelendo\User;
use Aprelendo\UserAuth;

$user = new User($pdo);
$user_auth = new UserAuth($user);

if (!$user_auth->isLoggedIn()) {
    require_once PUBLIC_PATH . 'simpleheader.php';
} else {
    require_once PUBLIC_PATH . 'header.php';
}
?>

<div class="container mtb d-flex flex-grow-1 flex-column">
    <div class="row">
        <div class="col-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="active">About us</span>
                    </li>
                </ol>
            </nav>
            <main class="simple-text">
                <!-- TEAM -->
                <div class="row vertical-align-center">
                    <div class="col-sm-9">
                        <section>
                            <h4>Founder</h4>
                            <p>I'm <strong>Pablo</strong>, the creator of Aprelendo. I work as a
                                <strong>diplomat</strong>, so languages are not just a hobby for me. They are part of
                                daily professional life: reading, listening, writing, and speaking with people from
                                different countries in real situations where precision matters.</p>

                            <p>Aprelendo grew out of a practical frustration. Many language tools are excellent at
                                helping complete beginners get started, but far fewer are designed for learners who
                                already know the basics and need better input, better context, and better practice to
                                keep improving. That gap is where many learners plateau.</p>

                            <p>I built Aprelendo to make language study more useful, more realistic, and less
                                mechanical. Instead of separating vocabulary from meaningful content, the platform helps
                                you learn from the material you actually read or watch and then revisit that vocabulary
                                until it becomes easier to understand and use.</p>
                        </section>
                    </div>
                    <div class="col-sm-3 d-none d-sm-block">
                        <figure>
                            <img class="rounded-circle" src="img/avatar-pablo.webp" alt="Pablo Castagnino">
                        </figure>
                    </div>
                </div>

                <!-- ABOUT APRELENDO -->
                <br>
                <div class="row vertical-align-center">
                    <div class="col-sm-9">
                        <section>
                            <h4>About Aprelendo</h4>
                            <p>Aprelendo is a language-learning platform built around <strong>real content</strong>.
                                You can study from ebooks, audiobooks and audio-backed texts, YouTube videos, offline
                                videos, and web content. The idea is to help you discover vocabulary in context first,
                                then keep practicing it in ways that fit your schedule.</p>

                            <p>That is why Aprelendo includes both immersive reading-based tools and lighter review
                                options. You can save words while reading or watching, then continue with flashcards or
                                cloze review when you do not have time for a full session. If you want a deeper workflow,
                                you can also use <a href="/totalreading">total reading</a>, an optional assisted mode
                                that combines reading, listening, speaking, dictation, and review.</p>

                            <p><strong>Aprelendo is not meant to replace every other way of learning a language.</strong>
                                It works best as a complement to grammar study, conversation, classes, tutoring, or any
                                other practice that helps you build real command of the language. In particular, it is
                                especially helpful for learners who are already past the beginner stage and want to push
                                beyond the intermediate or advanced plateau.</p>

                            <br>
                            <h4>Open source and free</h4>
                            <p>Aprelendo is <strong>100% free and 100% open source</strong>. There is no paid tier, no
                                hidden advertising model, and no plan to introduce surprise fees later. The project is
                                developed openly because I want the platform to remain useful, transparent, and available
                                to learners who need it.</p>

                            <p>If Aprelendo helps you, you can support the project by <a href="/donate">making a
                                    donation</a> or by contributing on <a href="https://github.com/usemoslinux/aprelendo"
                                    target="_blank" rel="noopener noreferrer">GitHub</a>. Both are appreciated, but the
                                platform will remain free either way.</p>
                        </section>
                    </div>
                    <div class="col-sm-3 d-none d-sm-block">
                        <figure>
                            <img class="img-fluid" src="img/logo.webp" alt="Aprelendo logo">
                        </figure>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
<!--/container -->

<?php require_once 'footer.php'; ?>

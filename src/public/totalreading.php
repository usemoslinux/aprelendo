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
                        <span class="active">Total reading</span>
                    </li>
                </ol>
            </nav>
            <main class="simple-text">
                <section>
                    <h4>What total reading means</h4>
                    <p><strong>Total reading</strong> is the name we use for an optional Aprelendo workflow in
                        which one piece of content becomes several kinds of practice. Instead of reading once and
                        moving on, you return to the same material with different objectives: understanding,
                        listening, speaking, dictation, and review.</p>

                    <p>The idea is not that total reading is the only correct way to learn a language. It is
                        simply one useful workflow inside Aprelendo for learners who want deeper practice from
                        the same material, especially when the goal is to move vocabulary from recognition into
                        active use.</p>
                </section>
                <br>

                <section>
                    <h4>Start from real content</h4>
                    <p>Aprelendo is built around <strong>real content</strong>: ebooks, audiobooks and
                        audio-backed texts, YouTube videos, offline videos, and web texts. For many learners,
                        these formats are easier to sustain over time than abstract drills because they connect
                        vocabulary to a story, topic, voice, or scene that already matters to them.</p>

                    <p>That context matters. When you add a word while reading or watching, you are not creating
                        a card out of thin air. You are saving vocabulary from a sentence you already saw in use.
                        This makes collection less tedious and usually makes later review easier because meaning,
                        spelling, tone, and usage are already anchored in memory.</p>
                </section>
                <br>

                <section>
                    <h4>Real content and flashcards work together</h4>
                    <p>Aprelendo does <strong>not</strong> treat flashcards as the enemy. Flashcards and cloze
                        review are useful, especially on busy days, while commuting, or whenever you do not have
                        time for a full study session. They are part of the platform because they solve a real
                        problem: helping you keep progressing when time is limited.</p>

                    <p>The difference is that Aprelendo tries to make flashcard creation less stressful and less
                        dull by letting you collect words directly from meaningful content. In that sense, total
                        reading and flashcards are complements: one gives you richer context, the other gives you
                        a lighter way to revisit what you saved.</p>
                </section>
                <br>

                <section>
                    <h4>The assisted workflow in Aprelendo</h4>
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <p>When you want more structure, Aprelendo can guide you through an assisted
                                workflow built around five phases:</p>
                            <ol>
                                <li><strong>Reading</strong>: focus first on understanding the overall meaning,
                                    then identify the words or phrases that deserve attention.</li>
                                <li><strong>Listening</strong>: revisit the same material through audio and pay
                                    attention to rhythm, pronunciation, and phrasing.</li>
                                <li><strong>Speaking</strong>: read aloud or speak along with the audio to make
                                    pronunciation and sentence patterns more familiar.</li>
                                <li><strong>Dictation</strong>: type or reconstruct difficult words as you hear
                                    them to reinforce spelling and sound.</li>
                                <li><strong>Review</strong>: return to the saved vocabulary and actively recall
                                    meaning, form, and possible use in your own sentences.</li>
                            </ol>
                            <p>This is the core idea behind total reading: one text can train several language
                                skills when you revisit it with purpose instead of consuming it only once.</p>
                        </div>
                        <div class="col-12 col-lg-6 mt-lg-3">
                            <div class="ratio ratio-16x9">
                                <iframe src="https://www.youtube.com/embed/AmRq3tNFu9I"
                                    title="YouTube video player"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media;gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </section>
                <br>

                <section>
                    <h4>Who benefits most</h4>
                    <p>Total reading is especially helpful for learners who are already past the absolute beginner
                        stage and want to break out of a plateau. Intermediate and advanced learners often need
                        more than isolated word review: they need richer input, repeated exposure, and more
                        chances to turn passive vocabulary into something they can actually use.</p>

                    <p>Beginners can still benefit, but the material should be shorter, simpler, and closer to
                        their current level. If you are just starting out, choose easier texts and combine
                        Aprelendo with grammar study, classes, or other structured support.</p>
                </section>
                <br>

                <section>
                    <h4>If this workflow is not for you</h4>
                    <p>Total reading is optional. If you prefer a lighter workflow, you can disable the assisted
                        phases and use Aprelendo in a simpler way: read or watch, save useful vocabulary, and
                        revisit it later with flashcards or cloze review.</p>

                    <p>That flexibility is intentional. Aprelendo is meant to complement your language studies,
                        not dictate a single method. Use the deeper workflow when it helps, skip it when it does
                        not, and combine it with whatever else is already working for you.</p>
                </section>
            </main>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>

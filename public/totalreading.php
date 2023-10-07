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
        <div class="col-sm-12">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/index">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="active">Total reading</span>
                    </li>
                </ol>
            </nav>
            <main>
                <div class="row flex simple-text">
                    <div class="col-sm-12">
                        <section>
                            <h4>Fluency</h4>
                            <p>Language proficiency can be grouped into three dimensions of academic literacy:
                                linguistic, cognitive, and sociocultural. In addition, there are <strong>four
                                    language domains</strong>: listening, speaking, reading, and writing. A person must
                                be knowledgable in each of these domains in order to be language proficient.</p>
                            <p>During the process of learning a second language, it may seem logical to segment your
                                efforts and even prioritize these domains. In this line of reasoning, you might be
                                tempted to think that passive domains (listening & reading) are easier than active
                                (speaking & writing) ones or, conversely, that improving your oral skills is easier
                                or even more important than mastering the written word. However, this depends on many
                                factors, like the complexity of the language and its grammar, in what context you are
                                thinking of using this language, or how much this language resembles your mother
                                tongue or some other language you've already mastered. Truth is, none of these
                                domains is more important than the other, as all are equally required to be proficient
                                in a language.</p>
                            <p>In fact, all these domains are so intrinsically interconnected that <strong>it is
                                    better to study them together</strong>. So, how do you achieve this objective?</p>
                            <br>

                            <h4>Immersion is the answer</h4>
                            <figure class="d-none d-sm-block img-fluid">
                                <img src="/img/other/immersion-banner.jpg" class="w-100" alt="Immersion banner">
                            </figure>
                            <p>Language immersion is the process of learning a language using only the target language
                                for a specified time frame. That means no native language skills are used for
                                communication of any kind. It's sink or swim, so you swim.</p>
                            <p>There are different ways to achieve language immersion: living abroad, dating a foreign
                                person, an immersive classroom setting or online learning. Aprelendo is one of the
                                latter.</p>
                            <p>Regarding <strong>online tools</strong>, there is a catch. Although many are more or
                                less immersive, they are not complete, since they focus on one of the dimensions of
                                the language you are trying to learn. For example, language learning platforms that
                                allow you to hire language teachers or speak with foreign people tend to focus on
                                improving your speaking skills. Other tools, especially those that use cards and <a
                                    href="https://en.wikipedia.org/wiki/Spaced_repetition">spaced repetition
                                    algorithms</a>, target vocabulary acquisition.</p>
                            <p>Aprelendo was designed to overcome this as well as other limitations associated with
                                flashcards and spaced repetition software. Let's analyze them in detail before delving
                                into the concept of total reading.</p>
                        </section>
                        <br>

                        <section>
                            <h4>Why using flashcards might not be a good idea to learn a new language?</h4>
                            <p>If you have ever used spaced repetition software like <a
                                    href="https://apps.ankiweb.net/">Anki</a> you probably know that creating new
                                flashcards can rapidly become a very tiresome and time consuming task and, after a
                                while, reviewing them also becomes dull and monotonous.</p>
                            <p>Also, most of these programs are not specifically designed for language learning. If they
                                are used correctly, they might help you achieving that goal and give you the impression
                                you are advancing your language skills. However, the overall results will be
                                suboptimal, to say the least.</p>
                            <p>
                                The reason for this is that flashcard programs only help you train your card deck,
                                which most probably has little bearing on the real situations you will face. Besides,
                                they are very easy to misuse. They usually do not encourage you to add context or
                                visual and phonetic cues to your cards. Dealing with different verb conjugations or
                                words with more than one meaning is also usually a problem. Of course, there are ways
                                to handle these cases, but they are often convoluted and easy to miss for the average
                                user.</p>
                            <p></p>
                        </section>
                        <br>

                        <section>
                            <h4>The benefits of reading</h4>
                            <figure class="d-none d-sm-block img-fluid">
                                <img src="/img/other/reading-banner.jpg" class="w-100" alt="Reading banner">
                            </figure>
                            <p><strong>Reading</strong> alone has some nice <strong>benefits</strong>. It allows us to
                                acquire <strong>vocabulary in context</strong>, by presenting words and phrases as they
                                are used, including grammar, spelling, inflections, etc. Also, the importance of
                                commonly used words or phrases is more evident while we read them.
                            <p>
                            <p>These benefits are enhaced whenever we are interested in the topic of the text.
                                In that case, the context of the words or phrases we are trying to learn becomes
                                <strong>more relevant and memorable</strong>, therefore facilitating vocabulary
                                acquisition. Learning vocabulary that we know we will often want to use
                                ourselves creates a hidden need to incorporate it in our long term memory.
                            </p>
                            <p>Reading has one final advantage: we already do it a lot every day as we surf the
                                Web. We only need to take advantage of this to learn new languages.</p>
                        </section>
                        <br>
                        <section>
                            <h4>7 steps to practicing total reading</h4>
                            <p>Reading alone would only cover one of the four dimensions mentioned above. In order to
                                practice "total reading", you should follow these steps:</p>
                            <ol>
                                <li>Read <strong>short texts</strong> (the length of a newspaper article or a short
                                    story, not a book). The less proficient you are in the language, the shorter the
                                    text. Also, make sure it is at <strong>your level or slightly above it</strong>.
                                </li>
                                <li>Start by focusing on <strong>understanding</strong> the general meaning of the text,
                                    then its parts (paragraphs, phrases and specific words)
                                </li>
                                <li><strong>Search</strong> the meaning of words and phrases you don't understand and
                                    add them to your learning stack
                                </li>
                                <li><strong>Highlight</strong> these words every time you encounter them, as a way to
                                    check if you understand their meaning in each particular context.
                                </li>
                                <li><strong>Listen</strong> to an audio version of the text and pay attention to
                                    pronunciation.
                                </li>
                                <li>Read the text out loud, trying to imitate the recording that is being played.
                                    Training mouth muscles is key to achieving a good accent. A natural and stress-free
                                    way of improving speaking skills is by repeating someone else's words without
                                    thinking what is the correct way to say this or that, or how the phrase should be
                                    constructed. Eventually, it will become second nature.
                                </li>
                                <li>Finally, in the <strong>dictation phase</strong>, play the audio again and try to
                                    write the words that you had previously marked as difficult.
                                </li>
                            </ol>
                        </section>
                        <br>
                        <section>
                            <h4>How does Aprelendo implement total reading?</h4>
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <p>Aprelendo allows reading texts in <strong>two modes</strong>: free and
                                        assisted. <strong>Free mode</strong> lets you read texts however you
                                        like. <strong>Assisted mode</strong>, on the other hand, leads you through
                                        5 phases:</p>
                                    <ol>
                                        <li><strong>Reading</strong>: try to understand what the text is about. If you
                                            see words or phrases that you don&#39;t understand, look them up in the
                                            built-in dictionary.</li>
                                        <li><strong>Listening</strong>: listen to the -automagically created- audio
                                            version of the text and pay attention to the different sounds.</li>
                                        <li><strong>Speaking</strong>: speak on top of the recording, trying to imitate
                                            the pronunciation of each word. You can reduce the speed of the recording
                                            if necessary.</li>
                                        <li><strong>Dictation</strong>: type the words you marked for learning as
                                            they are spoken.</li>
                                        <li><strong>Review</strong>: this is the most
                                            <a href="https://en.wikipedia.org/wiki/Testing_effect" target="_blank"
                                                rel="noopener noreferrer">critical phase</a> for long-term language
                                            acquisition. Review all the underlined words. Make an effort to remember
                                            their meaning and pronunciation, while also paying attention to their
                                            spelling. Try to come up with alternative phrases in which you could use
                                            them. The latter is essential to turn your
                                            <a href="https://en.wiktionary.org/wiki/passive_vocabulary" target="_blank"
                                                rel="noopener noreferrer">passive vocabulary</a> into
                                            <a href="https://en.wiktionary.org/wiki/active_vocabulary" target="_blank"
                                                rel="noopener noreferrer">active vocabulary</a>.
                                        </li>
                                    </ol>
                                    <p>As you see, by using Aprelendo you will be practicing all four dimensions of
                                        the language you want to learn at the same time, in a systematic and
                                        integrated way.</p>
                                </div>
                                <div class="col-12 col-lg-6 mt-lg-3">
                                    <div class="ratio ratio-16x9">
                                        <iframe src="https://www.youtube.com/embed/qimkPHrLkS4"
                                            title="YouTube video player"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media;gyroscope; picture-in-picture"
                                            allowfullscreen></iframe>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <br>
                        <section>
                            <h4>Is total reading a good method for complete beginners?</h4>
                            <p>Our method has proven to be very beneficial, particularly for those who are on a learning
                                "plateau" or anyone seeking to improve their language skills. It is true that <strong>
                                    complete beginners may encounter some difficulties</strong> with this system, as
                                their
                                initial vocabulary is very limited and due to the fact that there are not many curated
                                texts adapted to their level freely available online.</p>
                            <p>In any case, a good recommendation to keep in mind is to <strong>
                                    practice using texts that are at your level (or slightly above it)</strong>. Thus,
                                if
                                you are a beginner you should try to use very short texts, with very basic
                                vocabulary. Intermediate and advanced users, on the other hand, can try using longer
                                and more complex texts.
                            <p>To alleviate the lack of curated texts, we created the "<a href="/sharedtexts">shared
                                    texts</a>" section, which allows our community to add and share texts that fit
                                different levels of learning.
                            </p>
                        </section>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<?php require_once 'footer.php';?>

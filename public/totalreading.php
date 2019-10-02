<?php 
/**
 * Copyright (C) 2018 Pablo Castagnino
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

require_once '../includes/dbinit.php'; // connect to database
require_once PUBLIC_PATH . 'head.php';

use Aprelendo\Includes\Classes\User;

$user = new User($con);

if (!$user->isLoggedIn()) {
    require_once PUBLIC_PATH . 'simpleheader.php';
} else {
    require_once PUBLIC_PATH . 'header.php';
}
?>

<div class="container mtb">
    <div class="row">
        <div class="col-sm-12">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="index.php">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a class="active">Total reading</a>
                </li>
            </ol>
            <div class="row flex simple-text">
                <div class="col-sm-12">
                    <h4>Fluency</h4>
                    <p>There are four dimensions to learning a language: listening, reading, speaking and writing.</p> 

                    <p>During the process of learning a second language, it may seem logical to segment your efforts and even prioritize these dimensions. In this line of reasoning, you might be tempted to think that passive dimensions (listening & reading) are easier than active (speaking & writing) ones, or that dominating the spoken word is easier than mastering the written word. However, this entirely depends on the language you are trying to learn and how much this language resembles your mother tongue or some other language you've already mastered. Also, it would be illogical to say that one of these dimensions is more important than the other, as all are equally required to be proficient in a language.</p>
                    <p>In fact, all these dimensions are so intrinsically interconnected that it is better to study them together. So, how do you achieve this objective?</p>
                    <br>

                    <h4>Immersion is the answer</h4>
                    <p>Language immersion is the process of learning a language using only the target language for a specified time frame. That means no native language skills are used for communication of any kind. It's sink or swim, so you swim.</p>
                    <p>There are different ways to achieve language immersion: living abroad, dating a foreign person, an immersive classroom setting or online learning. Aprelendo is one of the latter.</p>
                    <p>Regarding online tools, there is a catch. Although many are more or less immersive, they are not complete, since they focus on one of the dimensions of the language you are trying to learn. That's why you are usually compelled to combine them to get the expected results. For example, language learning platforms that allow you to hire language teachers or speak with foreign people tend to focus on improving your speaking skills. Other tools, especially those that use cards and <a href="https://en.wikipedia.org/wiki/Spaced_repetition">spaced repetition algorithms</a>, target vocabulary acquisition.</p>
                    <p>Aprelendo was designed to overcome this as well as other limitations associated with flashcards and spaced repetition software. Let's analyze them in detail before delving into the concept of total reading.</p>
                    <br>

                    <h4>Why using flash cards might not be a good idea to learn a new language?</h4>
                    <p>If you have ever used spaced repetition software like <a href="https://apps.ankiweb.net/">Anki</a> you probably know that creating new flashcards can rapidly become a very tiresome and time consuming task and, after a while, reviewing them also becomes dull and monotonous.</p>
                    <figure>
                        <blockquote class="blockquote float-right">
                            <p class="pt-3"><strong>Flash cards are not fun!</strong></p>
                        </blockquote>
                    </figure>
                    <p>Also, most of these programs are not specifically designed for language learning. If they are used correctly, they might help achieving that goal and give users the impression they are advancing their language skills. However, the overall results will be suboptimal, at the least. One of the reasons for this is that they are very easy to misuse. There is nothing in them oblying you to add context or visual and phonetic aids to your cards. Dealing with different verb conjugations or words with more than one meaning is also usually a problem. Of course, there are ways to handle these cases, but they are often convoluted and the average user misses them entirely.</p>
                    <br>

                    <h4>Let's try an alternative: total reading</h4>
                    <p>Reading alone has some nice benefits. It allows us to acquire vocabulary in context, by presenting words and phrases as they are used, including grammar, spelling, inflections, etc. Also, the importance of commonly used words or phrases is more evident while reading them.<p>
                    <p>These benefits are enhaced if we are interested in the subject of the text. In that case, the context of the words or phrases we are trying to learn becomes more relevant and memorable, therefore facilitating vocabulary acquisition. It can also help us learn vocabulary that we know we will often want to use ourselves, therefore creating a hidden need to incorporate it.</p>
                    <p>Reading has one final advantage: we already do it a lot as we surf the Web. We only need to take advantage of this to learn new languages. However, the biggest limitation of this strategy is that it would only cover one of the four dimensions mentioned above. To become "total", the reading process has to meet certain criteria (which usually does not):</p>
                    <figure>
                        <blockquote class="blockquote float-right">
                            <p class="pt-3"><strong>Reading is nice and fun, but total reading is a more complete learning experience</strong></p>
                        </blockquote>
                    </figure>
                    <ol>
                        <li>Texts should be short (the length of a newspaper article or a short story, not a book)</li>
                        <li>Users should be compelled to read the same text a couple of times.</li>
                        <li>Users should start focusing on understanding the general meaning of the text, then its parts (paragraphs, phrases and specific words)</li>
                        <li>Users should be allowed to easily search the meaning of words and phrases and add them to their learning stack</li>
                        <li>Words and phrases that are being learned should be highlighted, as a way to let users check if they understand their meaning in each particular context</li>
                        <li>Texts should include audio, allowing users to learn the pronunciation of words</li>
                        <li>Users should be encouraged to read the text out loud, trying to imitate the recording that is being played. Training mouth muscles is key to achieving a good accent. A natural and stress-free way of improving speaking skills is by repeating someone else's words without thinking what is the correct way to say this or that, or how the phrase should be constructed. Eventually, it will become second nature.</li>
                        <li>There should be a dictation phase in which users have to write down the words they are learning as they hear them.</li>
                    </ol>
                    <br>
                    <h4>How does Aprelendo implement total reading?</h4>
                    <p>Aprelendo allows reading texts in two modes: free and assisted. Assisted mode typically consists of 4 phases:</p>
                    <ol>
                        <li>Reading: try to understand what the text is about. If you see words or phrases that you don&#39;t understand, look them up in the built-in dictionary.</li>
                        <li>Listening: listen to the recording and pay attention to the different sounds.</li>
                        <li>Speaking: speak on top of the recording, trying to imitate the pronunciation of each word. You can reduce the speed of the recording if necessary.</li>
                        <li>Dictation: type the words you marked for learning as they are spoken.</li>
                    </ol>
                    <p>As you see, by using Aprelendo you will be practicing all four dimensions of the language you want to learn at the same time, in a systematic and integrated way.</p>
                    <br>
                    <h4>Is total reading a good method for complete beginners?</h4>
                    <p>Our method has proven to be very beneficial for those who are on a learning "plateau" or anyone seeking to improve their language skills. Having said that, complete beginners may encounter some difficulties, as their initial vocabulary is very limited and also due to the fact that there are not many curated texts adapted to their level freely available online.</p>
                    <p>In any case, a good recommendation to keep in mind is to practice using texts that are at your level (or slightly above it). Thus, if you are a beginner you should try to use very short texts, with very basic vocabulary. Intermediate and advanced users, on the other hand, can try using longer and more complex texts.
                    <p>To alleviate the lack of curated texts, we created the "shared texts" section, which allows our community to add and share texts that fit different levels of learning.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php';?>
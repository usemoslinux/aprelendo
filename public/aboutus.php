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
                    <a class="active">About us</a>
                </li>
            </ol>
        </div>
        <!-- /col -->
    </div>
    <!--/row -->

    <!-- TEAM -->
    <div class="simple-text">
        <div class="row vertical-align-center">
            <div class="col-sm-9">
                <h4>Team</h4>
                <p>Hey guys! I'm Pablo, creator of Aprelendo. I'm a diplomat and as part of my job I'm always in contact with people from other countries and frequently need to communicate in several languages.</p>
                <p>I am particularly intrigued how to become fluent in a second language. Especially as we tend to see people that are proficient in more than one language as geniuses that have a special talent for that, but if you think about it we all learned to speak our native language without problems. So, we should be able to do it a second time, right? It seems evident to me that there is something very wrong with the way foreign languages are taught at school or in other academic environments. Aprelendo is my small contribution to change that situation. Also, it allowed me to combine two of my biggest passions: computer science and languages.</p>
            </div>
            <div class="col-sm-3 d-none d-sm-block">
                <img class="rounded-circle" src="img/avatar_pablo.jpg" alt="Pablo Castagnino - Photo">
            </div>
        </div>
        <!--/row -->

        <!-- ABOUT APRELENDO -->
        <br>
        <div class="row vertical-align-center">
            <div class="col-sm-9">
                <h4>About aprelendo</h4>
                <p>Evidence shows that the best way to learn a language is through immersion. The real challenge is how to achieve that. Believe me, it's not easy. In my experience, people who become fluent in a second language usually use it at home in some way or another and have the help of a "caring and interested tutor" (either their parents or soulmate).</p>

                <p>Yet, using a second language 100% of the time can be quite difficult to achieve. For starters, not everyone has the chance to have a "caring tutor" at home. Besides, it is increasingly difficult to find one in a business or day-to-day environement. Apart from this, it can be very demotivating (especially in the initial levels) to spend the whole day using a second language, and some may see it as a waste of time (because you will take longer to do everything, even thinking). Also, depending on your proficiency level and context in which you spend your day, it may even be materially impossible to practice immersion since your colleagues and friends may not understand that language or will not have the patience to deal with you, especially if you do not share a similar level of proficiency. Living in another country might help, but it's not enough, as nowadays many people may speak English or even your native language.</p>

                <p>In a context in which the Internet has become a tool for bringing people together and disseminating information, online learning has become an increasingly popular alternative. Some language learning services allow you to contact native speakers and chat with them, at your best convenience. However, they don't offer a real immersive experience and sooner or later they end up being tiring and tremendously boring. Truth is that after a couple of sessions you no longer know what to talk about with people on the other side, not to mention that it's hard to find someone with whom you really share something. Other services focus almost entirely on vocabulary acquisition, by using of flashcards or other similar methods, like gamification.</p>
                
                <p>Aprelendo, on the other hand, is based on the idea that learning a new language by reading is easier, more engaging and -more importantly- more effective than reviewing stand-alone flashcards, as most language learning software seem to do these days. It is also based on the acceptance that even though it would be the ideal thing, it is often impossible to reach total immersion, as we have already seen. Instead, we developed a method we call <a href="totalreading.php">total reading</a>, which enables students to develop not only their reading comprehension, but their overall comprehension (reading & listening) and communication (writing & speaking) skills.</p>
                
                <p>One word of caution: our system is not magic. Your perseverance and effort will still be required to learn a new language. In that sense, Aprelendo should be seen only as a complementary method to others you may choose in your learning experience. It is even advisable that you alternate your Aprelendo sessions with grammar lessons or other approaches that let you better understand the intricacies of your target language. 
                </p>
                
                <p>In sum, Aprelendo was designed to allow you to learn a language from the comfort of your home and adapting to your schedules and interests. This, with the added benefit of improving all your levels of comprehension and communication in each session, instead of just improving your oral expression or vocabulary, as most popular language learning services do. As long as you can read at least 1 article per day using Aprelendo, we are sure you will notice the results within a few weeks. Just try it and <a href="https://www.facebook.com/aprelendo" target="_blank" rel="noopener noreferrer">let us know</a> what you think.</p>
            </div>

            <div class="col-sm-3 d-none d-sm-block">
                <img class="img-fluid" src="img/logo.svg" alt="Aprelendo logo">
            </div>
        </div>
        <!--/row -->

    </div>
</div>
<!--/container -->

<?php require_once 'footer.php';?>
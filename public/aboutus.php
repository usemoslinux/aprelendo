<?php 
require_once('../includes/dbinit.php'); // connect to database

use Aprelendo\Includes\Classes\User;

$user = new User($con);

if (!$user->isLoggedIn()) {
    require_once('simpleheader.php');
} else {
    require_once('header.php');
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
                <p>Hey guys! I'm Pablo Castagnino, creator of Aprelendo. I'm a diplomat and as part of my job I'm
                    always in contact with people from other countries and frequently need to communicate in several
                    languages. Aprelendo allowed me to combine two of my biggest passions: computer science and
                    languages.</p>
                <p>I am particularly intrigued how to become fluent in a second language. We tend to see people that
                    are proficient in more than one language as geniuses that have a special talent for that, but if
                    you think about it we all learned to speak our native language without problems. We should be able
                    to do it a second time, right? It seems evident to me that there is something very wrong with the
                    way foreign languages are taught at school or in other academic environments. Aprelendo is my small
                    contribution to change that situation.</p>
            </div>
            <div class="col-sm-3 d-none d-sm-block">
                <img class="rounded-circle" src="img/avatar_pablo.jpg" alt="Pablo Castagnino - Photo">
            </div>
        </div>
        <!--/row -->

        <!-- ABOUT APRELENDO -->

        <div class="row vertical-align-center">
            <div class="col-sm-9">
                <h4>About aprelendo</h4>
                <p>Evidence shows that the best way to learn a language is through immersion. The real challenge is how
                    to achieve that. Believe me, it's not easy. In my experience, people who become fluent in a second
                    language usually use it at home in some way or another and have the help of a "caring and
                    interested tutor" (either their parents or soulmate). Living in another country might help, but is
                    not enough, especially nowadays when many people may speak English or even your native language.
                    Also, it's difficult to find that "caring tutor" in a business environement or in the streets.</p>
                <p>An increasingly popular alternative is online learning. Some popular services allow you to contact
                    native speakers and chat with them. However, they don't offer a real immersive experience and
                    sooner or later they end up being tiring and tremendously boring. Truth is that after a couple of
                    sessions you no longer know what to talk about with people on the other side, not to mention that
                    it's hard to find someone with whom you really share something. Other services focus almost
                    entirely on vocabulary acquisition, by using of flashcards or other similar methods, like
                    gamification.</p>
                <p>Aprelendo, on the other hand, is based on the idea that learning a new language by reading is
                    easier, more engaging and -more importantly- more effective than reviewing stand alone flashcards,
                    as most language learning software seem to do these days. To achieve complete immersion in each
                    session, we developed a method we call <a href="totalreading.php">total reading</a>, which enables
                    users to develop their comprehension (reading & listening) and communication (writing & speaking)
                    skills.</p>
                <p>In sum, Aprelendo was designed to allow people to learn a language in the most immersive way
                    possible, from the comfort of their home and adapting to their schedules and interests.</p>
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
<?php 

require_once('db/dbinit.php'); // connect to database
require_once(PUBLIC_PATH . 'classes/users.php'); // load Users class

$user = new User($con);

if (!$user->isLoggedIn()) {
    require_once('simpleheader.php');
} else {
    require_once('header.php');
}
?>

<div class="container mtb">
    <div class="row">
        <div class="col-xs-12">
            <ol class="breadcrumb">
                <li>
                    <a href="index.php">Home</a>
                </li>
                <li>
                    <a class="active">Privacy policy</a>
                </li>
            </ol>
            <div class="row flex simple-text">
                <div class="col-xs-12">
                    <h4>Cookies Policy</h4>
                    <i>Last updated: August 2018</i>
                    <p>
                        <span class="bg-danger">
                            Company
                        </span> uses cookies on LangX (the "Service"). By using it, you consent to the use of cookies.</p>
                    <p>Our Cookies Policy explains what cookies are, how we use cookies, how third-parties we may partner with
                        may use cookies on the Service, your choices regarding cookies and further information about cookies.</p>
                    <br>
                    <h4>What are cookies</h4>
                    <p>Cookies are small pieces of text sent by your web browser by a website you visit. A cookie file is stored
                        in your web browser and allows the Service or a third-party to recognize you and make your next visit
                        easier and the Service more useful to you.</p>
                    <p>Cookies can be "persistent" (they expire on a specified date, usually remote in time) or "session" wise
                        (they expire when you leave a website or after a short while).</p>
                    <br>
                    <h4>How
                        <span class="bg-danger">My Company</span> uses cookies</h4>
                    <p>When you use and access the Service, we may place a number of cookies files in your web browser.</p>
                    <p>We use cookies for the following purposes: to enable auto-login ("remember me") functionality.
                        <p>Unlike most websites, we don't use cookies to provide analytics, to store your preferences, to enable
                            advertisements delivery or including behavioral advertising.</p>
                        <p>We use both session and persistent cookies on the Service and we use different types of cookies to
                            run the Service:</p>
                        <ul>
                            <li>user_token: user id to enable auto-login</li>
                            <li>accept_cookies: tells if you have accepted to use cookies</li>
                        </ul>
                        <br>
                        <h4>Third-party cookies</h4>
                        <p>We do not use third-party cookies to report usage statistics of the Service, deliver advertisements
                            on and through the Service, and so on.</p>
                        <br>
                        <h4>What are your choices regarding cookies</h4>
                        <p>If you'd like to delete cookies or instruct your web browser to delete or refuse cookies, please
                            visit the help pages of your web browser.</p>
                        <p>Please note, however, that if you delete cookies or refuse to accept them, you might not be able
                            to use all of the features we offer and some of our pages might not display properly.</p>
                        <br>
                        <h4>Where can your find more information about cookies</h4>
                        <p>You can learn more about cookies on the following third-party websites:</p>
                        <ul>
                            <li>
                                <a href="http://www.allaboutcookies.org/">AllAboutCookies</a>
                            </li>
                            <li>
                                <a href="http://www.networkadvertising.org/">Network Advertising Initiative</a>
                            </li>
                        </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php';?>
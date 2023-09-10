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

require_once '../includes/dbinit.php'; // connect to database
require_once PUBLIC_PATH . 'head.php';

use Aprelendo\Includes\Classes\User;
use Aprelendo\Includes\Classes\UserAuth;

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
                        <span class="active">Privacy policy</span>
                    </li>
                </ol>
            </nav>
            <main>
                <div class="row flex simple-text">
                    <div class="col-sm-12">
                        <section>
                            <h4>Our privacy policy</h4>
                            <em>Last updated: December 2020</em>
                            <p>Protecting the privacy of Aprelendo website users is important to us.</p>
                            <p>Our Privacy Policy is designed to inform you about the personal information we collect on
                                this website. From time to time, we may make changes to this Privacy Policy, so we
                                encourage you to check back and review it regularly to ensure you are aware of current
                                practices.</p>
                            <p>If you have additional questions or require more information about our Privacy Policy, do
                                not hesitate to <a href="/contact">contact us</a>.</p>
                            <br>
                            <h6>Personal information</h6>
                            <p>We collect some minimal information about you on this website. This information includes
                                your user name and email address, which you provide when you register to our service.
                                Additionally, we may log the IP address and web browser details of the computer or
                                device you use. In case you use your Google account to log in to Aprelendo we will get
                                your basic profile information (full name, e-mail address and profile image).</p>
                            <br>
                            <h6>Cookies</h6>
                            <p>When you use and access Aprelendo we place a minimal number of cookies on your web
                                browser.</p>
                            <p>Unlike most websites, we won't use them to provide analytics, store your preferences or
                                deliver personalized ads.</p>
                            <p>Here is a detailed description of the cookies we store on your computer:</p>
                            <ul>
                                <li>user_token: user token to enable auto-login</li>
                                <li>accept_cookies: tells us you have accepted to use cookies</li>
                                <li>hide_welcome_msg: hides welcome message, which introduces Aprelendo to new users
                                </li>
                            </ul>
                            <br>
                            <strong>Third-party cookies</strong>
                            <p>The following is a list of third party services we use that may store cookies on your
                                computer:</p>
                            <ul>
                                <li>Google sign-in</li>
                                <li>YouTube</li>
                                <li>External dictionaries</li>
                            </ul>
                            <br>
                            <strong>Deleting/blocking cookies</strong>
                            <p>Please note that if you delete cookies or refuse to accept them, you might not be able to
                                use all of the features we offer and some of our pages might not display properly.</p>
                            <br>
                            <h6>Consent</h6>
                            <p>By using our website, you hereby consent to our Privacy Policy and agree to its Terms and
                                Conditions.</p>
                        </section>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<?php require_once 'footer.php';?>

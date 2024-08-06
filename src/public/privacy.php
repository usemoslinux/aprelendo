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
                        <span class="active">Privacy Policy</span>
                    </li>
                </ol>
            </nav>
            <main>
                <div class="row flex simple-text">
                    <div class="col-sm-12">
                        <section>
                            <h4>Our Privacy Policy</h4>
                            <em>Last updated: July 2024</em>
                            <p>Protecting the privacy of our users is important to us.</p>
                            <p> This Privacy Policy explains how we collect, use, and protect your personal
                                information when you visit our website.</p>
                        </section>
                        <br>
                        <section>
                            <h6>1. What Information We Collect</h6>
                            <p>We collect the following types of information:</p>
                            <ul>
                                <li><strong>Personal information:</strong> this includes your username and email address
                                    when you register. If you log in with Google, we collect your basic profile
                                    information (full name, email address, and profile image).</li>
                                <li><strong>Technical information:</strong> this includes your IP address, browser type,
                                    and other technical details about the device you use.</li>
                            </ul>
                        </section>
                        <br>
                        <section>
                            <h6>2. How We Use Your Information</h6>
                            <p>The information we collect is used to:</p>
                            <ul>
                                <li>Provide and improve our services.</li>
                                <li>Communicate with you about your account or any issues you encounter.</li>
                                <li>Analyze how our website is used to enhance your experience.</li>
                            </ul>
                        </section>
                        <br>
                        <section>
                            <h6>3. Cookies and Tracking Technologies</h6>
                            <p>Cookies are small text files stored on your device to help us improve your experience on
                                our website. We use minimal cookies, primarily to support essential site functions such
                                as auto-login and managing your preferences.</p>
                            <p>Unlike most websites, <strong>we do not use cookies for analytics or delivering
                                personalized ads</strong>.</p>
                            <p>Here is a detailed description of the cookies we store on your computer:</p>
                            <ul>
                                <li><strong>user_token:</strong> a token to enable auto-login.</li>
                                <li><strong>accept_cookies:</strong> indicates that you have accepted the use of
                                    cookies.</li>
                                <li><strong>hide_welcome_msg:</strong> hides the welcome message that introduces
                                    Aprelendo to new users.</li>
                            </ul>
                            <br>
                            <strong>Third-party Cookies</strong>
                            <p>We use third-party services that may store cookies on your computer. These include:</p>
                            <ul>
                                <li><strong>Google Sign-In:</strong> stores cookies to manage your login session
                                    securely.</li>
                                <li><strong>YouTube:</strong> may place cookies when you watch embedded videos on our
                                    site.</li>
                                <li><strong>External Dictionaries:</strong> certain features may use cookies to enhance
                                    your experience when using external resources.</li>
                            </ul>
                            <br>
                            <strong>Deleting/blocking cookies</strong>
                            <p>You can manage your cookie preferences through your browser settings. Please note that
                                blocking cookies may affect the functionality of our site and limit your ability to use
                                certain features.</p>
                        </section>
                        <br>
                        <section>
                            <h6>4. Sharing Your Information</h6>
                            <p>We do not share your personal information with third parties.</p>
                            <p>In cases where we work with third-party services, we ensure they adhere to strict privacy
                                standards.</p>
                        </section>
                        <br>
                        <section>
                            <h6>5. Your Rights</h6>
                            <p>You have the right to access, update, or delete your personal information at any time.
                                You can do this by visiting your account settings or contacting us directly.</p>
                            <p>If you have any concerns about how your data is handled, please contact us, and we will
                                address them promptly.</p>
                        </section>
                        <br>
                        <section>
                            <h6>6. Changes to This Privacy Policy</h6>
                            <p>We may update our Privacy Policy from time to time to reflect changes in our practices or
                                for other operational, legal, or regulatory reasons.</p>
                            <p>We will notify you of any significant changes by posting the new policy on this page. We
                                encourage you to review this Privacy Policy periodically for any updates.</p>
                        </section>
                        <br>
                        <section>
                            <h6>7. Contact Us</h6>
                            <p>If you have any questions or concerns about this Privacy Policy, please do not hesitate
                                to contact us through our <a href="/contact">contact page</a>.</p>
                        </section>
                        <br>
                        <section>
                            <h6>8. Consent</h6>
                            <p>By using our website, you consent to the collection and use of your information as
                                described in this Privacy Policy. If you do not agree with this policy, please do not
                                use our services.</p>
                        </section>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
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
                        <span class="active">Privacy Policy</span>
                    </li>
                </ol>
            </nav>
            <main class="simple-text">
                <section aria-labelledby="privacy-policy-title">
                    <h1 id="privacy-policy-title" class="h4">Privacy Policy</h1>
                    <p><em><time datetime="2026-04-05">Last updated: April 5, 2026</time></em></p>
                    <p>This Privacy Policy explains what information Aprelendo collects, how we use it,
                        when it is shared, and what choices you have.</p>
                    <div class="alert alert-light border my-4">
                        <p class="mb-2"><strong>In short</strong></p>
                        <ul class="mb-0">
                            <li>We collect the account, learning, and content data needed to run Aprelendo.</li>
                            <li>Google Sign-In and Lingobot are optional features.</li>
                            <li>We do not sell your personal information.</li>
                            <li>If you delete your account, Aprelendo removes your profile data, uploaded files,
                                word list, and your private and shared texts from our servers.</li>
                        </ul>
                    </div>
                </section>
                
                <section aria-labelledby="what-we-collect">
                    <h2 id="what-we-collect" class="h6">1. What we collect</h2>
                    <ul>
                        <li><strong>Account data:</strong> your username, email address, password hash if you
                            create an email/password account, and your selected language settings.</li>
                        <li><strong>Optional Google Sign-In data:</strong> if you choose Google Sign-In, we
                            receive your Google account ID, name, and email address to authenticate your
                            account.</li>
                        <li><strong>User content:</strong> texts, files, imports, vocabulary items, study
                            activity, and related metadata that you choose to save in Aprelendo. Content kept in
                            <a href="/texts">My texts</a> stays private to your account unless you choose to share
                            it publicly.
                        </li>
                        <li><strong>Optional Lingobot data:</strong> if you choose to use Lingobot, you may save a
                            Hugging Face token in your profile. We store that token in encrypted form and use it
                            only to authenticate Lingobot requests made from your account. When you use Lingobot,
                            the prompts you submit are sent to Hugging Face to generate a reply.</li>
                        <li><strong>Technical and security data:</strong> IP address, browser or device details,
                            and similar request data may be processed for security, contact requests, password
                            reset emails, diagnostics, embedded third-party content, and analytics on the hosted
                            service.</li>
                    </ul>
                </section>
                <br>

                <section aria-labelledby="how-we-use-it">
                    <h2 id="how-we-use-it" class="h6">2. How we use it</h2>
                    <ul>
                        <li>To create, authenticate, and secure your account.</li>
                        <li>To save your reading library, vocabulary, study progress, and other learning data.</li>
                        <li>To display and moderate public content you choose to share.</li>
                        <li>To send account, security, and service-related emails such as activation and password
                            reset messages.</li>
                        <li>To provide Lingobot replies when you choose to use that feature.</li>
                        <li>To understand product usage and improve the hosted Aprelendo service. On the hosted
                            service, this may include privacy-focused analytics through
                            <a href="https://matomo.org/" target="_blank" rel="noopener noreferrer">Matomo</a>.
                        </li>
                    </ul>
                </section>
                <br>

                <section aria-labelledby="cookies-and-tracking">
                    <h2 id="cookies-and-tracking" class="h6">3. Cookies and tracking technologies</h2>
                    <p>Aprelendo uses a small number of first-party cookies to keep the site working properly and
                        to remember basic preferences.</p>
                    <ul>
                        <li><strong>user_token:</strong> keeps you signed in when auto-login is enabled.</li>
                        <li><strong>accept_cookies:</strong> remembers that you dismissed the cookie notice.</li>
                        <li><strong>hide_welcome_msg:</strong> remembers that you hid the welcome message shown to
                            new users.</li>
                    </ul>
                    <p>We do not use cookies for targeted advertising.</p>
                    <p>Some optional or embedded third-party services may also use cookies or similar
                        technologies. For example, Google Sign-In is optional, YouTube videos may use third-party
                        cookies, PayPal may use cookies during donation flows, and external assets such as Google
                        Fonts or CDN-hosted libraries may receive your IP address and browser information when a
                        page loads.</p>
                    <p>You can control or block cookies through your browser settings, but some features may stop
                        working correctly if you do.</p>
                </section>
                <br>

                <section aria-labelledby="sharing-and-third-parties">
                    <h2 id="sharing-and-third-parties" class="h6">4. Sharing and third-party services</h2>
                    <p>We do not sell your personal information. We share information only when it is needed to
                        operate features you choose to use, deliver the service, or comply with legal obligations.</p>
                    <ul>
                        <li><strong>Google:</strong> if you choose Google Sign-In, Google is involved in the
                            authentication flow.</li>
                        <li><strong>Hugging Face:</strong> if you use Lingobot, your encrypted token is decrypted on
                            our server and used to authenticate requests sent to Hugging Face together with the
                            prompts you submit.</li>
                        <li><strong>YouTube and other embedded content providers:</strong> if you load embedded or
                            linked third-party content, those providers may receive request and usage data from
                            your browser.</li>
                        <li><strong>PayPal:</strong> if you choose to donate, payment processing happens through
                            PayPal and is governed by PayPal's own terms and privacy practices.</li>
                        <li><strong>Email delivery and infrastructure providers:</strong> account and security
                            emails may pass through the mail provider configured for Aprelendo.</li>
                    </ul>
                </section>
                <br>

                <section aria-labelledby="retention-and-deletion">
                    <h2 id="retention-and-deletion" class="h6">5. Retention and deletion</h2>
                    <p>We keep your account data while your account remains active and while it is needed to
                        provide the service.</p>
                    <p>You can update your information from your account settings. You can also delete your account
                        from your profile page. Deleting your account removes your profile information, uploaded
                        files, word list, and your private and shared texts from Aprelendo's servers.</p>
                </section>
                <br>

                <section aria-labelledby="your-choices">
                    <h2 id="your-choices" class="h6">6. Your choices and rights</h2>
                    <ul>
                        <li>You can access and update your account data from your profile page.</li>
                        <li>You can choose not to use optional features such as Google Sign-In, Lingobot, or PayPal
                            donations.</li>
                        <li>You can remove or replace your Hugging Face token at any time from your profile and can
                            revoke it from your Hugging Face account.</li>
                        <li>You can manage cookie preferences in your browser.</li>
                        <li>You can contact us if you have questions about your data.</li>
                    </ul>
                </section>
                <br>

                <section aria-labelledby="privacy-changes">
                    <h2 id="privacy-changes" class="h6">7. Changes to this policy</h2>
                    <p>We may update this Privacy Policy to reflect product, operational, legal, or security
                        changes. If an update materially changes how Aprelendo handles personal data, we will post
                        the revised policy on this page and update the date above.</p>
                </section>
                <br>

                <section aria-labelledby="privacy-contact">
                    <h2 id="privacy-contact" class="h6">8. Contact us</h2>
                    <p>If you have questions or concerns about this Privacy Policy, please contact us through our
                        <a href="/contact">contact page</a>.
                    </p>
                </section>
            </main>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
